<?php
namespace Slink\Process;

use Slink\Component\Single;
use Slink\Config;
use Slink\Component\Conver;

/**
 * 获取slink处理类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/17
 */
class Getslink
{
    use Single;

    //短链长度
    private $slinkLen = 7;

    public function __construct()
    {
        $this->slinkLen = Config::getInstance()->getSlinkLen() ?? $this->slinkLen;
        if ($this->slinkLen < 4) {
            die('Slink length setting is too short');
        }
    }

    //通过算法补全长度
    public function completeLink(?string $str, int $alias) : ? string
    {
        //补全短链长度并转化
        return $this->getNewLink(sprintf("%0" . ($this->slinkLen - 1) . "s", $str), $alias);
    }

    private function getNewLink($link, $count = 0)
    {
        $count = $count+1;
        //根据id补全短链长度
        $new_slink = '';
        $cover_arr = Conver::getInstance()->cover_arr;
        do {
            $temp = '';
            for ($i = 0; $i < $this->slinkLen - 1; $i++) {
                //属于第几位
                $offset = array_search($link[$i], $cover_arr);
                //根据offset和i转换 + 8
                $temp .= $cover_arr[($offset + $i + 8) % 62];
            }
            $link = $temp;
            unset($temp);
            $count--;
        } while ($count > 0);
        return $link;
    }
}