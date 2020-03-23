<?php

namespace LFPhp\Logger\Output;

abstract class CommonAbstract {
	/**
	 * print trace info
	 * @param array $trace_info trace info from debug_backtrace()
	 * @param bool $with_func output with class or function name
	 * @param bool $as_return output as return only
	 * @return string
	 */
	public static function printTraceInfo($trace_info, $with_func = false, $as_return = false){
		$loc = '';
		if($with_func){
			$loc .= $trace_info['class'].$trace_info['type'].$trace_info['function'].'() ';
		}
		$loc .= $trace_info['file']."#{$trace_info['line']}";
		if(!$as_return){
			echo $loc;
		}
		return $loc;
	}

	/**
	 * output handler
	 * @param mixed[] $messages
	 * @param string $level
	 * @param string $logger_id
	 * @param array $locate_info
	 * @return mixed
	 */
	abstract public function output($messages, $level, $logger_id, $locate_info);

	/**
	 * output called as function
	 * @param mixed[] $messages
	 * @param string $level
	 * @param string $logger_id
	 * @param array $locate_info
	 * @return mixed
	 */
	public function __invoke($messages, $level, $logger_id, $locate_info = []){
		return $this->output($messages, $level, $logger_id, $locate_info);
	}
}