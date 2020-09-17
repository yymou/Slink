<?php

namespace Slink;

use Slink\Component\Single;
use Slink\Component\Di;

/**
 * 配置类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/15
 */
class Config
{
    use Single;

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