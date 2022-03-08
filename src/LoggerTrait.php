<?php

namespace LFPhp\Logger;

/**
 * Logger特性
 * @package LFPhp\Logger
 */
trait LoggerTrait {
	/** @var Logger|null */
	protected static $__logger;

	/**
	 * 为当前class设置当前Logger
	 * @param Logger $__logger
	 */
	public static function setLogger(Logger $__logger){
		static::$__logger = $__logger;
	}

	/**
	 * 获取为当前class设置的Logger
	 * @return Logger
	 */
	public static function getLogger(){
		if(!static::$__logger){
			static::$__logger = new Logger(static::class);
		}
		return static::$__logger;
	}
}