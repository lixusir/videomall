<?php

namespace addons\aliyunsms;

use AlibabaCloud\Client\AlibabaCloud;
use fast\Http;
use think\Addons;
use think\Exception;

/**
 * 插件
 */
class Aliyunsms extends Addons
{
    private $appkey = null;
    private $appsecret = null;
    private $regionid = null;
    private $signname = null;
    private $config = null;
    public function ConfigInit()
    {
        $this->config = $this->getConfig();

        $this->appkey = $this->config['appkey'];
        $this->appsecret = $this->config['appsecret'];
        $this->regionid = $this->config['regionid'];
        $this->signname = $this->config['signname'];
    }

    public  function curl_request($url,$postStr = ""){



        $header = array(

            'Content-Type: application/json',

        );

        $curl = curl_init($url);



        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);



        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");



        curl_setopt($curl, CURLOPT_POSTFIELDS, $postStr);



        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);



        curl_setopt($curl, CURLOPT_FAILONERROR, false);



        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);



        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);



        $response = curl_exec($curl) or die("error：".curl_errno($curl));



        curl_close($curl);



        $result = (array)json_decode($response);



        return $result;

    }

    public function smsSend(&$params){

        $smsParam= array(
            'account' => 's22050085',
            'password' => md5('B1K7Hn'),
            'mobile' => $params['mobile'],
            'content' => '【新零售商城】您的验证码是:'.$params['code'].'验证码5分钟后过期，请您及时验证！' ,
            'requestId' => '1111',
            'extno' => ''
        );

        $url = 'http://www.17int.cn/xxsmsweb/smsapi/send.json';

        $post_data = json_encode($smsParam,true);

        $list = $this->curl_request($url,$post_data);


        return true;
    }

    /**
     * 短信发送通知
     * @param array $params
     * @return  boolean
     */
    public function smsSend_1(&$params)
    {
        dump($params);die;
        $this->ConfigInit();
        AlibabaCloud::accessKeyClient($this->appkey,$this->appsecret)
            ->regionId($this->regionid)
            ->asDefaultClient();

        try {
            $templateID = $this->config['template'][$params->event];
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $this->regionid,
                        'PhoneNumbers' => $params['mobile'],
                        'SignName' => $this->signname,
                        'TemplateCode' => $templateID,
                        'TemplateParam' => json_encode(['code'=>$params['code']]),
                    ],
                ])
                ->request();
            if($result->Code == 'OK' && $result->Message == 'OK'){
                return true;
            }else{
                write_log($result->Message,'短信发送失败');
                //记录错误信息
                $this->setError($result->Message);
                return false;
            }
        } catch (Exception $e) {
            write_log($e->getMessage(),'短信发送失败');
            exit();
        }
    }
    /**
     * 短信发送通知
     * @param array $params
     * @return  boolean
     */
    public function smsNotice(&$params)
    {
        $this->ConfigInit();
        AlibabaCloud::accessKeyClient($this->appkey,$this->appsecret)
            ->regionId($this->regionid)
            ->asDefaultClient();

        try {
            $templateID = $this->config['template'][$params['template']];
            $result = AlibabaCloud::rpc()
                ->product('Dysmsapi')
                // ->scheme('https') // https | http
                ->version('2017-05-25')
                ->action('SendSms')
                ->method('POST')
                ->host('dysmsapi.aliyuncs.com')
                ->options([
                    'query' => [
                        'RegionId' => $this->regionid,
                        'PhoneNumbers' => $params['mobile'],
                        'SignName' => $this->signname,
                        'TemplateCode' => $templateID,
                        'TemplateParam' => json_encode($params['msg']),
                    ],
                ])
                ->request();
            if($result->Code == 'OK' && $result->Message == 'OK'){
                return true;
            }else{
                write_log($result->Message,'短信发送失败');
                //记录错误信息
                $this->setError($result->Message);
                return false;
            }
        } catch (Exception $e) {
            write_log($e->getErrorMessage(),'短信发送失败');
            var_dump($e);
            exit();
        }
    }
    /**
     * 检测验证是否正确
     * @param Sms $params
     * @return  boolean
     */
    public function smsCheck(&$params)
    {
        return true;
    }
    /**
     * 记录失败信息
     * @param [type] $err [description]
     */
    private function setError($err)
    {
        $this->sendError = $err;
    }

    /**
     * 获取失败信息
     * @return [type] [description]
     */
    public function getError()
    {
        return $this->sendError;
    }
    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {

        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {

        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {

        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {

        return true;
    }

    /**
     * 实现钩子方法
     * @return mixed
     */
    public function testhook($param)
    {
        // 调用钩子时候的参数信息
        print_r($param);
        // 当前插件的配置信息，配置信息存在当前目录的config.php文件中，见下方
        print_r($this->getConfig());
        // 可以返回模板，模板文件默认读取的为插件目录中的文件。模板名不能为空！
        //return $this->fetch('view/info');
    }

}
