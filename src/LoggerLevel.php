<?php

namespace LFPhp\Logger;

/**
 * Class LoggerLevel
 * defined referred to PSR-3
 * @package LFPhp\Logger
 */
class LoggerLevel {
	const DEBUG = 'debug';
	const INFO = 'info';
	const NOTICE = 'notice';
	const WARNING = 'warning';
	const ERROR = 'error';
	const EXCEPTION = 'exception';
	const CRITICAL = 'critical';
	const ALERT = 'alert';
	const EMERGENCY = 'emergency';

	/**
	 * log level error level order by severity
	 */
	const SEVERITY_ORDER = [
		self::DEBUG,
		self::INFO,
		self::NOTICE,
		self::WARNING,
		self::ERROR,
		self::EXCEPTION,
		self::CRITICAL,
		self::ALERT,
		self::EMERGENCY,
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
	 * get level above than
	 * @param string $level
	 * @param bool $include_current
	 * @return array
	 */
	public static function levelAboveThan($level, $include_current = false){
		$lvs = $include_current ? [$level] : [];
		$match_flag = false;
		foreach(self::SEVERITY_ORDER as $lvl){
			if($match_flag){
				$lvs[] = $lvl;
			}
			if($lvl == $level){
				$match_flag = true;
			}
		}
		return $lvs;
	}

	/**
	 * get levels lower than
	 * @param string $level
	 * @param bool $include_current
	 * @return array
	 */
	public static function levelLowerThan($level, $include_current = false){
		$lvs = [];
		foreach(self::SEVERITY_ORDER as $lvl){
			if($lvl == $level){
				break;
			}
			$lvs[] = $lvl;
		}
		if($include_current){
			$lvs[] = $level;
		}
		return $lvs;
	}

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
		$lv1_idx = array_search($lv1, self::SEVERITY_ORDER);
		$lv2_idx = array_search($lv2, self::SEVERITY_ORDER);
		if($lv1_idx === $lv2_idx){
			return 0;
		}
		return $lv1_idx > $lv2_idx ? 1 : -1;
	}
}