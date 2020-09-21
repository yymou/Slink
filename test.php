<?php
require 'vendor/autoload.php';
//生成短连接测试
Slink\App::getInstance()->setCommonRedis([
    'hostname' => '127.0.0.1',
    'password' => '',
    'port' => '6379',
    'timeout' => '5'
]);

Slink\App::getInstance()->setRedisPrefix('slink:test');

Slink\App::getInstance()->setSlinkLen(7);

$slink = Slink\App::getInstance()->setOlinkCacheTtl(86400*2);
$slink = Slink\App::getInstance()->getSlink('https://www.baidu.com');
echo $slink . "\n";
//短连接转换原始链接测试
$olink =Slink\App::getInstance()->getOlink($slink);
echo $olink . "\n";