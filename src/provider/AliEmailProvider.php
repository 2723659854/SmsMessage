<?php
declare(strict_types=1);
namespace Xiaosongshu\Message\provider;

use Xiaosongshu\Message\exception\TencentMsgException;
use AlibabaCloud\SDK\Dm\V20151123\Dm;
use \Exception;
use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dm\V20151123\Models\SingleSendMailRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;

/**
 * @purpose 阿里云邮件发送提供者
 */
class AliEmailProvider implements MessageProviderInterface
{

    /** 账户id */
    protected  $accessKeyId;
    /** 账户秘钥 */
    protected  $accessKeySecret;
    /** 发件地址 */
    protected  $accountName;
    /** 发送邮件请求 */
    protected  $singleSendMailRequest;
    /** 阿里发送邮件客户端 */
    protected  $client;
    /** 收件人地址 */
    protected  $sendTo;
    /** 邮件主题 */
    protected  $title;
    /** 邮件内容 */
    protected  $content;

    /**
     * 初始化
     * @param object|null $object
     * @param array $configs = [ 'accessKeyId'=>'', 'accessKeySecret'=>'', 'fromAddress'=>'']
     */
    public function __construct(?object $object,array $configs=[]){
        $this->accessKeyId = $configs['accessKeyId']??'';
        $this->accessKeySecret = $configs['accessKeySecret']??'';
        $this->accountName=$configs['fromAddress']??'';
        $config = new Config([
            "accessKeyId" => $this->accessKeyId,
            "accessKeySecret" => $this->accessKeySecret
        ]);
        $config->endpoint = "dm.aliyuncs.com";
        $this->client= new Dm($config);
    }

    /**
     * 初始化包配置，用于静态化调用
     * @param array $configs = [ 'accessKeyId'=>'', 'accessKeySecret'=>'', 'fromAddress'=>'']
     * @return object
     */
    public function config(array $configs):object{
        $this->accessKeyId = $configs['accessKeyId']??'';
        $this->accessKeySecret = $configs['accessKeySecret']??"";
        $this->accountName=$configs['fromAddress']??'';
        $config = new Config([
            "accessKeyId" => $this->accessKeyId,
            "accessKeySecret" => $this->accessKeySecret
        ]);
        $config->endpoint = "dm.aliyuncs.com";
        $this->client= new Dm($config);
        return $this;
    }
    /**
     * 设置标题
     * @param string $title
     * @return object
     */
    public function setTitle(string $title):object{
        $this->title=$title;
        return $this;
    }

    /**
     * 设置内容
     * @param array $content
     * @return object
     */
    public function setContent(array $content):object{
        $this->content=implode("\r\n",$content);
        return $this;
    }

    /**
     * 设置模板
     * @param string $templateId
     * @return object
     */
    public function setTemplate(string $templateId):object{
        return $this;
    }

    /**
     * 接收人,这里是发送
     * @param array $sendTo
     * @return object
     */
    public function sendTo(array $sendTo):object{
        $this->sendTo=implode(',',$sendTo);
        return $this;
    }

    /**
     * 发送消息
     * @return array = ['status'=>200 , 'body' =>'ok' ]
     */
    public function send():array{
        $this->singleSendMailRequest = new SingleSendMailRequest([
            "accountName" => $this->accountName,
            "addressType" => 1,
            "replyToAddress" => true,
            "toAddress" => $this->sendTo,
            "subject" => $this->title,
            "textBody" => $this->content,
        ]);
        $runtime = new RuntimeOptions([]);
        try {
            $this->client->singleSendMailWithOptions($this->singleSendMailRequest, $runtime);
            return ['status'=>200,'body'=>'ok'];
        }
        catch (Exception $error) {
            throw new TencentMsgException($error->getMessage());
        }

    }
}