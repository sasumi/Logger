<?php

use FSLogger\Logger\LoggerLevel;
use FSLogger\Logger\Output\ConsoleOutput;
use FSLogger\Logger\Output\FileOutput;
use FSLogger\Logger\Logger;

include dirname(__DIR__).'/autoload.php';

//print all log to screen
Logger::register(new ConsoleOutput, LoggerLevel::DEBUG);

//log only level bigger than INFO to file
Logger::register(new FileOutput(__DIR__.'/log/debug.log'), LoggerLevel::INFO);

//log by id(class name)
Logger::register(new FileOutput(__DIR__.'/log/class.log'), LoggerLevel::DEBUG, MyClass::class);

//log on warning happens
Logger::registerWhile(LoggerLevel::WARNING, new FileOutput(__DIR__.'/log/Lite.error.log'), LoggerLevel::INFO);

//custom processor binding
Logger::register(function($messages, $level){
	echo "process man: ", Logger::combineMessages($messages);
}, LoggerLevel::INFO);


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
		Logger::instance(__CLASS__)->debug('class construct called');
	}

	public function __destruct(){
		Logger::warning('class destruct called');
	}
}

$obj = new MyClass();
$obj->foo();
$obj->castError();;

