<?php
declare(strict_types=1);

namespace Xiaosongshu\Message;

use Xiaosongshu\Message\exception\TencentMsgException;
use Xiaosongshu\Message\provider\AliEmailProvider;
use Xiaosongshu\Message\provider\AliSmsProvider;
use Xiaosongshu\Message\provider\MessageProviderInterface;
use Xiaosongshu\Message\provider\TencentEmailProvider;
use Xiaosongshu\Message\provider\TencentSmsProvider;
use Xiaosongshu\Message\provider\ThinkSmsProvider;

/**
 * @purpose 发送通知消息客户端
 * @property TencentEmailProvider $Temail 腾讯邮箱
 * @property TencentSmsProvider $Tsms 腾讯短信
 * @property AliSmsProvider $Asms 阿里短信
 * @property AliEmailProvider $Aemail 阿里邮箱
 * @property ThinkSmsProvider $ThinkSms 顶想云短信
 */
class MessageClient
{
    /** @var array|string[] */
    protected static  $alias = [
        /** 腾讯邮箱 */
        'Temail' => TencentEmailProvider::class,
        /** 腾讯短信 */
        'Tsms' => TencentSmsProvider::class,
        /** 阿里短信 */
        'Asms' => AliSmsProvider::class,
        /** 阿里邮件 */
        'Aemail' => AliEmailProvider::class,
        /** 顶想云短信 */
        'ThinkSms' => ThinkSmsProvider::class,
    ];

    /** @var array */
    protected static  $providers = [];

    /** @var array */
    public static $configs = [];

    /**
     * 初始化配置
     * @param array|null $configs
     */
    public function __construct(?array $configs = null)
    {
        static::$configs = $configs ?? [];
    }

    /**
     * 动态调用
     * @param string $name
     * @return object
     */
    public function __get(string $name): object
    {
        if (!isset($name) || !isset(static::$alias[$name])) {
            throw new TencentMsgException("[$name]不合法，或者服务不存在");
        }

        if (isset(static::$providers[$name])) {
            return static::$providers[$name];
        }
        $class = static::$alias[$name];
        return static::$providers[$name] = static::$configs ?
            new $class($this, static::$configs) :
            new $class($this);
    }

    /**
     * 被静态化调用
     * @param string $name
     * @param array $params
     * @return object
     */
    public static function __callStatic(string $name, array $params): object
    {
        if (!isset($name) || !isset(static::$alias[$name])) {
            throw new TencentMsgException("[$name]不合法，或者服务不存在");
        }

        if (isset(static::$providers[$name])) {
            return static::$providers[$name];
        }
        $class = static::$alias[$name];
        return static::$providers[$name] = static::$configs ?
            new $class(new \stdClass(), static::$configs) :
            new $class(new \stdClass());
    }

    /**
     * 静态注册服务
     * @param string $key
     * @param string $value
     * @return void
     * @throws \ReflectionException
     */
    public static function register(string $key, string $value): void
    {
        if (class_exists($value)) {
            $class = new \ReflectionClass($value);
            if ($class->newInstance(null) instanceof MessageProviderInterface){
                static::$alias[$key] = $value;
            } else {
                throw new TencentMsgException("[$value]必须继承接口" . MessageProviderInterface::class);
            }
        } else {
            throw new TencentMsgException("注册服务失败：[$value] 不存在");
        }
    }

    /**
     * 动态注册服务
     * @param string $key
     * @param string $value
     * @return void
     */
    public function bind(string $key, string $value): void
    {
        static::register($key,$value);
    }
}