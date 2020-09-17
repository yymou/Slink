<?php
/**
 * 进制转化类
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/16
 */

namespace Slink\Component;

use Slink\Config;

class Conver
{
    use Single;

    //短链长度
    private $slinkLen = 6;

    public $cover_arr = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

    /**
     * 十进制转化62进制
     */
    public function _10To62($dec)
    {
        $result = '';
        do {
            $result = $this->cover_arr[$dec % 62] . $result;
            $dec = intval($dec / 62);
        } while ($dec != 0);

        $this->slinkLen = Config::getInstance()->getEnv('linklen') ?? $this->slinkLen;
        if ($this->slinkLen < 4) {
            die('Slink length setting is too short');
        }

        return $result;
    }

    public function _62To10($str)
    {
        $len = strlen($str);
        $dec = 0;
        $cover_str = implode('', $this->cover_arr);
        for($i = 0;$i < $len; $i++){
            //找到对应字典的下标
            $pos = strpos($cover_str, $str[$i]);
            $dec += $pos*pow(62,$len-$i-1);
        }
        return $dec;
    }
}