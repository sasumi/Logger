<?php

namespace LFPhp\Logger\Output;

abstract class CommonAbstract {
	public static function printTraceInfo($trace_info, $as_return = false){
		$loc = $trace_info['class'].$trace_info['type'].$trace_info['function'].' called at ['.$trace_info['file']."#{$trace_info['line']}]";
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
	 * call as function
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