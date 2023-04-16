<?php
namespace LFPhp\Logger\Output;

use LFPhp\Logger\Logger;

/**
 * Class SummaryOutput
 * collect message and then flush them in specified time interval
 * @package LFPhp\Logger\Output
 */
class SummaryOutput extends CommonAbstract {
	private $flusher;
	private $tmp_file;
	private $start_time;
	private $send_interval;
	private $subject;

	/**
	 * constructor options
	 * @param callable $flusher
	 * @param int $send_interval
	 * @param string $tmp_file
	 */
	public function __construct($flusher, $send_interval = 300, $tmp_file = ''){
		$this->flusher = $flusher;
		if(!$tmp_file){
			$tmp_fold = sys_get_temp_dir().'/logger/';
			mkdir($tmp_fold, 0755, true);
			$tmp_file = tempnam($tmp_fold, 'lgg');
		}
		$this->setTemporalFile($tmp_file);
		$this->send_interval = $send_interval;
	}

	/**
	 * set temporal file
	 * @param $tmp_file
	 * @return \LFPhp\Logger\Output\SummaryOutput
	 */
	public function setTemporalFile($tmp_file){
		$this->tmp_file = $tmp_file;
		if(is_file($tmp_file)){
			$this->start_time = filemtime($tmp_file);
		}
		return $this;
	}

	/**
	 * @param bool $flush
	 * @return bool|null
	 */
	private function send($flush = false){
		if(!$this->tmp_file || !is_file($this->tmp_file)){
			return false;
		}
		if(!$flush && filemtime($this->tmp_file) > (time() - $this->send_interval)){
			return false;
		}

		$content = file_get_contents($this->tmp_file);
		$content = trim($content);
		if(!$content){
			unlink($this->tmp_file); //may be trigger by sometime ?
			return null;
		}
		$subject = $this->subject ?: 'Unknown Errors';
		call_user_func($this->flusher, $subject, $content);
		unlink($this->tmp_file);
		return true;
	}

	/**
	 * insert file separator after context
	 */
	public function __destruct(){
		$this->send(true);
	}

	/**
	 * do log
	 * @param $messages
	 * @param string $level
	 * @param null $logger_id
	 * @param null $trace_info
	 */
	public function output($messages, $level, $logger_id, $trace_info = null){
		if(!$this->start_time){
			$this->start_time = time();
		}
		$text = self::formatAsText($messages, $level, $logger_id, $trace_info);
		$this->subject = "[".strtoupper($level)."] ".Logger::combineMessages($messages);
		file_put_contents($this->tmp_file, $text.PHP_EOL, FILE_APPEND);
	}
}