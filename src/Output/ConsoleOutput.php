<?php

namespace LFPhp\Logger\Output;

use LFPhp\Logger\LoggerLevel;
use function LFPhp\Func\console_color;

/**
 * 控制台输出
 */
class ConsoleOutput extends CommonAbstract {
	private $colorless;
	public $preset_time = true;

	/**
	 * 不同级别颜色配置[level => [foreground color, background color], ...]
	 * 颜色值定义可以在 console_color() 函数中参考
	 * @var \string[][]
	 */
	public static $COLOR_MAP = [
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
	 * @param bool $preset_time 是否预置时间
	 */
	public function __construct($colorless = false, $preset_time = true){
		$this->colorless = $colorless;
		$this->preset_time = $preset_time;
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
		$str = self::formatAsText($messages, $lv_str, $logger_id, $trace_info, $this->preset_time);
		if(!$this->colorless){
			$str = console_color($str, self::$COLOR_MAP[$level][0], isset(self::$COLOR_MAP[$level][1]) ? self::$COLOR_MAP[$level][1] : null);
		}
		echo $str, PHP_EOL;
	}
}
