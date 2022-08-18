<?php
/**
 * 执行生成流程
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/15
 */
namespace Slink\process;

use Slink\component\Single;
use Slink\cache\Redis;
use Slink\component\Conver;

class Olink
{
    use Single;

    private $shortLink;

    private $originLink;

    //存储下标
    private $save_alias;

    public function __construct(?string $short_link)
    {
        if (empty($short_link)) {
            die('please set shortlink');
        }
        $this->shortLink = $short_link;
    }

    /**
     * 开始流程
     */
    public function start()
    {
        //检查是否存在
        $this->checkLink();
        $this->getOlink();

        return $this->originLink ?? '';
    }

    //检查短链是否存在
    private function checkLink()
    {
        //获取短链的最后一位数字
        $this->save_alias = substr($this->shortLink, -1);
        $is_exist = Redis::getInstance()->checkLinkHash($this->save_alias, $this->shortLink);
        if (!$is_exist) {
            die('slink error');
        }
    }

    //生成短链
    private function getOlink() : void
    {
        $originLinkEncode = Redis::getInstance()->getLinkHash($this->save_alias, $this->shortLink);
        $this->originLink = urldecode($originLinkEncode);
        if (empty($originLinkEncode) || !$this->originLink = urldecode($originLinkEncode)) {
            die('slink error');
        }
    }
}
?>