<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
/**
 *	微信开放平台PHP-SDK, ThinkPHP5实例
  *  @link http://git.oschina.net/uctoo/uctoo
 *  @version 1.0
 *  usage:
 *   $options = array(
 *			'token'=>'tokenaccesskey', //填写你设定的key
 *			'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey
 *			'appid'=>'wxdk1234567890', //填写高级调用功能的app id
 *			'appsecret'=>'xxxxxxxxxxxxxxxxxxx' //填写高级调用功能的密钥
 *		);
 *	 $weObj = new TPWechatOpen($options);
 *   $weObj->getAuthorizerInfo($appid);
 *   ...
 *
 */
namespace com;
use think\Db;
use think\Request;
use app\common\model\WechatApplet;

class TPWechatOpen extends WechatOpen
{

    public function __construct($options)
    {
        parent::__construct($options);

        $model = model('mpopen');
        $component = $model->where('status', 1)->find(); //数据库中保存的第三方平台信息
        $options['token'] = Db::name('mpopen')->where(['id' => 1])->value('token');
        $options['component_appid'] = $component['appid'];    //初始化options信息
        $options['component_appsecret'] = $component['appsecret'];
        $options['component_access_token'] = $component['component_access_token'];
        $options['encodingaeskey'] = $component['encodingAesKey'];
        $options['debug'] = config('app_debug');            //调试状态跟随系统调试状态
        if($options['debug']){
            $options['logcallback'] = 'trace';              //微信类调试信息用trace方法记录到TP日志文件中
        }

        $this->token = isset($options['token'])?$options['token']:'';
        $this->encodingAesKey = isset($options['encodingaeskey'])?$options['encodingaeskey']:'';
        $this->appid = isset($options['appid'])?$options['appid']:'';
        $this->appsecret = isset($options['appsecret'])?$options['appsecret']:'';
        $this->component_appid = isset($options['component_appid'])?$options['component_appid']:'';
        $this->component_appsecret = isset($options['component_appsecret'])?$options['component_appsecret']:'';
        $this->component_access_token = isset($options['component_access_token'])?$options['component_access_token']:'';
        $this->debug = isset($options['debug'])?$options['debug']:false;
        $this->logcallback = isset($options['logcallback'])?$options['logcallback']:false;
        $this->encrypt_type = 'aes';
    }

    //TP5 依赖注入的自动实例化方法

    public static function invoke()
    {
        $model = model('mpopen');
        $component = $model->where('status', 1)->find(); //数据库中保存的第三方平台信息
        $options['token'] = Db::name('mpopen')->where(['id' => 1])->value('token');
        $options['component_appid'] = $component['appid'];    //初始化options信息
        $options['component_appsecret'] = $component['appsecret'];
        $options['component_access_token'] = $component['component_access_token'];
        $options['encodingaeskey'] = $component['encodingAesKey'];
        $options['debug'] = config('app_debug');            //调试状态跟随系统调试状态
        if($options['debug']){
            $options['logcallback'] = 'trace';              //微信类调试信息用trace方法记录到TP日志文件中
        }
        $weObj = new TPWechatOpen($options);
        return $weObj;
    }

	/**
	 * 重载设置缓存
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		return cache($cachename,$value,$expired);
	}

	/**
	 * 重载获取缓存
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		return cache($cachename);
	}

	/**
	 * 重载清除缓存
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return cache($cachename,null);
	}



    /**
     * 获取access_token，5、获取（刷新）授权公众号或小程序的接口调用凭据（令牌），重写父类方法增加保存到wechatapplet表维护有效期
     * @param string $appid 不可为空
     * @param string $authorizer_refresh_token 不可为空
     * @return boolean|array                //SDK缓存了公众号access_token,应用如将此值做了保存，也应同时更新。
     */
    public function checkAuth($appid = '', $authorizer_refresh_token = '' ){
        if (!$appid) {
            $appid = $this->appid;
            if (!$appid) {
                return false;
            }
        }
        if (!$authorizer_refresh_token) {
            $authorizer_refresh_token = $this->authorizer_refresh_token;
            if (!$authorizer_refresh_token) {
                return false;
            }
        }

        $old_info = Db::name('wechat_applet')->where(['appid' => $appid])->find();

        if($old_info['access_token_overtime'] > time()) {
            //没过期，直接返回
            $this->access_token = $old_info['authorizer_access_token'];
            return $old_info['authorizer_access_token'];
        }

/*        $authname = 'wechat_access_token'.$appid;

        $rs = $this->getCache($authname);
        if ($rs)  {
            $this->access_token = $rs;
            return $rs;
        }*/

        $data = array(
            "component_appid"=>$this->component_appid,
            "authorizer_appid"=>$appid,
            "authorizer_refresh_token"=>$authorizer_refresh_token,
        );
        $result = $this->http_post(self::API_URL_PREFIX.self::WXOPEN_AUTHORIZER_TOKEN.'component_access_token='.$this->component_access_token, self::json_encode($data));

        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || isset($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->access_token = $json['authorizer_access_token'];
            $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 7200;
            //$this->setCache($authname,$this->access_token,$expire);
            $appinfo = WechatApplet::get(['appid' => $appid]);//获取公众号信息
            $appinfo->authorizer_access_token = $json['authorizer_access_token'];
            $appinfo->access_token_overtime = time()+$expire;
            $appinfo->save();

            return $json;
        }
        return false;
    }
}



