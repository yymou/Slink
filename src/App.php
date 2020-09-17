<?php
/**
 * 核心类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/8
 */

namespace Slink;

use Slink\Component\Single;
use Slink\Process\Slink;
use Slink\Process\Olink;

class App
{
    use Single;

    //构造方法
    public function __construct()
    {
        defined('SLINK_ROOT') or define('SLINK_ROOT', realpath(getcwd()));
        //加载配置文件
        $this->loadEnv();
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

    private function loadEnv()
    {
        $file = SLINK_ROOT . '/env.php';
        if(file_exists($file)){
            Config::getInstance()->loadEnv($file);
        }else{
            die('env file missing');
        }
    }
}
?>