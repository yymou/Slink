# Slink
利用redis实现的常用的短链服务, 支持redis一主多从配置, 速度快且运行稳定(建议开启redis持久化), 能满足亿级别的链接转化需求.
底层使用redis发号器递增转换成62进制,但根据简单算法实现了短链接非递增返回. 解析短链依托redis,速度快.

#### 使用:
+ 1 引入composer
  + 在composer.json文件中添加
    + `"psr-4": {
        "Slink\\": "src/"
       }`
  + 执行 `composer update`
  + 项目中引入composer `require 'vendor/autoload.php';`
  
+ 2 配置
  + 在根目录env.php文件中配置redis 支持一主多从
  
+ 3 项目中使用
  + 生成短链接实例 `Slink\App::getInstance()->getSlink('www.baidu.com')`
  + 短连接解析 `Slink\App::getInstance()->getOlink('oruxC52')`
  
> 有意见或问题可以随时联系我交流哈 qq:875167485 WeChat:yangmeng6036