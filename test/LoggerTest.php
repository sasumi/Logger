<?php
namespace LFPhp\Logger\TestCase;

use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use LFPhp\Logger\Logger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {
	public function testNormalLogFile(){
		$log_file = sys_get_temp_dir().'/log/info.log';
		Logger::registerGlobal(new FileOutput($log_file), LoggerLevel::INFO);
		self::castMyClass();
		$this->assertFileExists($log_file);
	}

	public function testWhileLogFile(){
		$debug_log = sys_get_temp_dir().'/log/while_warning.debug.log';
		$warning_log = sys_get_temp_dir().'/log/while_warning.debug.log';

		Logger::registerWhileGlobal(LoggerLevel::WARNING, new FileOutput($debug_log), LoggerLevel::DEBUG);
		Logger::registerWhileGlobal(LoggerLevel::ERROR, new ConsoleOutput(), LoggerLevel::INFO);
		Logger::registerWhileGlobal(LoggerLevel::WARNING, new FileOutput($warning_log), LoggerLevel::DEBUG);
		self::castMyClass();

		$this->assertFileExists($debug_log);
		$this->assertFileExists($warning_log);
	}

	private static function castMyClass(){
		$obj = new MyClass();
		Logger::info('3 Object created');

		$obj->foo();
		$obj->castWarning();
		$obj->castError();
		unset($obj);
	}
}