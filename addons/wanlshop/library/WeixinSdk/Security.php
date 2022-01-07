<?php
namespace addons\wanlshop\library\WeixinSdk;

use fast\Http;

class Security
{
	private $appId;
	private $appSecret;
	
	public function __construct($appId = '', $appSecret = '')
	{
	    $this->appId = $appId;
		$this->appSecret = $appSecret;
	}
	
	/**
	 * 内容安全
	 * @param {string} $security 内容msg_sec_check 媒体media_check_async 图片img_sec_check
	 * @param {array} $data 内容[content] 媒体[media_url, media_type] 图片[media]
	 */
	public function check($security, $data)
	{
		$http = Http::get("https://api.weixin.qq.com/cgi-bin/token", [
			'grant_type' => 'client_credential',
			'appid'      => $this->appId,
			'secret'     => $this->appSecret
		]);
		$json = json_decode($http, true);
		if(!empty($json['access_token'])){
			$wxa = json_decode(Http::post('https://api.weixin.qq.com/wxa/'.$security.'?access_token='.$json['access_token'], json_encode($data, JSON_UNESCAPED_UNICODE)), true);
			return ['code' => $wxa['errcode'] ,'msg' => $wxa['errmsg']];
		}else{
			return ['code' => $json['errcode'] ,'msg' => '内容审核失败：可能后台小程序的appid、appsecret配置错误，具体：'. $json['errmsg']];
		}
	}
	
	
	
	
}