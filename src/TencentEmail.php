<?php
declare(strict_types=1);
namespace Xiaosongshu\Message;

use Xiaosongshu\Message\provider\TencentEmailProvider;

/**
 * @purpose 邮件发送包静态化处理
 * @method object setTemplate(string $templateId = null) 设置模板
 * @method object setContent(array $content) 设置内容
 * @method object setTitle(string $title) 设置标题
 * @method object sendTo(array $sendTo) 接收人
 * @method array send() 发送邮件
 * @author yanglong
 * @date  2023年2月17日15:22:51
 * @example $config=[ 'accessKeyId'=>'LTAI5tBMSpwR9DGfyYz1uQqW', 'accessKeySecret'=>'JdWCJl7WBpAI4ldQsmrjH4bTU9aiKA', 'signName'=>'阿里云短信测试','sdkAppId'=>"1400383641" ];
 * @example TencentEmail::init($config)->setTemplate()->setTitle($param['title'])->setConTent(['username' => $param['username']])->sendTo([$param['email']])->send();
 */
class TencentEmail
{
    /** @var TencentEmailProvider $instances 已实例化的类 */
    protected static TencentEmailProvider $instances;

    /**
     * 获取静态实例
     * @return object
     */
    protected static function getInstance():object
    {
        if (!isset(static::$instances) ) {
            /** @var object $instances 实例化*/
            static::$instances = new TencentEmailProvider(new \stdClass(),[]);
        }
        return static::$instances;
    }

    /**
     * 调用邮件方法
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return static::getInstance()->{$name}(...$arguments);
    }
}