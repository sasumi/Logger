<?php

namespace LFPhp\Logger;

/**
 * Logger Trait
 * @package LFPhp\Logger
 */
trait LoggerTrait {
	/** @var Logger|null */
	protected static $__logger;

	/**
	 * Set the Logger set for the current class
	 * @param Logger $__logger
	 */
	public static function setLogger(Logger $__logger){
		static::$__logger = $__logger;
	}

	/**
	 * Get the Logger set for the current class
	 * @return Logger
	 */
	public static function getLogger(){
		if(!static::$__logger){
			static::$__logger = new Logger(static::class);
		}
		return static::$__logger;
	}
}
