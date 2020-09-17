<?php
namespace Slink\Cache;

use Slink\Component\Single;
use Slink\Component\Di;
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

    private $prefix = '';

    const STRING_NAME = 'STRING_NAME';
    const SET_NAME = 'SET_NAME';
    const HASH_NAME = 'HASH_NAME';
    const INCR_NAME = 'INCR_NAME';

    //读库
    private function read()
    {
        $this->conn('read');
        return $this->handler['read'];
    }

    //写库
    private function write()
    {
        if (!isset($this->handler['write'])) {
            $this->conn('write');
        }
        return $this->handler['write'];
    }

    public function conn($mode = 'read')
    {
        //读取配置
        $params = Config::getInstance()->getEnv('redis');
        if (empty($params)) {
            throw new \Exception("redis config params can not empty");
        }
        //读写库
        if ($mode == 'read') {
            //读库
            $rand_number = rand(0, $params['read']['count'] - 1);
            $connect = $params['read'][$rand_number];
        } else if($mode == 'write') {
            $connect = $params['write'];
        }
        $timeout = isset($connect['timeout']) ? $connect['timeout'] : 10;

        //是否有前缀
        if (isset($params['prefix']) && !empty($params['prefix'])) {
            $this->prefix = $params['prefix'];
        }

        //是否开启扩展
        if (extension_loaded('redis')) {
            $this->handler[$mode] = new \Redis;
            $this->handler[$mode]->connect($connect['hostname'], $connect['port'], $timeout);

            if ($connect['password'] != '') {
                $this->handler[$mode]->auth($connect['password']);
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
        return $this->write()->setex($this->getName(self::STRING_NAME . ':' . $originlink), Config::getInstance()->getEnv('redis.orilinkTtl') ?? 86400, $shortlink);
    }

    public function getShort(string $originlink) : ?string
    {
        return $this->read()->get($this->getName(self::STRING_NAME . ':' . $originlink));
    }
}