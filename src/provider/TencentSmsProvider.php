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
    protected Credential $credential;
    /** @var SendSmsRequest 短信发送请求 */
    protected SendSmsRequest $request;
    /** 服务id */
    /** @var string|mixed */
    protected string $secretId;
    /** @var string|mixed 服务key */
    protected string $secretKey;
    /** @var string|mixed SDK的appid */
    protected string $sdkAppId;
    /** @var string|mixed 短信签名 */
    protected string $signName;
    /** @var string 模板id */
    protected string $templateId ;
    /** @var string 服务器大区 */
    protected string $region = 'ap-guangzhou';

    /**
     * 发送短信
     * @author yanglong
     * @date  2023年2月17日15:22:51
     * @example 用法：
     * $config=[ 'accessKeyId'=>'LTAI5tBMSpwR9DGfyYz1uQqW', 'accessKeySecret'=>'JdWCJl7WBpAI4ldQsmrjH4bTU9aiKA', 'signName'=>'阿里云短信测试','sdkAppId'=>"1400383641" ];
     * $res=(new MessageClient($config))->Tsms->setTemplate("SMS_154950909")->setConTent(['code' => rand(100000,999999)])->sendTo([$request->post('phone')])->send();
     */
    public function __construct(object $object,array $config=[])
    {
        $this->secretId   = $config['accessKeyId']??"";
        $this->secretKey  = $config['accessKeySecret']??'';
        $this->sdkAppId   = $config['sdkAppId']??"1400383641";
        $this->signName   = $config['signName']??"兰台的小家网";
        $credential       = new Credential($this->secretId, $this->secretKey);
        $this->credential = $credential;
        $request          = new SendSmsRequest();
        $request->setSmsSdkAppId($this->sdkAppId);
        $request->setSignName($this->signName);
        $this->request = $request;
        return $this;
    }

    /** 初始化包信息，用于静态化调佣 */
    public function init(array $configs): object
    {
        $this->secretId   = $configs['accessKeyId']??"";
        $this->secretKey  = $configs['accessKeySecret']??'';
        $this->sdkAppId   = $configs['sdkAppId']??"1400383641";
        $this->signName   = $configs['signName']??"兰台的小家网";
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
     * @return array
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