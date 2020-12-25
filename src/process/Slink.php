<?php
/**
 * 执行生成流程
 * @author yangyanlei
 * @email yangyanlei@dangdang.com
 * Ctime 2020/9/15
 */
namespace Slink\Process;

use Slink\Component\Single;
use Slink\Cache\Redis;
use Slink\Component\Conver;

class Slink
{
    use Single;

    private $shortLink;

    private $originLink;

    //存储下标
    private $save_alias;

    public function __construct(?string $origin_link)
    {
        if (empty($origin_link)) {
            die('please set originlink');
        }
        $this->originLink = $origin_link;
    }

    /**
     * 开始流程
     */
    public function start()
    {
        //检查是否存在
        $this->checkLink();
        if (!$this->shortLink) {
            //分配短链
            $this->getSlink();
            //保存关系
            $this->saveRelation();
        }

        return $this->shortLink ?? '';
    }

    //检查短链是否存在
    private function checkLink() : bool
    {
        $this->shortLink = Redis::getInstance()->getShort(urlencode($this->originLink));
        if (!empty($this->shortLink)) {
            return true;
        }
        return false;
    }

    //生成短链
    private function getSlink() : void
    {
        $incr_id = Redis::getInstance()->getId();
        $this->save_alias = $incr_id % 10;
        $this->shortLink = Getslink::getInstance()->completeLink(Conver::getInstance()->_10To62($incr_id), $this->save_alias) . $this->save_alias;
    }

    private function saveRelation() : void
    {
        Redis::getInstance()->setLinkHash($this->save_alias, $this->shortLink, urlencode($this->originLink));
        //设置origin-short的缓存
        Redis::getInstance()->saveOriginlink(urlencode($this->originLink), $this->shortLink);
    }

}
?>