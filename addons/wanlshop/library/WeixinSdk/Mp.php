<?php
namespace addons\wanlshop\library\WeixinSdk;

use fast\Http;

class Mp
{
	public static function config()
	{
	    $config = get_addon_config('wanlshop');
	
	    return [
	        /**
	         * 账号基本信息，请从微信公众平台/开放平台获取
	         */
	        'app_id'        => $config['sdk_qq']['gz_appid'], // AppID
	        'secret'        => $config['sdk_qq']['gz_secret'], // AppSecret
	        'token'         => $config['sdk_qq']['gz_token'], // Token
	        'aes_key'       => $config['sdk_qq']['gz_aeskey'], // EncodingAESKey，安全模式下请一定要填写！！！
	
	        /**
	         * 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
	         * 使用自定义类名时，构造函数将会接收一个 `EasyWeChat\Kernel\Http\Response` 实例
	         */
	        'response_type' => 'array',
	
	        /**
	         * 日志配置
	         *
	         * level: 日志级别, 可选为：debug/info/notice/warning/error/critical/alert/emergency
	         * path：日志文件位置(绝对路径!!!)，要求可写权限
	         */
	        'log'           => [
	            'default'  => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
	            'channels' => [
	                // 测试环境
	                'dev'  => [
	                    'driver' => 'single',
	                    'path'   => ROOT_PATH . '/runtime/log/wanlmp/wechat.log',
	                    'level'  => 'debug',
	                ],
	                // 生产环境
	                'prod' => [
	                    'driver' => 'daily',
	                    'path'   => ROOT_PATH . '/runtime/log/wanlmp/wechat.log',
	                    'level'  => $config['sdk_qq']['gz_loglevel'],
	                ],
	            ],
	        ],
	
	        /**
	         * 接口请求相关配置，超时时间等，具体可用参数请参考：
	         * http://docs.guzzlephp.org/en/stable/request-config.html
	         *
	         * - retries: 重试次数，默认 1，指定当 http 请求失败时重试的次数。
	         * - retry_delay: 重试延迟间隔（单位：ms），默认 500
	         * - log_template: 指定 HTTP 日志模板，请参考：https://github.com/guzzle/guzzle/blob/master/src/MessageFormatter.php
	         */
	        'http'          => [
	            'max_retries' => 1,
	            'retry_delay' => 500,
	            'timeout'     => 5.0,
	            // 'base_uri' => 'https://api.weixin.qq.com/', // 如果你在国外想要覆盖默认的 url 的时候才使用，根据不同的模块配置不同的 uri
	        ],
	
	        /**
	         * OAuth 配置
	         *
	         * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
	         * callback：OAuth授权完成后的回调页地址
	         */
	        'oauth'         => [
	            'scopes'   => ['snsapi_userinfo'],
	            'callback' => $config['ini']['appurl']. $config['sdk_qq']['gz_callback'],
	        ],
	    ];
	}
	
	public static function appConfig()
	{
		return [];
	}
	
	
	public static function pay()
	{
	    $config = get_addon_config('wanlshop');
	
	    return [
	        /**
	         * 账号基本信息，请从微信公众平台/开放平台获取
	         */
	        'app_id'        => $config['sdk_qq']['gz_appid'], // AppID
			'mch_id'        => $config['sdk_qq']['mch_id'],
			'key'           => $config['sdk_qq']['key'],   // API 密钥

			// 如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)
			// 'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
			// 'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！

			'notify_url'    => $config['ini']['appurl']. $config['sdk_qq']['gz_notify_url'],     // 你也可以在下单时单独设置来想覆盖它
	    ];
	}
	
	/**
	 * 获取Token
	 */
	public static function getAccessToken()
	{
	    $token = Session::get('wanlwechat_access_token');
	    if (!$token) {
	        $config = get_addon_config('wanlshop');
	        $params = [
	            'grant_type' => 'client_credential',
	            'appid'      => $config['sdk_qq']['gz_appid'],
	            'secret'     => $config['sdk_qq']['gz_secret'],
	        ];
	        $url = "https://api.weixin.qq.com/cgi-bin/token";
	        $result = Http::sendRequest($url, $params, 'GET');
	        if ($result['ret']) {
	            $msg = (array)json_decode($result['msg'], true);
	            if (isset($msg['access_token'])) {
	                $token = $msg['access_token'];
	                Session::set('wanlwechat_access_token', $token, $msg['expires_in'] - 1);
	            }
	        }
	    }
	    return $token;
	}
	
}