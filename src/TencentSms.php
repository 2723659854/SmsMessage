<?php
declare(strict_types=1);
namespace Xiaosongshu\Message;

use Xiaosongshu\Message\provider\TencentSmsProvider;

/**
 * @purpose 短信发送包静态化处理
 * @method object init(array $config) 配置服务
 * @method object setTemplate(string $templateId = null) 设置模板
 * @method object setContent(array $content) 设置内容
 * @method object setTitle(string $title) 设置标题
 * @method object sendTo(array $sendTo) 接收人
 * @method array  send() 发送短信
 * @author yanglong
 * @date  2023年2月17日15:22:51
 * @example $config=[ 'accessKeyId'=>'LTAI5tBMSpwR9DGfyYz1uQqW', 'accessKeySecret'=>'JdWCJl7WBpAI4ldQsmrjH4bTU9aiKA', 'signName'=>'阿里云短信测试','sdkAppId'=>"1400383641" ];
 * @example TencentSms::init($config)->setTemplate()->setContent([rand(1000, 9999)])->sendTo([$param['phone']])->send();
 */
class TencentSms
{
    /** @var TencentSmsProvider $instances 已实例化的类 */
    protected static TencentSmsProvider $instances;

    /**
     * 获取静态实例
     * @return object
     */
    protected static function getInstance():object
    {
        if (!isset(static::$instances) ) {
            /** @var object $instances 实例化*/
            static::$instances = new TencentSmsProvider(new \stdClass(),[]);
        }
        return static::$instances;
    }

    /**
     * 调用短息方法
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return static::getInstance()->{$name}(...$arguments);
    }
}