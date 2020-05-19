# Logger 库
> 当前库基于PHP5.6及以上环境测试。

## 代码引入
```shell script
composer require lfphp/logger
```

## 方法调用
库提供Logger方法进行简单日志记录收集。
外部调用程序通过注册方法 ```Logger::register``` 进行事件处理注册。
内部对象可使用对象方法 ```$logger->register``` 进行事件处理注册

例：
> 业务代码：**business.php**
```php
<?php
use LFPhp\Logger\Logger;

//Business start ...
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
```

> 业务调用、日志监听代码：**test.php**
```php
<?php
use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use LFPhp\Logger\Logger;
use LFPhp\Logger\test\MyClass;

require_once "autoload.php";

//打印所有日志信息到控制台（屏幕）
Logger::registerGlobal(new ConsoleOutput, LoggerLevel::DEBUG);

//记录等级大于或等于INFO的信息到文件
Logger::registerGlobal(new FileOutput(__DIR__.'/log/Lite.debug.log'), LoggerLevel::INFO);

//记录注册ID为Curl::class（一般使用类名作为注册ID）的所有日志信息到文件
Logger::registerGlobal(new FileOutput(__DIR__.'/log/Lite.curl.log'), LoggerLevel::DEBUG, MyClass::class);

//仅在发生WARNING级别日志事件时记录所有等级大于或等于INFO的信息到文件
Logger::registerWhileGlobal(LoggerLevel::WARNING, new FileOutput(__DIR__.'/log/Lite.error.log'), LoggerLevel::INFO);

//自行处理信息
Logger::registerGlobal(function($messages, $level){
	var_dump($messages);
	//执行处理逻辑
}, LoggerLevel::INFO);

//开始执行正常业务
require_once "business.php";
```
