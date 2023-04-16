<?php

namespace LFPhp\Logger\Output;

use LFPhp\Logger\LoggerLevel;

class BrowserConsoleOutput extends CommonAbstract {
	protected static $level_map = [
		LoggerLevel::DEBUG     => 'debug',
		LoggerLevel::INFO      => 'info',
		LoggerLevel::WARNING   => 'warn',
		LoggerLevel::ERROR     => 'error',
		LoggerLevel::CRITICAL  => 'error',
		LoggerLevel::EMERGENCY => 'error',
	];

	private $logs;

	public function __construct(){
		register_shutdown_function(function(){
			if(!$this->logs){
				return;
			}
			echo '<script>';
			foreach($this->logs as list($level, $messages, $logger_id, $trace_info)){
				$op = self::$level_map[$level];
				$json = [json_encode('%c['.$logger_id.'] '), json_encode('color:#1ca54d; font-weight:bold;')];
				foreach($messages as $msg){
					$json[] = json_encode($msg, JSON_UNESCAPED_UNICODE);
				}
				if($trace_info){
					$callee = $trace_info['class'].$trace_info['type'].$trace_info['function'].'()';
					$loc = $trace_info['file']."({$trace_info['line']})";
					$json[] = json_encode("\n[Callee] $callee");
					$json[] = json_encode("\n[Loc] {$loc}");
				}
				echo "console.{$op}(".join(",", $json).");", PHP_EOL;
			}
			echo '</script>';
		});
	}

	public function output($messages, $level, $logger_id, $trace_info = null){
		$this->logs[] = [$level, $messages, $logger_id, $trace_info];
	}
}