<?php

namespace LFPhp\Logger\Output;

class HeaderOutput extends CommonAbstract {
	private static $idx;

	public function output($messages, $level, $logger_id, $trace_info = null){
		$lv_str = strtoupper($level);
		@header('DBG'.(++self::$idx).': '.self::formatAsText($messages, $lv_str, $logger_id, $trace_info));
	}
}