<?php

namespace Xiaosongshu\Message;

use Xiaosongshu\Message\provider\ThinkSmsProvider;

class ThinkSms
{
    /** @var ThinkSmsProvider $instances 已实例化的类 */
    protected static ThinkSmsProvider $instances;

    /**
     * 获取静态实例
     * @return object
     */
    protected static function getInstance():object
    {
        if (!isset(static::$instances) ) {
            /** @var object $instances 实例化*/
            static::$instances = new ThinkSmsProvider(new \stdClass(),[]);
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