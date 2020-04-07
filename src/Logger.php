<?php

namespace LFPhp\Logger;

use function LFPhp\Func\var_export_min;

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
	 * @var array format：[[processor, collecting_level, logger_id, with_trace_info, last_occurs_index],...]
	 */
	private static $handlers = [];
	private static $while_handlers = [];
	private static $log_dumps = [];

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
	 * @param callable $handler
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
	 * @param array|string|null $logger_id specified logger instance id, or id list
	 * @param bool $with_trace_info
	 */
	public static function registerGlobal($handler, $collecting_level = LoggerLevel::INFO, $logger_id = null, $with_trace_info = false){
		self::$handlers[] = [$handler, $collecting_level, $logger_id, $with_trace_info];
	}

	/**
	 * @param string $trigger_level
	 * @param callable $handler
	 * @param string $collecting_level
	 * @param bool $with_trace_info
	 */
	public function registerWhile($trigger_level, $handler, $collecting_level = LoggerLevel::INFO, $with_trace_info = false){
		self::$while_handlers[] = [$trigger_level, $handler, $collecting_level, $this->id, $with_trace_info, 0];
	}

	/**
	 * register while log happens on specified trigger level
	 * @param string $trigger_level
	 * @param callable $handler
	 * @param string $collecting_level
	 * @param array|string|null $logger_id specified logger instance id, or id list
	 * @param bool $with_trace_info
	 */
	public static function registerWhileGlobal($trigger_level, $handler, $collecting_level = LoggerLevel::INFO, $logger_id = null, $with_trace_info = false){
		self::$while_handlers[] = [$trigger_level, $handler, $collecting_level, $logger_id, $with_trace_info, 0];
	}

	/**
	 * clear log dumps
	 */
	public static function clearDump(){
		self::$log_dumps = [];
	}

	/**
	 * trigger log action
	 * @param $messages
	 * @param $level
	 * @return mixed
	 */
	private function trigger($messages, $level){
		$trace_info = null;

		//trace信息补全
		if(in_array(true, array_column(self::$handlers, 3), true) ||
			in_array(true, array_column(self::$while_handlers, 4), true)){
			$tmp = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
			$trace_info = $tmp[1];
		}

		//普通单次绑定事件触发
		foreach(self::$handlers as list($handler, $collecting_level, $logger_id, $with_trace_info)){
			$match_id = !$logger_id || (is_array($logger_id) && in_array($this->id, $logger_id)) || $logger_id === $this->id;
			if($match_id && LoggerLevel::levelCompare($level, $collecting_level) >= 0){
				if(call_user_func($handler, $messages, $level, $this->id, $trace_info) === false){
					return false;
				}
			}
		}

		//条件绑定事件触发
		if(self::$while_handlers){
			self::$log_dumps[] = [$messages, $level, $trace_info, $this->id];
			foreach(self::$while_handlers as $k => list($trigger_level, $handler, $collecting_level, $logger_id, $with_trace_info, $last_occurs_index)){
				$match_id = !$logger_id || (is_array($logger_id) && in_array($this->id, $logger_id)) || $logger_id === $this->id;
				if($match_id && LoggerLevel::levelCompare($level, $trigger_level) >= 0){
					$dumps = array_slice(self::$log_dumps, $last_occurs_index);
					//update last trigger dumping data index
					self::$while_handlers[$k][5] = $last_occurs_index + count($dumps);
					array_walk($dumps, function($data) use ($collecting_level, $handler){
						list($message, $level, $trace_info, $logger_id) = $data;
						if(LoggerLevel::levelCompare($level, $collecting_level) >= 0){
							call_user_func($handler, $message, $level, $logger_id, $trace_info);
						}
					});
				}
			}
		}
		return $this->log($messages, $level);
	}
}