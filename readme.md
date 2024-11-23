# Logger Library
> The current library is tested based on PHP5.6 and above.

## Install
```shell script
composer require lfphp/logger
```

## Using
The library provides Logger methods for simple log collection.
External callers register event handlers through the registration method ```Logger::register```.
Internal objects can use the object method ```$logger->register``` to register event handling

example:
> Business code: **business.php**
```php
<?php
use LFPhp\Logger\Logger;

//Business start ...
class MyClass {
private $logger;
	public function __construct(){
$this->logger = Logger::instance(__CLASS__);

//Register specific log object event processing
$this->logger->register(function($messages){
echo "Log from internal";
var_dump($messages);
echo PHP_EOL;
});

$this->logger->debug('class construct.'); //Logging in the object
	}

	public function foo() {
		$msg = "I'm calling foo()";
		$this->logger->info($msg); //Logging in the object
		return $msg;
	}

	public function castError(){
		$this->logger->warning('warning, error happens'); //Logging in the object
	}

	public function __destruct(){
		$this->logger->warning('class destruct.'); //Logging in the object
	}
}

//Global logging
Logger::debug('Global logging start...');

$obj = new MyClass();
Logger::info('Object created', $obj);

$obj->foo();
$obj->castError();
unset($obj);

//Global logging
Logger::warning('Object destructed');
```

> Business call, log monitoring code: **test.php**
```php
<?php
use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use LFPhp\Logger\Logger;
use LFPhp\Logger\test\MyClass;

require_once "autoload.php";

//Print all log information to the console (screen)
Logger::registerGlobal(new ConsoleOutput, LoggerLevel::DEBUG);

//Record information with a level greater than or equal to INFO to the file
Logger::registerGlobal(new FileOutput(__DIR__.'/log/Lite.debug.log'), LoggerLevel::INFO);

//Record all log information of the registration ID Curl::class (generally the class name is used as the registration ID) to the file
Logger::registerGlobal(new FileOutput(__DIR__.'/log/Lite.curl.log'), LoggerLevel::DEBUG, MyClass::class);

//Only when a WARNING level log event occurs, log all information with a level greater than or equal to INFO to the file
Logger::registerWhileGlobal(LoggerLevel::WARNING, new FileOutput(__DIR__.'/log/Lite.error.log'), LoggerLevel::INFO);

//Process the information yourself
Logger::registerGlobal(function($messages, $level){
	var_dump($messages);
	//Execute processing logic
}, LoggerLevel::INFO);

//Start normal business
require_once "business.php";
```
