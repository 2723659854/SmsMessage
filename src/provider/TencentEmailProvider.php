<?php
declare(strict_types=1);

namespace Xiaosongshu\Message\provider;

use Xiaosongshu\Message\exception\TencentMsgException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Ses\V20201002\Models\SendEmailRequest;
use TencentCloud\Ses\V20201002\Models\Template;
use TencentCloud\Ses\V20201002\SesClient;

/**
 * @purpose 集成腾讯邮件发送
 */
class TencentEmailProvider implements MessageProviderInterface
{

    /** @var Template 邮件模板 */
    protected Template $template;
    /** @var int 模板id 默认是注册 */
    protected int $templateId = 36959;
    /** @var Credential 签名 */
    protected Credential $Credential;
    /** @var SendEmailRequest 发送邮件请求 */
    protected SendEmailRequest $sendRequest;
    /** @var string 服务id */
    protected string $secretId;
    /** @var string 服务密钥 */
    protected string $secretKey;
    /** @var string 发件地址 */
    protected string $fromAddress;
    /** @var string 服务器大区 */
    protected string $region = 'ap-hongkong';

    /**
     * 创建客户端
     * @param object|null $object
     * @param array $config = [ 'accessKeyId'=>'', 'accessKeySecret'=>'', 'fromAddress'=>''];
     */
    public function __construct(?object $object,array $config=[])
    {
        $this->secretId    = $config['accessKeyId'];
        $this->secretKey   = $config['accessKeySecret'];
        $this->fromAddress = $config['fromAddress'];
        $Credential        = new Credential($this->secretId, $this->secretKey);
        $this->Credential  = $Credential;
        $sendRequest       = new SendEmailRequest();
        $sendRequest->setFromEmailAddress($this->fromAddress);
        $this->sendRequest = $sendRequest;
        $this->template    = new Template();
        return $this;
    }

    /**
     * 初始化配置
     * @param array $configs = [ 'accessKeyId'=>'', 'accessKeySecret'=>'', 'fromAddress'=>''];
     * @return object
     */
    public function config(array $configs): object
    {
        $this->secretId    = $configs['accessKeyId'];
        $this->secretKey   = $configs['accessKeySecret'];
        $this->fromAddress = $configs['fromAddress'];
        $Credential        = new Credential($this->secretId, $this->secretKey);
        $this->Credential  = $Credential;
        $sendRequest       = new SendEmailRequest();
        $sendRequest->setFromEmailAddress($this->fromAddress);
        $this->sendRequest = $sendRequest;
        $this->template    = new Template();
        return $this;
    }

    /**
     * 设置模板
     * @param string|null $templateId
     * @return object
     */
    public function setTemplate(string $templateId = null): object
    {
        if ($templateId) {
            $this->templateId = (int)$templateId;
        }
        $this->template->setTemplateID($this->templateId);
        return $this;
    }

    /**
     * 设置邮件标题
     * @param string $title ='标题'
     * @return object
     */
    public function setTitle(string $title): object
    {
        $this->sendRequest->setSubject($title);
        return $this;
    }

    /**
     * 设置发送内容
     * @param array $content =['username' => '牡丹花','sex'=>'男','age'=>'15',...]
     * @return object
     */
    public function setConTent(array $content): object
    {
        $this->template->setTemplateData(json_encode($content));
        return $this;
    }

    /**
     * 设置收件人
     * @param array $sendTo =['xx@qq.com','yy@qq.com',...]
     * @return object
     */
    public function sendTo(array $sendTo): object
    {
        $this->sendRequest->setDestination($sendTo);
        return $this;
    }

    /**
     * 发送邮件
     * @return array = ['status'=>200 , 'body'=>'ok' ]
     */
    public function send(): array
    {
        try {
            $client = new SesClient($this->Credential, $this->region);
            $this->sendRequest->setTemplate($this->template);
            $client->SendEmail($this->sendRequest);
            return ['status'=>200,'body'=>'ok'];
        } catch (TencentCloudSDKException $exception) {
            throw new TencentMsgException($exception->getMessage());
        }
    }
}