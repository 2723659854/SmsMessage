<?php
declare(strict_types=1);
namespace Xiaosongshu\Message\provider;

use Xiaosongshu\Message\exception\TencentMsgException;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\Config;
use Exception;


/**
 * @purpose 阿里云短信发送
 */
class AliSmsProvider implements MessageProviderInterface
{
    /** @var string|mixed $accessKeyId 账户id */
    protected  $accessKeyId;
    /** @var string|mixed $accessKeySecret 账户秘钥 */
    protected  $accessKeySecret;
    /** @var string $phoneNumbers 用户手机号 */
    protected  $phoneNumbers;
    /** @var string|mixed $signName 短信签名 */
    protected  $signName;
    /** @var string $templateCode 短信模板 */
    protected  $templateCode;
    /** @var string $templateParam 短信内容 */
    protected Dysmsapi $templateParam;
    /** @var  $client 阿里云短信客户端 */
    protected  $client;

    /**
     * 创建客户端
     * @param object|null $object
     * @param array $config = [ 'accessKeyId'=>'', 'accessKeySecret'=>'', 'signName'=>'', 'sdkAppId'=>""];
     */
    public function __construct(?object $object,array $config=[])
    {
        $this->accessKeyId     = $config['accessKeyId'] ?? '';
        $this->accessKeySecret = $config['accessKeySecret'] ?? '';
        $this->signName=$config['signName']??"阿里云短信测试";
        $config                = new Config([
            "accessKeyId" => $this->accessKeyId,
            "accessKeySecret" => $this->accessKeySecret
        ]);
        $config->endpoint      = "dysmsapi.aliyuncs.com";
        $this->client= new Dysmsapi($config);
    }

    /**
     * 初始化配置
     * @param array $configs = [ 'accessKeyId'=>'', 'accessKeySecret'=>'', 'signName'=>'', 'sdkAppId'=>""];
     * @return object
     */
    public function config(array $configs):object{
        $this->accessKeyId     = $configs['accessKeyId'] ?? '';
        $this->accessKeySecret = $configs['accessKeySecret'] ?? '';
        $this->signName=$configs['signName']??"阿里云短信测试";
        $config                = new Config([
            "accessKeyId" => $this->accessKeyId,
            "accessKeySecret" => $this->accessKeySecret
        ]);
        $config->endpoint      = "dysmsapi.aliyuncs.com";
        $this->client= new Dysmsapi($config);
        return $this;
    }

    /**
     * 设置标题
     * @param string $title
     * @return object
     */
    public function setTitle(string $title): object
    {
        return $this;
    }

    /**
     * 设置内容
     * @param array $content =[ "code"=>999999]
     * @return object
     */
    public function setContent(array $content): object
    {
        $this->templateParam=json_encode($content);
        return $this;
    }

    /**
     * 设置模板
     * @param string $templateId="SMS_154950909"
     * @return object
     */
    public function setTemplate(string $templateId): object
    {
        $this->templateCode=$templateId?:'SMS_154950909';
        return $this;
    }

    /**
     * 接收人
     * @param array $sendTo=[13986598754,13885968748,...]
     * @return object
     */
    public function sendTo(array $sendTo): object
    {
        $this->phoneNumbers=implode(',',$sendTo);
        return $this;
    }

    /**
     * 发送消息
     * @return array=["status"=>200,"body"=>["bizId": "630111676617258274^0", "code": "OK", "message": "OK", "requestId": "C00B4D8C-9296-51BF-93F1-9F689EE607CB"]]
     */
    public function send(): array
    {
        $sendSmsRequest = new SendSmsRequest([
            'phoneNumbers'         => $this->phoneNumbers,
            'signName'             => $this->signName,
            'templateCode'         => $this->templateCode,
            'templateParam'        => $this->templateParam,
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            $response=$this->client->sendSmsWithOptions($sendSmsRequest, $runtime);
            return ['status'=>$response->statusCode,'body'=>json_decode(json_encode($response->body),true)];
        }
        catch (Exception $error) {
            throw new TencentMsgException($error->getMessage());
        }
    }
}