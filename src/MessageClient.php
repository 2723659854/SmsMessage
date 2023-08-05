<?php
declare(strict_types=1);
namespace Xiaosongshu\message;

use Xiaosongshu\message\exception\TencentMsgException;
use Xiaosongshu\message\provider\AliSmsProvider;
use Xiaosongshu\message\provider\TencentEmailProvider;
use Xiaosongshu\message\provider\TencentSmsProvider;

/**
 * @purpose 发送通知消息客户端
 * @author yanglong
 * @date 2023年2月17日15:42:51
 * @example 阿里云短信发送 (new MessageClient([]))->Asms->setTemplate("SMS_154950909")->setConTent(['code' => rand(100000,999999)])->sendTo(['1398xxxx423'])->send();
 * @property TencentEmailProvider $Temail
 * @property TencentSmsProvider $Tsms
 * @property AliSmsProvider $Asms
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
            throw new TencentMsgException("{$name} is invalid.");
        }

        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }
        $class = $this->alias[$name];
        return $this->providers[$name] = $this->configs ?
            new $class($this, $this->configs) :
            new $class($this);
    }
}