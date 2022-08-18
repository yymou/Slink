<?php
/**
 * 核心类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/8
 */

namespace Slink;

use Slink\component\Single;
use Slink\process\Slink;
use Slink\process\Olink;

class App
{
    use Single;

    //构造方法
    public function __construct()
    {
        defined('SLINK_ROOT') or define('SLINK_ROOT', realpath(getcwd()));
    }

    //设置和获取配置
    public function __call($func_name, $arg)
    {
        try {
            Config::getInstance()->$func_name($arg[0]);
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    //获取短连接
    public function getSlink(string $origin_link) : ?string
    {
        $this->shortLink = Slink::getInstance($origin_link)->start();
        if (!empty($this->shortLink)) {
            return $this->shortLink;
        }
    }

    //获取原始链接
    public function getOlink(string $short_link) : string
    {
        $this->shortLink = Olink::getInstance($short_link)->start();
        if (!empty($this->shortLink)) {
            return $this->shortLink;
        }
    }
}
?>