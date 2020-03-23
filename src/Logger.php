<?php

namespace LFPhp\Logger;

/**
 * Class Logger
 * @package Lite\Logger
 * @method static emergency (...$messages)
 * @method static alert (...$messages)
 * @method static critical (...$messages)
 * @method static error (...$messages)
 * @method static warning (...$messages)
 * @method static notice (...$messages)
 * @method static info (...$messages)
 * @method static debug (...$messages)
 */
class Logger {
	const DEFAULT_ID = 'default';

	/**
	 * event handler store
	 * @var array format：[[processor, collecting_level],...]
	 */
	private static $handlers = [];
	private static $log_dumps = [];
	private static $while_handlers = [];

	private $id;

	/**
	 * Logger constructor.
	 * @param $id
	 */
	public function __construct($id){
		$this->id = $id;
	}

	/**
	 * get instance
	 * @param string|null $id
	 * @return self
	 */
	public static function instance($id = ''){
		$id = $id ?: self::DEFAULT_ID;
		static $instances = [];
		if(!$instances[$id]){
			$instances[$id] = new static($id);
		}
		return $instances[$id];
	}

	/**
	 * default log for none actions
	 * @param array $messages
	 * @param int $level
	 * @return mixed|null
	 */
	protected function log($messages, $level){
		return null;
	}

	/**
	 * call as function
	 * @param mixed ...$messages
	 * @return mixed
	 */
	public function __invoke(...$messages){
		return call_user_func_array([$this, 'info'], $messages);
	}

	/**
	 * call static log method via default logger instance
	 * @param $level_method
	 * @param $messages
	 * @return mixed|null
	 * @throws \Exception
	 */
	public static function __callStatic($level_method, $messages){
		$level_method = strtoupper($level_method);
		if(!defined(LoggerLevel::class."::$level_method")){
			throw new LoggerException("Logger level no exists:".$level_method);
		}
		$level = constant(LoggerLevel::class."::$level_method");
		$ins = self::instance();
		return $ins->trigger($messages, $level);
	}

	/**
	 * call log method
	 * @param $level_method
	 * @param $messages
	 * @return mixed|null
	 * @throws \Exception
	 */
	public function __call($level_method, $messages){
		$level_method = strtoupper($level_method);
		if(!defined(LoggerLevel::class."::$level_method")){
			throw new LoggerException("Logger level no exists:".$level_method);
		}
		$level = constant(LoggerLevel::class."::$level_method");
		return $this->trigger($messages, $level);
	}

	/**
	 * @param $messages
	 * @return string
	 */
	public static function combineMessages($messages){
		foreach($messages as $k => $msg){
			$messages[$k] = is_scalar($msg) ? $msg : var_export_min($msg, true);
		}
		return join(' ', $messages);
	}

	/**
	 * register current object handler
	 * @param $handler
	 * @param string $collecting_level
	 * @param bool $with_trace_info
	 */
	public function register($handler, $collecting_level = LoggerLevel::INFO, $with_trace_info = false){
		self::$handlers[] = [$handler, $collecting_level, $this->id, $with_trace_info];
	}

	/**
	 * register global handler
	 * @param callable $handler
	 * @param string $collecting_level
	 * @param string|null $logger_id
	 * @param bool $with_trace_info
	 */
	public function registerGlobal($handler, $collecting_level = LoggerLevel::INFO, $logger_id = null, $with_trace_info = false){
		self::$handlers[] = [$handler, $collecting_level, $logger_id, $with_trace_info];
	}

	/**
	 * @param $trigger_level
	 * @param $handler
	 * @param string $collecting_level
	 * @param bool $with_trace_info
	 */
	public function registerWhile($trigger_level, $handler, $collecting_level = LoggerLevel::INFO, $with_trace_info = false){
		self::$while_handlers[] = [$trigger_level, $handler, $collecting_level, $this->id, $with_trace_info];
	}

	/**
	 * register while log happens on specified trigger level
	 * @param $trigger_level
	 * @param callable $handler
	 * @param string $collecting_level
	 * @param string|null $logger_id
	 * @param bool $with_trace_info
	 */
	public static function registerWhileGlobal($trigger_level, $handler, $collecting_level = LoggerLevel::INFO, $logger_id = null, $with_trace_info = false){
		self::$while_handlers[] = [$trigger_level, $handler, $collecting_level, $logger_id, $with_trace_info];
	}

	/**
	 * trigger log action
	 * @param $messages
	 * @param $level
	 * @return mixed
	 */
	private function trigger($messages, $level){
		$trace_info = null;

		foreach(self::$handlers as list($handler, $collecting_level, $logger_id, $with_trace_info)){
			if((!$logger_id || $logger_id == $this->id) && LoggerLevel::levelCompare($level, $collecting_level) >= 0){
				//required trace info
				if($with_trace_info && !$trace_info){
					$tmp = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
					$trace_info = $tmp[1];
				}
				if(call_user_func($handler, $messages, $level, $this->id, $trace_info) === false){
					return false;
				}
			}
		}

		//trigger while handlers
		if(self::$while_handlers){
			//check required trace info
			if(!$trace_info){
				foreach(self::$while_handlers as list($trigger_level, $handler, $collecting_level, $logger_id, $with_trace_info)){
					if($with_trace_info){
						$tmp = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
						$trace_info = $tmp[1];
						break;
					}
				}
			}

			self::$log_dumps[] = [$messages, $level, $trace_info];
			foreach(self::$while_handlers as list($trigger_level, $handler, $collecting_level, $logger_id, $with_trace_info)){
				if((!$logger_id || $logger_id == $this->id) && LoggerLevel::levelCompare($level, $trigger_level) >= 0){
					array_walk(self::$log_dumps, function($data) use ($collecting_level, $handler){
						list($message, $level, $trace_info) = $data;
						if(LoggerLevel::levelCompare($level, $collecting_level) >= 0){
							call_user_func($handler, $message, $level, $this->id, $trace_info);
						}
					});
				}
			}
		}

		return $this->log($messages, $level);
	}
}