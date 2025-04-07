<?php
declare(strict_types=1);

namespace Xiaosongshu\Message\provider;

use Xiaosongshu\Message\exception\TencentMsgException;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\SmsClient;

/**
 * @purpose 腾讯短信发送包
 * @author yanglong
 * @date  2023年2月17日15:22:51
 */
class TencentSmsProvider implements MessageProviderInterface
{
    /** @var Credential 签名生成器 */
    protected  $credential;
    /** @var SendSmsRequest 短信发送请求 */
    protected  $request;
    /** 服务id */
    /** @var string|mixed */
    protected  $secretId;
    /** @var string|mixed 服务key */
    protected  $secretKey;
    /** @var string|mixed SDK的appid */
    protected  $sdkAppId;
    /** @var string|mixed 短信签名 */
    protected  $signName;
    /** @var string 模板id */
    protected  $templateId ;
    /** @var string 服务器大区 */
    protected  $region = 'ap-guangzhou';

    /**
     * 发送短信
     * @author yanglong
     * @date  2023年2月17日15:22:51
     */
    public function __construct(object $object,array $config=[])
    {
        $this->secretId   = $config['accessKeyId']??"";
        $this->secretKey  = $config['accessKeySecret']??'';
        $this->sdkAppId   = $config['sdkAppId']??"";
        $this->signName   = $config['signName']??"";
        $credential       = new Credential($this->secretId, $this->secretKey);
        $this->credential = $credential;
        $request          = new SendSmsRequest();
        $request->setSmsSdkAppId($this->sdkAppId);
        $request->setSignName($this->signName);
        $this->request = $request;
        return $this;
    }

    /**
     * 初始化配置
     * @param array $configs = [ 'accessKeyId'=>'', 'accessKeySecret'=>'', 'signName'=>'','sdkAppId'=>"" ];
     * @return object
     */
    public function config(array $configs): object
    {
        $this->secretId   = $configs['accessKeyId']??"";
        $this->secretKey  = $configs['accessKeySecret']??'';
        $this->sdkAppId   = $configs['sdkAppId']??"";
        $this->signName   = $configs['signName']??"";
        $credential       = new Credential($this->secretId, $this->secretKey);
        $this->credential = $credential;
        $request          = new SendSmsRequest();
        $request->setSmsSdkAppId($this->sdkAppId);
        $request->setSignName($this->signName);
        $this->request = $request;
        return $this;
    }

    /**
     * 接收短信电话号码组
     * @param array $sendTo =['+8613983436511','+8613983436511',...]
     * @return object
     */
    public function sendTo(array $sendTo): object
    {
        $this->request->setPhoneNumberSet($sendTo);
        return $this;
    }

    /**
     * 设置模板id
     * @param string|null $templateId
     * @return object
     */
    public function setTemplate(string $templateId = null): object
    {
        $this->templateId=$templateId?:'1430565';
        $this->request->setTemplateId($this->templateId);
        return $this;
    }

    /**
     * 设置短信模板中的变量
     * @param array $content =['value1','value2',...]
     * @return object
     */
    public function setContent(array $content): object
    {
        foreach ($content as &$v) {
            $v = (string)$v;
        }
        $this->request->setTemplateParamSet(array_values($content));
        return $this;
    }

    /**
     * 设置标题
     * @param string $title
     * @return object
     */
    public function setTitle(string $title): object
    {
        $this->request->setSignName($this->signName);
        return $this;
    }

    /**
     * 发送短信
     * @return array = ['status'=>200 , 'body'=>'ok']
     */
    public function send(): array
    {
        try {
            $msgClient = new SmsClient($this->credential, $this->region);
            $content   = json_decode(json_encode($msgClient->SendSms($this->request)->SendStatusSet),true)[0];
            if ($content['Code']=='Ok'){
                return ['status'=>200,'body'=>$content['Message']];
            }else{
                return ['status'=>400,'body'=>$content['Message']];
            }
        } catch (TencentCloudSDKException $exception) {
            throw new TencentMsgException($exception->getMessage());
        }
    }
}