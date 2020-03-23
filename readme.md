# Logger 库
> 当前库基于PHP5.6及以上环境测试。

## 代码引入

## 方法调用
库提供Logger方法进行简单日志记录收集。外部调用程序通过注册方法 ```Logger::register``` 进行事件处理注册。

例：

```php
<?php
use LFPhp\Logger\LoggerLevel;
use LFPhp\Logger\Output\ConsoleOutput;
use LFPhp\Logger\Output\FileOutput;
use LFPhp\Logger\Logger;
use LFPhp\Logger\test\MyClass;

require_once "autoload.php";

//打印所有日志信息到控制台（屏幕）
Logger::register(new ConsoleOutput, LoggerLevel::DEBUG);

//记录等级大于或等于INFO的信息到文件
Logger::register(new FileOutput(__DIR__.'/log/Lite.debug.log'), LoggerLevel::INFO);

//记录注册ID为Curl::class（一般使用类名作为注册ID）的所有日志信息到文件
Logger::register(new FileOutput(__DIR__.'/log/Lite.curl.log'), LoggerLevel::DEBUG, MyClass::class);

//仅在发生WARNING级别日志事件时记录所有等级大于或等于INFO的信息到文件
Logger::registerWhile(LoggerLevel::WARNING, new FileOutput(__DIR__.'/log/Lite.error.log'), LoggerLevel::INFO);

//自行处理信息
Logger::register(function($messages, $level){
	//执行处理逻辑
}, LoggerLevel::INFO);
```

