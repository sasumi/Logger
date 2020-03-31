<?php

namespace LFPhp\Logger\Output;

use LFPhp\Logger\Logger;

/**
 * 文件保存
 */
class FileOutput extends CommonAbstract {
	private $file;
	private $file_fp;
	private $format = '%H:%i:%s %m/%d {id} - {level} - {message}';

	/**
	 * @param null $log_file
	 * @return \LFPhp\Logger\Logger|void
	 */
	public function __construct($log_file = null){
		$log_file = $log_file ?: sys_get_temp_dir().'/logger.'.date('Ymd').'.log';
		$this->setFile($log_file);
	}

	/**
	 * set log file
	 * @param string $log_file log file path
	 * @return \LFPhp\Logger\Output\FileOutput
	 */
	public function setFile($log_file){
		if(is_callable($log_file)){
			$log_file = call_user_func($log_file);
		}
		$dir = dirname($log_file);
		if(!is_dir($dir)){
			mkdir($dir, 0777, true);
		}
		$this->file = $log_file;
		return $this;
	}

	/**
	 * @param $format
	 * @return $this
	 */
	public function setFormat($format){
		$this->format = $format;
		return $this;
	}

	/**
	 * do log
	 * @param $messages
	 * @param string $level
	 * @param null $logger_id
	 * @param null $trace_info
	 * @return mixed|void
	 */
	public function output($messages, $level, $logger_id, $trace_info = null){
		$str = str_replace(['{id}', '{level}', '{message}'], [
			$logger_id,
			$level,
			Logger::combineMessages($messages),
		], $this->format);
		$str = preg_replace_callback('/(%\w)/', function($matches){
			return date(str_replace('%', '', $matches[1]));
		}, $str);
		if(!$this->file_fp){
			$this->file_fp = fopen($this->file, 'a');
		}
		fwrite($this->file_fp, $str.PHP_EOL);
	}
}