<?php

class AppTest extends \PHPUnit\Framework\TestCase
{
    public function Test() {
        //生成短连接测试
        Slink\App::getInstance()->setCommonRedis([
            'hostname' => '127.0.0.1',
            'password' => '',
            'port' => '6379',
            'timeout' => '5'
        ]);

        Slink\App::getInstance()->setRedisPrefix('slink:test');

        Slink\App::getInstance()->setSlinkLen(7);

        $this->assertContainsOnly('string', Slink\App::getInstance()->getSlink('https://www.baidu.com'));
    }
}