<?php
declare(strict_types=1);
namespace Xiaosongshu\Message;

use Xiaosongshu\Message\exception\TencentMsgException;
use Xiaosongshu\Message\provider\AliEmailProvider;
use Xiaosongshu\Message\provider\AliSmsProvider;
use Xiaosongshu\Message\provider\MessageProviderInterface;
use Xiaosongshu\Message\provider\TencentEmailProvider;
use Xiaosongshu\Message\provider\TencentSmsProvider;

/**
 * @purpose 发送通知消息客户端
 * @author yanglong
 * @date 2023年2月17日15:42:51
 * @example 阿里云短信发送 (new MessageClient([]))->Asms->setTemplate("SMS_154950909")->setConTent(['code' => rand(100000,999999)])->sendTo(['1398xxxx423'])->send();
 * @property TencentEmailProvider $Temail 腾讯邮箱
 * @property TencentSmsProvider $Tsms 腾讯短信
 * @property AliSmsProvider $Asms 阿里短信
 * @property AliEmailProvider $Aemail 阿里邮箱
 */
class MessageClient
{
    /** @var array|string[] */
    protected array $alias = [
        /** 腾讯邮箱 */
        'Temail' => TencentEmailProvider::class,
        /** 腾讯短信 */
        'Tsms' => TencentSmsProvider::class,
        /** 阿里短信 */
        'Asms'=>AliSmsProvider::class,
        /** 阿里邮件 */
        'Aemail' =>AliEmailProvider::class
    ];

    /** @var array */
    protected array $providers = [];

    /** @var array */
    public array $configs = [];

    /**
     * 初始化配置
     * @param array|null $configs
     */
    public function __construct(?array $configs = null)
    {
        $this->configs = $configs ?? [];
    }

    /**
     * 调用提供者
     * @param string $name
     * @return object
     */
    public function __get(string $name):object
    {
        if (!isset($name) || !isset($this->alias[$name])) {
            throw new TencentMsgException("[$name]不合法，或者服务不存在");
        }

        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }
        $class = $this->alias[$name];
        return $this->providers[$name] = $this->configs ?
            new $class($this, $this->configs) :
            new $class($this);
    }

    /**
     * 注册服务
     * @param string $key
     * @param string $value
     * @return void
     */
    public  function register(string $key, string $value): void
    {
        if (class_exists($value)) {
            $obj = new $value;
            if ($obj instanceof MessageProviderInterface) {
                $this->alias[$key] = $value;
            }else{
                throw new TencentMsgException("[$value]必须继承接口".MessageProviderInterface::class);
            }
        }else{
            throw new TencentMsgException("注册服务失败：[$value] 不存在");
        }
    }
}