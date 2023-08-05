<?php
declare(strict_types=1);

namespace Xiaosongshu\message\provider;

use Xiaosongshu\message\exception\TencentMsgException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Ses\V20201002\Models\SendEmailRequest;
use TencentCloud\Ses\V20201002\Models\Template;
use TencentCloud\Ses\V20201002\SesClient;

/**
 * @purpose 集成腾讯邮件发送
 * @author yanglong
 * @date 2023年2月17日15:22:51
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
     *  发送邮件
     * @author yanglong
     * @date 2023年2月17日15:22:51
     * @example 用法(链式操作):
     *  $response=TencentEmail::init($config=[])->setTemplate()->setTitle('恭喜发财')->setConTent(['username' => '牡丹花'])->setTo([$emailAddress])->send();
     */
    public function __construct(object $object,array $config=[])
    {
        $this->secretId    = $config['secretId'];
        $this->secretKey   = $config['secretKey'];
        $this->fromAddress = $config['fromAddress'];
        $Credential        = new Credential($this->secretId, $this->secretKey);
        $this->Credential  = $Credential;
        $sendRequest       = new SendEmailRequest();
        $sendRequest->setFromEmailAddress($this->fromAddress);
        $this->sendRequest = $sendRequest;
        $this->template    = new Template();
        return $this;
    }

    /** 初始化包信息，用于静态化调用 */
    public function init(array $configs): object
    {
        $this->secretId    = $configs['secretId'];
        $this->secretKey   = $configs['secretKey'];
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
     * @return array
     */
    public function send(): array
    {
        try {
            $client = new SesClient($this->Credential, $this->region);
            $this->sendRequest->setTemplate($this->template);
            return [json_decode(json_encode($client->SendEmail($this->sendRequest)), true)];
        } catch (TencentCloudSDKException $exception) {
            throw new TencentMsgException($exception->getMessage());
        }
    }
}