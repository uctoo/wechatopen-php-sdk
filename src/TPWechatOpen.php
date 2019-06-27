<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2019 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
/**
 *	微信开放平台PHP-SDK,适配thinkphp 5 框架
 *  @author  UCT <admin@uctoo.com>
 *  @link https://github.com/uctoo/wechatopen-php-sdk
 *  @version 1.0
 *  usage:
 *	 $weObj = new Wechat();            //从数据库mpopen表中获取了第三方平台的配置参数
 *   $weObj->setAppid($appid);         //授权到第三方平台的公众号/小程序 appid
 *   $weObj->setAuthorizerRefreshToken($authorizer_refresh_token);         //授权到第三方平台的公众号/小程序 authorizer_refresh_token
 *
 *   $weObj->valid();
 *   $type = $weObj->getRev()->getRevType();
 *   switch($type) {
 *   		case Wechat::MSGTYPE_TEXT:
 *   			$weObj->text("hello, I'm wechat")->reply();
 *   			exit;
 *   			break;
 *   		case Wechat::MSGTYPE_EVENT:
 *   			....
 *   			break;
 *   		case Wechat::MSGTYPE_IMAGE:
 *   			...
 *   			break;
 *   		default:
 *   			$weObj->text("help info")->reply();
 *   }
 *
 *   //获取菜单操作:
 *   $menu = $weObj->getMenu();
 *   //设置菜单
 *   $newmenu =  array(
 *   		"button"=>
 *   			array(
 *   				array('type'=>'click','name'=>'最新消息','key'=>'MENU_KEY_NEWS'),
 *   				array('type'=>'view','name'=>'我要购物','url'=>'https://www.weiyoho.com'),
 *   				)
 *  		);
 *   $result = $weObj->createMenu($newmenu);
 */
namespace Uctoo\Wechatopen;
use app\common\model\WechatApplet;

class TPWechatOpen extends WechatOpen
{
    public function __construct($options = [])
    {
        parent::__construct($options);
        if($options == []){
            $model = model('mpopen');
            $component = $model->where('status', 1)->find(); //数据库中保存的第三方平台信息
            $options['token'] = $component['token'];
            $options['component_appid'] = $component['appid'];    //初始化options信息
            $options['component_appsecret'] = $component['appsecret'];
            $options['component_access_token'] = $component['component_access_token'];
            $options['encodingaeskey'] = $component['encodingAesKey'];
            $options['debug'] = config('app_debug');            //调试状态跟随系统调试状态
            if($options['debug']){
                $options['logcallback'] = 'trace';              //微信类调试信息用trace方法记录到TP日志文件中
            }
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
        $options['token'] = $component['token'];
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

        $old_info = WechatApplet::get(['appid' => $appid]);//获取公众号信息

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

    /**
     * 获取JSAPI授权TICKET，更新为第三方平台实现
     * @param string $appid 用于多个appid时使用,可空
     * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
     */
    public function getJsTicket($jsapi_ticket='',$appid='',$authorizer_refresh_token=''){
        if (!$this->access_token && !$this->checkAuth($appid, $authorizer_refresh_token)) return false;
        if (!$appid) $appid = $this->appid;
        if ($jsapi_ticket) { //手动指定token，优先使用
            $this->jsapi_ticket = $jsapi_ticket;
            return $this->jsapi_ticket;
        }

        $appinfo = WechatApplet::get(['appid' => $appid]);//获取公众号信息

        //$authname = 'wechat_jsapi_ticket'.$appid;
        //if ($rs = $this->getCache($authname))  {
        //	$this->jsapi_ticket = $rs;
        //	return $rs;
        //}

        if($appinfo['ticket_overtime'] > time()) {
            //没过期，直接返回
            $this->jsapi_ticket = $appinfo['ticket'];
            return $appinfo['ticket'];
        }

        $result = $this->http_get(self::API_URL_PREFIX.self::GET_TICKET_URL.'access_token='.$this->access_token.'&type=jsapi');
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            $this->jsapi_ticket = $json['ticket'];
            $expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
            //$this->setCache($authname,$this->jsapi_ticket,$expire);

            $appinfo->ticket = $json['ticket'];
            $appinfo->ticket_overtime = time()+$expire;
            $appinfo->save();

            return $this->jsapi_ticket;
        }
        return false;
    }
}



