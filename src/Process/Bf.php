<?php
/**
 * 布隆过滤器类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/15
 */

namespace Slink\Process;

use Slink\Component\Single;
use Slink\Component\Bfhash;
use Slink\Cache\Redis;

class Bf
{
    use Single;
    /**
     * 需要使用一个方法来定义bucket的名字
     */
    protected $bucket = 'bf';

    protected $hashFunction = ['BkdrHash', 'SdbmHash', 'JsHash'];

    public function __construct()
    {
        $this->Hash = Bfhash::getInstance();
    }

    /**
     * 添加到集合中
     */
    public function add($string)
    {
        $pipe = Redis::getInstance()->getMulti('write');
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string);
            $pipe->setBit($this->bucket, $hash, 1);
        }
        return $pipe->exec();
    }

    /**
     * 查询是否存在, 如果曾经写入过，必定回true，如果没写入过，有一定几率会误判为存在
     */
    public function exists($string)
    {
        $pipe = Redis::getInstance()->getMulti('read');
        $len = strlen($string);
        foreach ($this->hashFunction as $function) {
            $hash = $this->Hash->$function($string, $len);
            $pipe = $pipe->getBit($this->bucket, $hash);
        }
        $res = $pipe->exec();
        foreach ($res as $bit) {
            if ($bit == 0) {
                return false;
            }
        }
        return true;
    }
}

?>