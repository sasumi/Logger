<?php

namespace LFPhp\Logger\test;

use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use LFPhp\Logger\Logger;

include dirname(__DIR__).'/autoload.php';

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
	echo "process man: ", Logger::combineMessages($messages), PHP_EOL;
}, LoggerLevel::INFO);

//Business start ...
class MyClass {
	public function foo(){
		$msg = "I'm calling foo()";
		Logger::instance(__CLASS__)->info($msg);
		return $msg;
	}

	public function castError(){
		Logger::warning('warning, error happens');
	}

	public function __construct(){
		Logger::instance(__CLASS__)->debug('class construct.');
	}

	public function __destruct(){
		Logger::warning('class destruct.');
	}
}

$obj = new MyClass();
$obj->foo();
$obj->castError();;

