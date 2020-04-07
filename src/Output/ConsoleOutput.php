<?php

namespace LFPhp\Logger\Output;

use LFPhp\Logger\LoggerLevel;
use function LFPhp\Func\console_color;

class ConsoleOutput extends CommonAbstract {
	private $colorless = false;

	protected static $level_colors = [
		LoggerLevel::DEBUG     => ['dark_gray'],
		LoggerLevel::INFO      => ['white'],
		LoggerLevel::WARNING   => ['yellow'],
		LoggerLevel::ERROR     => ['red'],
		LoggerLevel::CRITICAL  => ['purple'],
		LoggerLevel::EMERGENCY => ['cyan'],
	];

	/**
	 * ConsoleOutput constructor.
	 * @param bool $colorless
	 */
	public function __construct($colorless = false){
		$this->colorless = $colorless;
	}

	public function output($messages, $level, $logger_id, $trace_info = null){
		$lv_str = strtoupper($level);
		if(!$this->colorless){
			$lv_str = console_color($lv_str, self::$level_colors[$level][0], self::$level_colors[$level][1]);
		}
		echo self::formatAsText($messages, $lv_str, $logger_id, $trace_info),PHP_EOL;
	}
}