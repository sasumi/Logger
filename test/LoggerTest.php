<?php
namespace LFPhp\Logger\TestCase;

use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use LFPhp\Logger\Logger;
use PHPUnit\Framework\TestCase;
use PHPUnit\TextUI\XmlConfiguration\File;
use function LFPhp\Func\dump;

class LoggerTest extends TestCase {
	public function testNormalLogFile(){
		$log_file = sys_get_temp_dir().'/log/info.log';
		Logger::registerGlobal(new FileOutput($log_file), LoggerLevel::INFO);
		self::castMyClass();
		$this->assertFileExists($log_file);
	}

	public function testLevelCalc(){
		$lvs = LoggerLevel::levelAboveThan(LoggerLevel::WARNING, true);
		$this->assertIsArray($lvs);

		$lvs2 = LoggerLevel::levelLowerThan(LoggerLevel::EXCEPTION, true);
		$this->assertIsArray($lvs2);
		dump($lvs, $lvs2);
	}

	public function testLogException(){
		Logger::registerGlobal(new ConsoleOutput(), LoggerLevel::DEBUG);
	}

	public function testWhileLogFile(){
		$debug_log = sys_get_temp_dir().'/log/while_warning.debug.log';
		$warning_log = sys_get_temp_dir().'/log/while_warning.debug.log';

		Logger::registerWhileGlobal(LoggerLevel::WARNING, new FileOutput($debug_log), LoggerLevel::DEBUG);
		Logger::registerWhileGlobal(LoggerLevel::ERROR, new ConsoleOutput(), LoggerLevel::INFO);
		Logger::registerWhileGlobal(LoggerLevel::WARNING, new FileOutput($warning_log), LoggerLevel::DEBUG);
		self::castMyClass();

		$ex = new \Exception('File Not Found.');

		Logger::exception($ex);

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

	public function testExceptionLogger(){
		$exp = new \Exception('exception message');
		$logger = Logger::instance();

		$log = __DIR__.'/exp.log';
		$logger->registerWhile(LoggerLevel::EXCEPTION, new FileOutput($log));

		$logger->exception($exp);
		$this->assertFileExists($log);
	}
}
