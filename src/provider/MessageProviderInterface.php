<?php
declare(strict_types=1);
namespace Xiaosongshu\message\provider;

/**
 * @purpose 通知消息模板接口
 * @author yanglong
 * @date  2023年2月17日15:22:51
 */
interface MessageProviderInterface
{

    /**
     * 初始化包配置，用于静态化调用
     * @param array $configs
     * @return object
     */
    public function init(array $configs):object;
    /**
     * 设置标题
     * @param string $title
     * @return object
     */
    public function setTitle(string $title):object;

    /**
     * 设置内容
     * @param array $content
     * @return object
     */
    public function setContent(array $content):object;

    /**
     * 设置模板
     * @param string $templateId
     * @return object
     */
    public function setTemplate(string $templateId):object;

    /**
     * 接收人
     * @param array $sendTo
     * @return object
     */
    public function sendTo(array $sendTo):object;

    /**
     * 发送消息
     * @return array
     */
    public function send():array;
}