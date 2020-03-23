<?php

namespace LFPhp\Logger;
/**
 * Class LoggerLevel
 * defined referred to PSR-3
 * @package LFPhp\Logger
 */
class LoggerLevel {
	const EMERGENCY = 'emergency';
	const ALERT = 'alert';
	const CRITICAL = 'critical';
	const ERROR = 'error';
	const WARNING = 'warning';
	const NOTICE = 'notice';
	const INFO = 'info';
	const DEBUG = 'debug';

	/**
	 * log level error level order by severity
	 */
	const SEVERITY_ORDER = [
		self::EMERGENCY,
		self::ALERT,
		self::CRITICAL,
		self::ERROR,
		self::WARNING,
		self::NOTICE,
		self::INFO,
		self::DEBUG,
	];

	/**
	 * PHP error code mapping to Logger level
	 */
	const PHP_ERROR_MAPS = array(
		E_ERROR             => self::CRITICAL,
		E_WARNING           => self::WARNING,
		E_PARSE             => self::ALERT,
		E_NOTICE            => self::NOTICE,
		E_CORE_ERROR        => self::CRITICAL,
		E_CORE_WARNING      => self::WARNING,
		E_COMPILE_ERROR     => self::ALERT,
		E_COMPILE_WARNING   => self::WARNING,
		E_USER_ERROR        => self::ERROR,
		E_USER_WARNING      => self::WARNING,
		E_USER_NOTICE       => self::NOTICE,
		E_STRICT            => self::NOTICE,
		E_RECOVERABLE_ERROR => self::ERROR,
		E_DEPRECATED        => self::NOTICE,
		E_USER_DEPRECATED   => self::NOTICE,
	);

	/**
	 * log level compare
	 * @param string $lv1
	 * @param string $lv2
	 * @return int
	 * return
	 * 0 if lv1 equal to lv2,
	 * 1 if lv1 more serious than lv2,
	 * -1 if lv1 less serious than lv2
	 */
	public static function levelCompare($lv1, $lv2){
		$lv1_idx = self::SEVERITY_ORDER[$lv1];
		$lv2_idx = self::SEVERITY_ORDER[$lv2];
		if($lv1_idx === $lv2_idx){
			return 0;
		}
		return $lv1_idx > $lv2_idx ? -1 : 1;
	}
}