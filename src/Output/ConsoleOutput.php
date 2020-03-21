<?php

namespace lfphp\Logger\Output;

use lfphp\Logger\Logger;

class ConsoleOutput extends CommonAbstract {
	public function output($messages, $level, $logger_logger_id, $trace_info = null){
		echo date('H:i:s m/d'), ($trace_info ? '' : ' '.$logger_logger_id).' - ', strtoupper($level).' - ', Logger::combineMessages($messages);
		if($trace_info){
			CommonAbstract::printTraceInfo($trace_info);
		}
		echo PHP_EOL;
	}
}