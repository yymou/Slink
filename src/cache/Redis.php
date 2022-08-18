<?php
namespace Slink\Cache;

use Slink\Component\Bfhash;
use Slink\Component\Single;
use Slink\Config;
/**
 * redis操作类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/15
 */
class Redis
{
    use Single;

    private $mode; //写库

    private $handler;

    private $hardHandler;

    private $prefix = 'slink';

    const STRING_NAME = 'STRING_NAME';
    const SET_NAME = 'SET_NAME';
    const BF_NAME = 'BIT_NAME';
    const HASH_NAME = 'HASH_NAME';
    const INCR_NAME = 'INCR_NAME';

    public function __construct()
    {
        //是否有现有的redis实例
        $this->hardHandler = Config::getInstance()->getRedisConnect() ?? null;
    }

    //读库
    private function read()
    {
        if (!empty($this->hardHandler)) {
            return $this->hardHandler;
        }
        if (!isset($this->handler['read'])) {
            $this->conn();
        }
        return $this->handler['read'];
    }

    //写库
    private function write()
    {
        if (!empty($this->hardHandler)) {
            return $this->hardHandler;
        }
        if (!isset($this->handler['write'])) {
            $this->conn('write');
        }
        return $this->handler['write'];
    }

    public function conn($mode = 'read')
    {
        //读取配置
        //$params = Config::getInstance()->getEnv('redis');
        //是否配置通用
        $common_redis = Config::getInstance()->getCommonRedis();
        if (!empty($common_redis)) {
            $connect = $common_redis;
        } else {
            //查看读写库
            $cluster_redis = Config::getInstance()->getClusterRedis();
            if (!empty($cluster_redis)) {
                if (isset($cluster_redis[$mode])) {
                    //读写库
                    if ($mode == 'read') {
                        //读库
                        $rand_number = rand(0, count($cluster_redis[$mode]) - 1);
                        $connect = $cluster_redis[$mode][$rand_number];
                    } else if($mode == 'write') {
                        $connect = $cluster_redis[$mode];
                    }
                }
            }
        }
        if (empty($connect)) {
            throw new \Exception("redis config params can not empty");
        }
        $timeout = isset($connect['timeout']) ? $connect['timeout'] : 10;

        //是否有前缀
        $redis_prefix = Config::getInstance()->getRedisPrefix();

        if (!empty($redis_prefix)) {
            $this->prefix = $redis_prefix;
        }

        //是否开启扩展
        if (extension_loaded('redis')) {
            $this->handler[$mode] = new \Redis;
            $this->handler[$mode]->connect($connect['hostname'], $connect['port'] ?? 6379, $timeout);
            if (isset($connect['password']) && $connect['password'] != '') {
                $this->handler[$mode]->auth($connect['password']);
            }
            if (isset($connect['database']) && $connect['database'] != '') {
                $this->handler[$mode]->select($connect['database']);
            }
        } else {
            throw new \BadFunctionCallException('not support: redis');
        }
    }

    private function getName(string $name) {
        return empty($this->prefix) ? $name : $this->prefix . ':' . $name;
    }

    public function addSet(string $key_name, string $value) {
        return $this->write()->sAdd($this->getName($key_name), $value);
    }

    public function getMulti(string $mode = 'read') {
        return $this->$mode()->multi();
    }

    public function getHash(string $key_name, string $slink) {
        return $this->read()->hGet($key_name, $slink);
    }

    public function setLinkHash($alias, string $slink, string $originlink) {
        return $this->write()->hSetNx($this->getName(self::HASH_NAME . ":" . $alias), $slink, $originlink);
    }

    public function getLinkHash($alias, string $slink) {
        return $this->write()->hGet($this->getName(self::HASH_NAME . ":" . $alias), $slink);
    }

    public function checkLinkHash($alias, string $slink) {
        return $this->read()->hExists($this->getName(self::HASH_NAME . ":" . $alias), $slink);
    }

    public function getId() {
        return $this->read()->incr($this->getName(self::INCR_NAME));
    }

    public function saveOriginlink(string $originlink, ?string $shortlink) {
        return $this->write()->setex($this->getName(self::STRING_NAME . ':' . $originlink), Config::getInstance()->getOlinkCacheTtl() ?? 86400, $shortlink);
    }

    public function getShort(string $originlink) : ?string
    {
        return $this->read()->get($this->getName(self::STRING_NAME . ':' . $originlink));
    }

    public function addBfBit(array $hash_funcs, string $string)
    {
        $pipe = $this->getMulti('write');
        foreach ($hash_funcs as $function) {
            $hash = Bfhash::getInstance()->$function($string);
            $pipe->setBit($this->getName(self::BF_NAME), $hash, 1);
        }
        return $pipe->exec();
    }

    public function existsBfBit(array $hash_funcs, string $string)
    {
        $pipe = $this->getMulti('read');
        $len = strlen($string);
        foreach ($hash_funcs as $function) {
            $hash = Bfhash::getInstance()->$function($string, $len);
            $pipe = $pipe->getBit($this->getName(self::BF_NAME), $hash);
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