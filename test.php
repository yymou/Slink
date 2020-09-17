<?php
require 'vendor/autoload.php';
//生成短连接测试
$slink = Slink\App::getInstance()->getSlink('www.baidu.com');
echo $slink . "\n";
//短连接转换原始链接测试
$olink =Slink\App::getInstance()->getOlink($slink);
echo $olink . "\n";