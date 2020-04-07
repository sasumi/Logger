<?php

namespace LFPhp\Logger\test;

use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use LFPhp\Logger\Logger;

include dirname(__DIR__).'/autoload.php';
include dirname(__DIR__,3).'/autoload.php';

$vendor_for_test = dirname(__DIR__).'/vendor/autoload.php';
if(is_file($vendor_for_test)){
	include $vendor_for_test;
}

//print all log to screen
Logger::registerGlobal(new ConsoleOutput, LoggerLevel::DEBUG, null, true);

//log only level bigger than INFO to file
Logger::registerGlobal(new FileOutput(__DIR__.'/log/debug.log'), LoggerLevel::INFO);

//log by id(class name)
Logger::registerGlobal(new FileOutput(__DIR__.'/log/class.log'), LoggerLevel::DEBUG, MyClass::class);

//log on warning happens
Logger::registerWhileGlobal(LoggerLevel::WARNING, new FileOutput(__DIR__.'/log/Lite.error.log'), LoggerLevel::INFO);

//custom processor binding
Logger::registerGlobal(function($messages, $level){
	echo "Log on info: ", Logger::combineMessages($messages), PHP_EOL;
}, LoggerLevel::INFO);

class MyClass {
	private $logger;
	public function __construct(){
		$this->logger = Logger::instance(__CLASS__);

		//具体日志对象事件处理注册
		$this->logger->register(function($messages){
			echo "Log from internal";
			var_dump($messages);
			echo PHP_EOL;
		});

		$this->logger->debug('class construct.'); //对象内日志记录
	}

	public function foo(){
		$msg = "I'm calling foo()";
		$this->logger->info($msg); //对象内日志记录
		return $msg;
	}

	public function castError(){
		$this->logger->warning('warning, error happens'); //对象内日志记录
	}

	public function __destruct(){
		$this->logger->warning('class destruct.'); //对象内日志记录
	}
}

//全局日志记录
Logger::debug('Global logging start...');

$obj = new MyClass();
Logger::info('Object created', $obj);

$obj->foo();
$obj->castError();
unset($obj);

//全局日志记录
Logger::warning('Object destructed');