<?php

namespace LFPhp\Logger\Output;

use LFPhp\Logger\Logger;
use LFPhp\Logger\LoggerLevel;
use function LFPhp\Func\console_color;

class ConsoleOutput extends CommonAbstract {
	public static $level_colors = [
		LoggerLevel::DEBUG     => ['dark_gray'],
		LoggerLevel::INFO      => ['green'],
		LoggerLevel::WARNING   => ['yellow'],
		LoggerLevel::ERROR     => ['red'],
		LoggerLevel::CRITICAL  => ['purple'],
		LoggerLevel::EMERGENCY => ['cyan'],
	];

	public function output($messages, $level, $logger_logger_id, $trace_info = null){
		$lv_str = console_color(strtoupper($level), self::$level_colors[$level][0], self::$level_colors[$level][1]);
		echo date('H:i:s m/d'), ($trace_info ? '' : ' '.$logger_logger_id).' - ', $lv_str.' - ', Logger::combineMessages($messages);
		if($trace_info){
			echo ' ';
			CommonAbstract::printTraceInfo($trace_info);
		}
		echo PHP_EOL;
	}
}