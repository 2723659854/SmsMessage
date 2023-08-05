<?php
declare(strict_types=1);
namespace Xiaosongshu\Message;

use Xiaosongshu\Message\provider\AliEmailProvider;

/**
 * @purpose 阿里邮件客户端静态化调用
 * @method object config(array $config) 配置服务
 * @method object setContent(array $content) 设置内容
 * @method object setTitle(string $title) 设置标题
 * @method object sendTo(array $sendTo) 接收人
 * @method array send() 发送邮件
 * @author yanglong
 * @date 2023年8月5日14:45:06
 */
class AliEmail
{

    /**
     * 阿里邮件客户端实例
     * @var AliEmailProvider
     */
    protected static AliEmailProvider $instance;

    /**
     * 获取阿里邮件客户端实例
     * @return object
     */
    protected static function getInstance():object
    {
        if (!isset(static::$instance)){
            static::$instance= new AliEmailProvider(new \stdClass(),[]);
        }
        return static::$instance;
    }

    /**
     * 客户端的静态调佣
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name,array $arguments){
        return static::getInstance()->{$name}(...$arguments);
    }
}