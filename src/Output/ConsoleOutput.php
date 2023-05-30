<?php

namespace LFPhp\Logger\Output;

use LFPhp\Logger\LoggerLevel;
use function LFPhp\Func\console_color;

/**
 * 控制台输出
 */
class ConsoleOutput extends CommonAbstract {
	private $colorless;

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
	 * @param bool $colorless 是否不添加颜色，缺省输出颜色
	 */
	public function __construct($colorless = false){
		$this->colorless = $colorless;
	}

	/**
	 * 输出信息
	 * @param array $messages
	 * @param string $level
	 * @param string $logger_id
	 * @param null $trace_info
	 * @return void
	 */
	public function output($messages, $level, $logger_id, $trace_info = null){
		$lv_str = strtoupper($level);
		$str = self::formatAsText($messages, $lv_str, $logger_id, $trace_info);
		if(!$this->colorless){
			$str = console_color($str, self::$level_colors[$level][0], isset(self::$level_colors[$level][1]) ? self::$level_colors[$level][1] : null);
		}
		echo $str, PHP_EOL;
	}
}