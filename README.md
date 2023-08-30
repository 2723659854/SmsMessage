### 短信和邮件发送

#### 安装

```bash 
composer require xiaosongshu/message
```

#### 用法

```php 

use Xiaosongshu\Message\AliEmail;
use Xiaosongshu\Message\TencentEmail;
use Xiaosongshu\Message\TencentSms;
use Xiaosongshu\Message\MessageClient;
use Xiaosongshu\Message\AliSms;


        /** 阿里云短信发送配置 */
        $configASMS=[
            'accessKeyId'=>'',
            'accessKeySecret'=>'',
            'signName'=>'',
            'sdkAppId'=>""
        ];
        $res=(new MessageClient($configASMS))->Asms->setTemplate("SMS_154950909")->setConTent(['code' => rand(100000,999999)])->sendTo([''])->send();
        $res = AliSms::config($configASMS)->setTemplate("SMS_154950909")->setConTent(['code' => rand(100000,999999)])->sendTo([''])->send();
        $res = MessageClient::Asms()->config($configASMS)->setTemplate("SMS_154950909")->setConTent(['code' => rand(100000,999999)])->sendTo([''])->send();

        
        /** 阿里云邮件发送配置 */
        $configAEmail=[
            'accessKeyId'=>'',
            'accessKeySecret'=>'',
            'fromAddress'=>''
        ];
        $res = (new MessageClient($configAEmail))->Aemail->setTitle('标题')->setContent(['你好呀'])->sendTo([''])->send();
        $res = AliEmail::config($configAEmail)->setTitle('标题')->setContent(['你好呀'])->sendTo([''])->send();
        $res = MessageClient::Aemail()->config($configAEmail)->setTitle('标题')->setContent(['你好呀'])->sendTo([''])->send();


        /** 腾讯短信发送配置 */
        $config=[
            'accessKeyId'=>'',
            'accessKeySecret'=>'',
            'signName'=>"",
            'sdkAppId'=>""
        ];
        $res=(new MessageClient($config))->Tsms->setTemplate("1430565")->setConTent(['code' => rand(100000,999999)])->sendTo([''])->send();
        $res=TencentSms::config($config)->setTemplate("1430565")->setConTent(['code' => rand(100000,999999)])->sendTo([''])->send();
        $res=MessageClient::Tsms()->config($config)->setTemplate("1430565")->setConTent(['code' => rand(100000,999999)])->sendTo([''])->send();
        /** 腾讯邮件发送配置 */
        $config= [
            'accessKeyId'=>'',
            'accessKeySecret'=>'',
            'fromAddress'=>''
        ];
        $res=(new MessageClient($config))->Temail->setTemplate()->setTitle('恭喜发财')->setConTent(['username' => '牡丹花'])->sendTo([''])->send();
        $res = TencentEmail::config($config)->setTemplate()->setTitle('恭喜发财')->setConTent(['username' => '牡丹花'])->sendTo([''])->send();
        $res=MessageClient::Temail()->config($config)->setTemplate()->setTitle('恭喜发财')->setConTent(['username' => '躺不平，摆不烂'])->sendTo([''])->send();
      
```
####对服务进行扩展
```php 
        $config= [
            'accessKeyId'=>'',
            'accessKeySecret'=>'',
            'fromAddress'=>''
        ];
       /** 对服务进行扩展：只在程序当前生命周期内有效。 */
        $factory = new MessageClient();
        /** 注册其他的消息服务类 例如Email第三方发送邮件服务,当然Email必须继承接口 Xiaosongshu\Message\provider\MessageProviderInterface */
        $factory->bind('other',Email::class);
        /** 调用已注册的服务 */
        $factory->other->config($config)->setTemplate()->setTitle('测试')->setConTent(['content' => '对服务进行扩展呢'])->sendTo(['xxx@qq.com'])->send();

        /** 静态注册  */
        MessageClient::register('other',Email::class);
        /** 静态调用 */
        MessageClient::other()->config($config)->setTemplate()->setTitle('恭喜发财')->setConTent(['username' => '躺不平，摆不烂'])->sendTo([''])->send();  
```
注册的扩展服务只在当前生命周期内有效。

##### 异常捕获
catch Xiaosongshu\Message\exception\TencentMsgException

#####
联系作者：2723659854@qq.com