<?php
/**
 * 布隆过滤器类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/15
 */

namespace Slink\Process;

use Slink\Component\Single;
use Slink\Cache\Redis;

class Bf
{
    use Single;
    /**
     * 需要使用一个方法来定义bucket的名字
     */
    protected $hashFunction = ['BkdrHash', 'SdbmHash', 'JsHash'];


    /**
     * 添加到集合中
     */
    public function add($string)
    {
        return Redis::getInstance()->addBfBit($this->hashFunction, $string);
    }

    /**
     * 查询是否存在, 如果曾经写入过，必定回true，如果没写入过，有一定几率会误判为存在
     */
    public function exists($string)
    {
        return Redis::getInstance()->existsBfBit($this->hashFunction, $string);
    }
}

?>