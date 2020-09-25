# Slink
利用redis实现的常用的短链服务, 支持redis一主多从配置, 速度快且运行稳定(建议开启redis持久化), 能满足亿级别的链接转化需求.
底层使用redis发号器递增转换成62进制,但根据简单算法实现了短链接非递增返回. 解析短链依托redis,速度快.

#### 使用:
+ 1 使用composer引入
  + 执行 `composer require yymou/slink`
  + 项目中引入composer `require 'vendor/autoload.php';`
  
+ 2 项目中使用
  + 设置redis连接: 通用配置 > 读写分离配置
    + 通用配置 :
        + ```
            Slink\App::getInstance()->setCommonRedis([
               'hostname' => '127.0.0.1',
               'password' => '',
               'port' => '6379',
               'timeout' => '5'
           ]);
          ```
    + 读写分离配置 :
        + ```
          Slink\App::getInstance()->setClusterRedis([
               'write' => [
                   'hostname' => '127.0.0.2',
               ],
               //支持设置多个读库
               'read' => [
                   [
                       'hostname' => '127.0.0.3',
                   ],
                   [
                       'hostname' => '127.0.0.4',
                   ]
               ],
           ]);
          ```
    > 如使用读写分离配置,则不可设置通用配置,否则不生效;
  + 设置redis前缀(可选)
    ```
    Slink\App::getInstance()->setRedisPrefix('slink:test');
    ```
    > 如不设置则默认前缀为slink
  + 设置原始链接缓存过期时间(可选) (如设置一天则 24小时内同样的链接会返回相同的短链) 单位为秒
    ```
    Slink\App::getInstance()->setOlinkCacheTtl(86400*2);
    ```        
    > 如不设置则默认缓存时间为86400秒
  + 设置返回短链长度(可选) 生成短链长度 推荐是7位 一旦项目启动禁止修改该值 7位生成的数量我62的*6*次方 最小为4
    ```
    Slink\App::getInstance()->setSlinkLen(7);
    ```                                                                                                                                                                                                                                                
  + **生成短链接实例**
    ```
        $slink = Slink\App::getInstance()->getSlink('https://www.baidu.com');
    ```
  + **短连接解析**
   ```
        $olink =Slink\App::getInstance()->getOlink('gikmor1');
   ```
   > 项目通过短链获取原始链接后,可通过302定向原始的url地址
  
> 有意见或问题可以随时联系我交流哈 qq:875167485 WeChat:yangmeng6036