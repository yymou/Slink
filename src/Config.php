<?php

namespace Slink;

use Slink\Component\Single;
use Slink\Component\Di;
use Slink\Cache\Redis;

/**
 * 配置类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/15
 */
class Config
{
    use Single;

    const COMMOM_REDIS = 'COMMON_REDIS';
    const REDIS_PREFIX = 'REDIS_PREFIX';
    const LINK_LEN = 'LINK_LEN';
    const GET='get';
    const SET='set';

    private static $configName = 'slink.config';

    public function loadEnv(string $file)
    {
        if (file_exists($file)) {
            $env_config = require $file;
            if (is_array($env_config)) {
                foreach ($env_config as $key => $val) {
                    Di::getInstance()->set(self::getName($key), $val);
                }
            }
        } else {
            throw new \Exception("config file : {$file} is miss");
        }
    }

    //设置和获取配置
    public function __call($func_name, $arg = NULL)
    {
        $type = substr($func_name, 0, 3);
        if (!in_array($type, [self::GET, self::SET])) {
            throw new \Exception("funcion is miss");
        }
        $name = substr($func_name, 3);
        if ($type == self::GET) {
            return $this->getEnv($name);
        } else {
            Di::getInstance()->set(self::getName($name), $arg[0] ? $arg[0] : '');
        }
    }

    public static function getName(string $name) : string
    {
        return self::$configName . '.' . $name;
    }

    public function getEnv(string $name)
    {
        $return = [];
        if (strrpos($name, '.') !== false) {
            $name_arr = explode('.', $name);
            $return = Di::getInstance()->get(self::getName(array_shift($name_arr)));
            do {
                $temp = $return[array_shift($name_arr)] ?? [];
                $return = $temp;
                if (empty($temp) || empty($name_arr)) {
                    break;
                }
                unset($temp);
            } while (true);
        } else {
            $return = Di::getInstance()->get(self::getName($name));
        }
        return $return;
    }
}