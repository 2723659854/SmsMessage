<?php

namespace Xiaosongshu\Message\provider;
use think\api\Client;
use Xiaosongshu\Message\exception\TencentMsgException;

/**
 * 顶想云短信发送
 */
class ThinkSmsProvider implements MessageProviderInterface
{

    /** @param Client $client 顶想云客户端 */
    protected Client $client ;

    /** @var string|mixed $accessKeyId 账户id */
    protected string $accessKeyId;

    /** @var string $phoneNumbers 用户手机号 */
    protected string $phoneNumbers;

    /** @var string|mixed $signName 短信签名 */
    protected string $signName;

    /** @var string $TemplateId 短信模板 */
    protected string $TemplateId;

    /** @var string $templateParam 短信内容 */
    protected string $templateParam;

    /**
     * 初始化配置
     * @param object|null $object
     * @param array $configs = ['accessKeyId'=>'', 'accessKeySecret'=>'', 'signName'=>'', 'sdkAppId'=>""]
     */
    public function __construct(?object $object,array $configs = []){

        $this->accessKeyId = $configs['accessKeyId']??"";
        $this->signName = $configs['signName']??"";
        $this->client = new Client($this->accessKeyId);
    }

    /**
     * 动态配置客户端
     * @param array $configs = ['accessKeyId'=>'', 'accessKeySecret'=>'', 'signName'=>'', 'sdkAppId'=>""]
     * @return object|$this
     */
    public function config(array $configs):object
    {
        $this->accessKeyId = $configs['accessKeyId'];
        $this->signName = $configs['signName'];
        $this->client = new Client($this->accessKeyId);
        return $this;
    }
    /**
     * 设置标题
     * @param string $title
     * @return object
     */
    public function setTitle(string $title):object{
        return $this;
    }

    /**
     * 设置内容
     * @param array $content
     * @return object
     */
    public function setContent(array $content):object{
        $this->templateParam = json_encode($content);
        return $this;
    }

    /**
     * 设置模板
     * @param string $templateId
     * @return object
     */
    public function setTemplate(string $templateId):object{
        $this->TemplateId = $templateId;
        return $this;
    }

    /**
     * 接收人
     * @param array $sendTo
     * @return object
     */
    public function sendTo(array $sendTo):object{
        $this->phoneNumbers = implode(',',$sendTo);
        return $this;
    }

    /**
     * 发送消息
     * @return array
     */
    public function send():array{

        if (empty($this->client)){
            throw new TencentMsgException("没有实例化客户端",500);
        }

        try {
            if (empty($this->templateParam)){
                return $this->client->smsBatchSend()
                    ->withSignId($this->signName)
                    ->withTemplateId($this->TemplateId)
                    ->withPhone($this->phoneNumbers)
                    ->request();
            }else{
                return $this->client->smsBatchSend()
                    ->withSignId($this->signName)
                    ->withTemplateId($this->TemplateId)
                    ->withPhone($this->phoneNumbers)
                    ->withParams($this->templateParam)
                    ->request();
            }
        }catch (\Exception $e){
            throw new TencentMsgException($e->getMessage());
        }

    }
}