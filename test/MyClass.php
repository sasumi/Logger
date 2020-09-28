<?php
namespace LFPhp\Logger\TestCase;
use LFPhp\Logger\Logger;

class MyClass {
	private $logger;
	public function __construct(){
		$this->logger = Logger::instance(__CLASS__);
		$this->logger->debug('2 class construct.'); //对象内日志记录
	}

	public function foo(){
		$this->logger->info("4 I'm calling foo()"); //对象内日志记录
	}

	public function castWarning(){
		$this->logger->warning('5 warning happens'); //对象内日志记录
	}

	public function castError(){
		$this->logger->error('6 error happens'); //对象内日志记录
	}

	public function __destruct(){
		$this->logger->warning('7 class destruct.'); //对象内日志记录
	}
}