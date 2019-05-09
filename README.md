wechatopen-php-sdk
==================

微信开放平台php开发包,细化各项接口操作,支持链式调用,欢迎Fork此项目  
php sdk for wechat open platform.
项目地址：**https://github.com/uctoo/wechatopen-php-sdk**  

## 知识准备
使用前请先查看微信公众平台、微信开放平台、微信支付官方文档：  
微信公众平台： http://mp.weixin.qq.com/wiki/  
微信开放平台： https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1419318292&token=&lang=zh_CN  
微信支付：https://pay.weixin.qq.com/wiki/doc/api/index.html  

## 目录 
> **[WechatOpen.php 官方API类库](#user-content-1-wechatopenphp-官方api类库)**  
> **[TPWechatOpen.php SDK适配ThinkPHP5类库](#2-tpwechatopenphp-sdk适配ThinkPHP5类库)**  
> **[errCode.php 全局返回码类](#user-content-5-errcodephp-全局返回码类)**  

## Requirement 运行要求

1. PHP >= 7.0
2. **[Composer](https://getcomposer.org/)**
3. openssl 拓展
4. fileinfo 拓展（素材管理模块需要用到）

## Installation 安装

```shell
$ composer require uctoo/wechatopen:dev-master
```

## Usage

基本使用:

----------

## 1. WechatOpen.php 官方API类库
此类不直接使用，一般需继承此类，根据开发框架适配重载缓存、log、初始化方法后使用。
调用官方API，具有更灵活的消息分类响应方式，支持链式调用操作 ； 

### 主要功能 
#### 微信公众号
- 接入验证 **（初级权限）**
- 自动回复（文本、图片、语音、视频、音乐、图文） **（初级权限）**
- 菜单操作（查询、创建、删除） **（菜单权限）**
- 客服消息（文本、图片、语音、视频、音乐、图文） **（认证权限）**
- 二维码（创建临时、永久二维码，获取二维码URL） **（服务号、认证权限）**
- 长链接转短链接接口 **（服务号、认证权限）**
- 分组操作（查询、创建、修改、移动用户到分组） **（认证权限）**
- 网页授权（基本授权，用户信息授权） **（服务号、认证权限）**
- 用户信息（查询用户基本信息、获取关注者列表） **（认证权限）**
- 多客服功能（客服管理、获取客服记录、客服会话管理） **（认证权限）**
- 媒体文件（上传、获取） **（认证权限）**
- 高级群发 **（认证权限）**
- 模板消息（设置所属行业、添加模板、发送模板消息） **（服务号、认证权限）**
- 卡券管理（创建、修改、删除、发放、门店管理等） **（认证权限）**
- 语义理解 **（服务号、认证权限）**
- 获取微信服务器IP列表 **（初级权限）**  
- 微信JSAPI授权(获取ticket、获取签名) **（初级权限）**  
- 数据统计(用户、图文、消息、接口分析数据) **（认证权限）**  
> 备注：  
> 初级权限：基本权限，任何正常的公众号都有此权限  
> 菜单权限：正常的服务号、认证后的订阅号拥有此权限  
> 认证权限：分为订阅号、服务号认证，如前缀服务号则仅认证的服务号有此权限，否则为认证后的订阅号、服务号都有此权限  
> 支付权限：仅认证后的服务号可以申请此权限  

#### 微信小程序
- 小程序相关接口

#### 微信开放平台
- 微信开放平台相关接口

### 初始化动作 
```php
   $options = array(
       'token'=>'tokenaccesskey', //填写你设定的key
       'encodingaeskey'=>'encodingaeskey', //填写加密用的EncodingAESKey  
       'component_appid'=>'wxdk1234567890', //填写第三方平台的appid
       'component_appsecret'=>'xxxxxxxxxxxxxxxxxxx' //填写第三方平台的appid
	);
   $weObj = new Wechat($options);
   $weObj->setAppid($appid);         //授权到第三方平台的公众号/小程序 appid
   $weObj->setAuthorizerRefreshToken($authorizer_refresh_token);         //授权到第三方平台的公众号/小程序 authorizer_refresh_token
```
**设置了appid和authorizer_refresh_token后，多数主动接口方法在不更换授权方的场景下可以不用再传入这两个参数，checkAuth方法会检测当前所设置的公众号/小程序接口调用凭据access_token是否有效。**

### 被动接口方法:   
* valid() 验证连接，被动接口处于加密模式时必须调用
* getRev() 获取微信服务器发来信息(不返回结果)，被动接口必须调用
* getRevData() 返回微信服务器发来的信息（数组）
* getRevFrom()  返回消息发送者的userid
* getRevTo()  返回消息接收者的id（即公众号id）
* getRevType() 返回接收消息的类型
* getRevID() 返回消息id
* getRevCtime() 返回消息发送时间
* getRevContent() 返回消息内容正文或语音识别结果（文本型）
* getRevPic() 返回图片信息（图片型信息） 返回数组{'mediaid'=>'','picurl'=>''}
* getRevLink() 接收消息链接（链接型信息） 返回数组{'url'=>'','title'=>'','description'=>''}
* getRevGeo() 返回地理位置（位置型信息） 返回数组{'x'=>'','y'=>'','scale'=>'','label'=>''}
* getRevEventGeo() 返回事件地理位置（事件型信息） 返回数组{'x'=>'','y'=>'','precision'=>''}
* getRevEvent() 返回事件类型（事件型信息） 返回数组{'event'=>'','key'=>''}
* getRevScanInfo() 获取自定义菜单的扫码推事件信息，事件类型为`scancode_push`或`scancode_waitmsg` 返回数组array ('ScanType'=>'qrcode','ScanResult'=>'123123')
* getRevSendPicsInfo() 获取自定义菜单的图片发送事件信息,事件类型为`pic_sysphoto`或`pic_photo_or_album`或`pic_weixin` 数组结构见php文件内方法说明
* getRevSendGeoInfo() 获取自定义菜单的地理位置选择器事件推送，事件类型为`location_select` 数组结构见php文件内方法说明
* getRevVoice() 返回语音信息（语音型信息） 返回数组{'mediaid'=>'','format'=>''}
* getRevVideo() 返回视频信息（视频型信息） 返回数组{'mediaid'=>'','thumbmediaid'=>''}
* getRevTicket() 返回接收TICKET（扫描带参数二维码,关注或SCAN事件） 返回二维码的ticket值
* getRevSceneId() 返回二维码的场景值（扫描带参数二维码的关注事件） 返回二维码的参数值
* getRevTplMsgID() 返回主动推送的消息ID（群发或模板消息事件） 返回MsgID值
* getRevStatus() 返回模板消息发送状态（模板消息事件） 返回文本：success(成功)|failed:user block(用户拒绝接收)|failed: system failed(发送失败（非用户拒绝）)
* getRevResult() 返回群发或模板消息发送结果（群发或模板消息事件） 返回数组，内容依事件类型而不同，参考开发文档中群发、模板消息推送事件
* getRevKFCreate() 返回多客服-接入会话的客服账号（多客服-接入会话事件） 返回文本型
* getRevKFClose() 返回多客服-处理会话的客服账号（多客服-接入会话事件） 返回文本型
* getRevKFSwitch() 返回多客服-转接会话信息（多客服-转接会话事件） 返回数组	{'FromKfAccount' => '','ToKfAccount' => ''}
* getRevCardPass() 返回卡券-审核通过的卡券ID（卡券-卡券审核事件） 返回文本型
* getRevCardGet() 返回卡券-用户领取卡券的相关信息（卡券-领取卡券事件） 返回数组{'CardId' => '','IsGiveByFriend' => '','UserCardCode' => ''}
* getRevCardDel() 返回卡券-用户删除卡券的相关信息（卡券-删除卡券事件） 返回数组{'CardId' => '','UserCardCode' => ''}
* 
* text($text) 设置文本型消息，参数：文本内容
* image($mediaid) 设置图片型消息，参数：图片的media_id
* voice($mediaid) 设置语音型消息，参数：语音的media_id
* video($mediaid='',$title,$description) 设置视频型消息，参数：视频的media_id、标题、摘要
* music($title,$desc,$musicurl,$hgmusicurl='',$thumbmediaid='') 设置回复音乐，参数：音乐标题、音乐描述、音乐链接、高音质链接、缩略图的媒体id
* news($newsData) 设置图文型消息，参数：数组。数组结构见php文件内方法说明
* Message($msg = '',$append = false) 设置发送的消息（一般不需要调用这个方法）
* transfer_customer_service($customer_account = '') 转接多客服，如不指定客服可不提供参数，参数：指定客服的账号
* reply() 将以上已经设置好的消息，回复给微信服务器

### 预定义常量列表：
```php
////消息类型，使用实例调用getRevType()方法取得
const MSGTYPE_TEXT = 'text';
const MSGTYPE_IMAGE = 'image';
const MSGTYPE_LOCATION = 'location';
const MSGTYPE_LINK = 'link';
const MSGTYPE_EVENT = 'event';
const MSGTYPE_MUSIC = 'music';
const MSGTYPE_NEWS = 'news';
const MSGTYPE_VOICE = 'voice';
const MSGTYPE_VIDEO = 'video';
////事件类型，使用实例调用getRevEvent()方法取得
const EVENT_SUBSCRIBE = 'subscribe';       //订阅
const EVENT_UNSUBSCRIBE = 'unsubscribe';   //取消订阅
const EVENT_SCAN = 'SCAN';                 //扫描带参数二维码
const EVENT_LOCATION = 'LOCATION';         //上报地理位置
const EVENT_MENU_VIEW = 'VIEW';                     //菜单 - 点击菜单跳转链接
const EVENT_MENU_CLICK = 'CLICK';                   //菜单 - 点击菜单拉取消息
const EVENT_MENU_SCAN_PUSH = 'scancode_push';       //菜单 - 扫码推事件(客户端跳URL)
const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; //菜单 - 扫码推事件(客户端不跳URL)
const EVENT_MENU_PIC_SYS = 'pic_sysphoto';          //菜单 - 弹出系统拍照发图
const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album';  //菜单 - 弹出拍照或者相册发图
const EVENT_MENU_PIC_WEIXIN = 'pic_weixin';         //菜单 - 弹出微信相册发图器
const EVENT_MENU_LOCATION = 'location_select';      //菜单 - 弹出地理位置选择器
const EVENT_SEND_MASS = 'MASSSENDJOBFINISH';        //发送结果 - 高级群发完成
const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH';//发送结果 - 模板消息发送结果
const EVENT_KF_SEESION_CREATE = 'kfcreatesession';  //多客服 - 接入会话
const EVENT_KF_SEESION_CLOSE = 'kfclosesession';    //多客服 - 关闭会话
const EVENT_KF_SEESION_SWITCH = 'kfswitchsession';  //多客服 - 转接会话
const EVENT_CARD_PASS = 'card_pass_check';          //卡券 - 审核通过
const EVENT_CARD_NOTPASS = 'card_not_pass_check';   //卡券 - 审核未通过
const EVENT_CARD_USER_GET = 'user_get_card';        //卡券 - 用户领取卡券
const EVENT_CARD_USER_DEL = 'user_del_card';        //卡券 - 用户删除卡券
///微信小程序相关接口
const WXAPP_SESSION_URL = '/sns/jscode2session?';
const WXAPP_MODIFY_DOMAIN = '/wxa/modify_domain?';   //修改服务器地址。需要先将域名登记到第三方平台的小程序服务器域名中，才可以调用接口进行配置
const WXAPP_SETWEBVIEW_DOMAIN = '/wxa/setwebviewdomain?';   //设置小程序业务域名（仅供第三方代小程序调用）
const WXAPP_BIND_TESTER = '/wxa/bind_tester?';       //1、绑定微信用户为小程序体验者
const WXAPP_UNBIND_TESTER = '/wxa/unbind_tester?';   //2、解除绑定小程序的体验者
const WXAPP_COMMIT = '/wxa/commit?';          //1、为授权的小程序帐号上传小程序代码
const WXAPP_GET_QRCODE = '/wxa/get_qrcode?';          //2、获取体验小程序的体验二维码
const WXAPP_GET_CATEGORY = '/wxa/get_category?';          //3、获取授权小程序帐号的可选类目
const WXAPP_GET_PAGE = '/wxa/get_page?';          //4、获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用）
const WXAPP_SUBMIT_AUDIT = '/wxa/submit_audit?';          //5、将第三方提交的代码包提交审核（仅供第三方开发者代小程序调用）
const WXAPP_GET_AUDITSTATUS = '/wxa/get_auditstatus?';          //7、获取第三方提交的审核版本的审核状态（仅供第三方代小程序调用）
const WXAPP_GET_LATESTAUDITSTATUS = '/wxa/get_latest_auditstatus?';          //7、获取第三方提交的审核版本的审核状态（仅供第三方代小程序调用）
const WXAPP_RELEASE = '/wxa/release?';          //9、发布已通过审核的小程序（仅供第三方代小程序调用）
const WXAPP_CHANGE_VISITSTATUS = '/wxa/change_visitstatus?';          //10、修改小程序线上代码的可见状态（仅供第三方代小程序调用）
const WXAPP_CODE_UNLIMIT = '/wxa/getwxacodeunlimit?';             //获取数量不受限的小程序二维码
///文档省略几百行，具体请参考源码
///微信开放平台相关接口
const WXOPEN_CLEAR = '/clear_quota?';   //公众号调用或第三方代公众号调用对公众号的所有API调用（包括第三方代公众号调用）次数进行清零
const WXOPEN_COMPONENT_CLEAR = '/component/clear_quota?';   //第三方平台对其所有API调用次数清零（只与第三方平台相关，与公众号无关，接口如api_component_token）
const WXOPEN_COMPONENT_ACCESS_TOKEN = '/component/api_component_token?';
const WXOPEN_COMPONENT_PREAUTHCODE = '/component/api_create_preauthcode?';
const WXOPEN_AUTHORIZATION_INFO = '/component/api_query_auth?';
const WXOPEN_AUTHORIZER_TOKEN = '/component/api_authorizer_token?';
const WXOPEN_AUTHORIZER_INFO = '/component/api_get_authorizer_info?';
const WXOPEN_GET_AUTHORIZER_OPTION = '/component/api_get_authorizer_option?';
const WXOPEN_SET_AUTHORIZER_OPTION = '/component/api_set_authorizer_option?';
const WXAPP_COMPONENT_SESSION_URL = '/sns/component/jscode2session?';   //

//微信企业支付提现
const WXAPP_TRANSFERS_URL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
//微信退款接口
const WXAPP_REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';

//微信下发小程序和公众号统一的服务消息
const WX_SEND_UNIFORM_MESSAGE = '/message/wxopen/template/uniform_send?';
//微信下发公众号订阅消息
const WX_SUBSCRIBE_MESSAGE = '/mp/subscribemsg?';  //TODO::第一步：需要用户同意授权，获取一次给用户推送一条订阅模板消息的机会
const WX_TEMPLATE_SUBSCRIBE = '/message/template/subscribe?';  //TODO::第二步：通过API推送订阅模板消息给到授权微信用户
```

### 变量列表：
```php
    protected $token;
    protected $encodingAesKey;
    protected $encrypt_type;
    protected $appid;                            //公众号或小程序的
    protected $appsecret;                        //公众号或小程序的，授权到第三方平台后，一般此值为空
    protected $authorizer_refresh_token;         //公众号或小程序的刷新令牌，只会在授权时刻提供，请妥善保存
    protected $component_appid;                  //第三方平台的，初始化时必填
    protected $component_appsecret;              //第三方平台的，初始化时必填
    protected $access_token;                     //即authorizer_access_token，公众号或小程序的接口调用凭据,应用维护各授权公众号的access_token,一般由checkAuth方法自动设置
    protected $component_access_token;           //第三方平台自己的接口调用凭据,第三方最好自行保存，初始化时必填，并在收到component_verify_ticket消息时及时更新
    protected $pre_auth_code;                    //第三方平台预授权码
    protected $jsapi_ticket;
    protected $api_ticket;
    protected $user_token;
    protected $partnerid;
    protected $partnerkey;
    protected $paysignkey;
    protected $postxml;
    protected $_msg;
    protected $_funcflag = false;
    protected $_receive;
    protected $_text_filter = true;
    public $debug =  false;
    public $errCode = 40001;
    public $errMsg = "no access";
    public $logcallback;
```

### 主动接口方法:   
 *  checkAuth($appid='', $authorizer_refresh_token='') 获取（刷新）授权公众号或小程序的接口调用凭据（令牌）,此处传入授权到第三方平台的appid和authorizer_refresh_token。函数将返回access_token操作令牌
 *  resetAuth($appid='') 删除验证数据
 *  resetJsTicket($appid='') 删除JSAPI授权TICKET
 *  getJsTicket($jsapi_ticket='',$appid='',$authorizer_refresh_token='') 获取JSAPI授权TICKET
 *  getJsSign($url, $timestamp=0, $noncestr='', $appid='',$authorizer_refresh_token='') 获取JsApi使用签名信息数组，可只提供url地址 
 *  createMenu(array $data, $appid='', $authorizer_refresh_token='') 创建菜单 $data菜单结构详见 **[自定义菜单创建接口](http://mp.weixin.qq.com/wiki/index.php?title=自定义菜单创建接口)**
 *  getServerIp() 获取微信服务器IP地址列表 返回数组array('127.0.0.1','127.0.0.1')
 *  getMenu() 获取菜单 
 *  deleteMenu() 删除菜单 
 *  uploadMedia($data, $type) 上传临时素材，有效期为3天(注意上传大文件时可能需要先调用 set_time_limit(0) 避免超时)
 *  getMedia($media_id,$is_video=false) 获取临时素材（含接收到的音频、视频媒体文件）
 *  uploadForeverMedia($data, $type,$is_video=false,$video_info=array()) 上传永久素材，可以在公众平台官网素材管理模块中看到
 *  uploadForeverArticles($data) 上传永久图文素材
 *  updateForeverArticles($media_id,$data,$index=0) 修改永久图文素材(认证后的订阅号可用)
 *  getForeverMedia($media_id,$is_video=false) 获取永久素材
 *  delForeverMedia($media_id) 删除永久素材
 *  getForeverList($type,$offset,$count) 获取永久素材列表(认证后的订阅号可用)
 *  getForeverCount() 获取永久素材总数
 *  uploadMpVideo($data) 上传视频素材，当需要群发视频时，必须使用此方法得到的MediaID，否则无法显示
 *  uploadArticles($data) 上传图文消息素材
 *  sendMassMessage($data) 高级群发消息
 *  sendGroupMassMessage($data) 高级群发消息（全体或分组群发）
 *  deleteMassMessage($msg_id) 删除群发图文消息
 *  previewMassMessage($data) 预览群发消息
 *  queryMassMessage($msg_id) 查询群发消息发送状态
 *  getQRCode($scene_id,$type=0,$expire=1800) 获取推广二维码ticket字串 
 *  getQRUrl($ticket) 获取二维码图片地址
 *  getShortUrl($long_url) 长链接转短链接接口
 *  getUserList($next_openid) 批量获取关注用户列表 
 *  getUserInfo($openid) 获取关注者详细信息 
 *  updateUserRemark($openid,$remark) 设置用户备注名
 *  getGroup() 获取用户分组列表 
 *  getUserGroup($openid) 获取用户所在分组
 *  createGroup($name) 新增自定分组 
 *  updateGroup($groupid,$name) 更改分组名称 
 *  updateGroupMembers($groupid,$openid) 移动用户分组  
 *  batchUpdateGroupMembers($groupid,$openid_list) 批量移动用户分组 
 *  sendCustomMessage($data) 发送客服消息  
 *  getOauthRedirect($callback,$state,$scope) 获取网页授权oAuth跳转地址  
 *  getOauthAccessToken() 通过回调的code获取网页授权access_token  
 *  getOauthRefreshToken($refresh_token) 通过refresh_token对access_token续期  
 *  getOauthUserinfo($access_token,$openid) 通过网页授权的access_token获取用户资料  
 *  getOauthAuth($access_token,$openid)  检验授权凭证access_token是否有效
 *  getSignature($arrdata,'sha1') 生成签名字串  
 *  generateNonceStr($length=16) 获取随机字串  
 *  setTMIndustry($id1,$id2='') 模板消息，设置所属行业
 *  addTemplateMessage($tpl_id) 模板消息，添加消息模板
 *  sendTemplateMessage($data) 发送模板消息
 *  
 *  多客服接口：
 *  getCustomServiceMessage($data) 获取多客服会话记录
 *  transfer_customer_service($customer_account) 转发多客服消息
 *  getCustomServiceKFlist() 获取多客服客服基本信息
 *  getCustomServiceOnlineKFlist() 获取多客服在线客服接待信息
 *  createKFSession($openid,$kf_account,$text='') 创建指定多客服会话
 *  closeKFSession($openid,$kf_account,$text='') 关闭指定多客服会话
 *  getKFSession($openid) 获取用户会话状态
 *  getKFSessionlist($kf_account) 获取指定客服的会话列表
 *  getKFSessionWait() 获取未接入会话列表
 *  addKFAccount($account,$nickname,$password) 添加客服账号
 *  updateKFAccount($account,$nickname,$password) 修改客服账号信息
 *  deleteKFAccount($account) 删除客服账号
 *  setKFHeadImg($account,$imgfile) 上传客服头像
 *  
 *  querySemantic($uid,$query,$category,$latitude=0,$longitude=0,$city="",$region="") 语义理解接口 参数含义及返回的json内容请查看 **[微信语义理解接口](http://mp.weixin.qq.com/wiki/index.php?title=语义理解)**
 *  getDatacube($type,$subtype,$begin_date,$end_date='') 获取统计数据 参数需注意$type与$subtype的定义
> 获取统计数据方法 参数定义
> 
| 数据分类 | $type值(字符串)  | 数据子分类 | $subtype值(字符串) | 时间跨度(天) |
| --------- | :-------:  | --------- | :------: | ----: |
| 用户分析 | 'user' | 获取用户增减数据 | 'summary' | 7 |
| 用户分析 | 'user' | 获取累计用户数据 | 'cumulate' | 7 |
| 图文分析 | 'article' | 获取图文群发每日数据 | 'summary' | 1 |
| 图文分析 | 'article' | 获取图文群发总数据 | 'total' | 1 |
| 图文分析 | 'article' | 获取图文统计数据 | 'read' | 3 |
| 图文分析 | 'article' | 获取图文统计分时数据 | 'readhour' | 1 |
| 图文分析 | 'article' | 获取图文分享转发数据 | 'share' | 7 |
| 图文分析 | 'article' | 获取图文分享转发分时数据 | 'sharehour' | 1 |
| 消息分析 | 'upstreammsg' | 获取消息发送概况数据 | 'summary' | 7 |
| 消息分析 | 'upstreammsg' | 获取消息分送分时数据 | 'hour' | 1 |
| 消息分析 | 'upstreammsg' | 获取消息发送周数据 | 'week' | 30 |
| 消息分析 | 'upstreammsg' | 获取消息发送月数据 | 'month' | 30 |
| 消息分析 | 'upstreammsg' | 获取消息发送分布数据 | 'dist' | 15 |
| 消息分析 | 'upstreammsg' | 获取消息发送分布周数据 | 'distweek' | 30 |
| 消息分析 | 'upstreammsg' | 获取消息发送分布月数据 | 'distmonth' | 30 |
| 接口分析 | 'interface' | 获取接口分析数据 | 'summary' | 30 |
| 接口分析 | 'interface' | 获取接口分析分时数据 | 'summaryhour' | 1 |
需要注意 `begin_date`和`end_date`的差值需小于“最大时间跨度”（比如最大时间跨度为1时，`begin_date`和`end_date`的差值只能为0，才能小于1）

 *  
 *  卡券接口：
 *  createCard($data) 创建卡券
 *  updateCard($data) 修改卡券
 *  delCard($card_id) 删除卡券
 *  getCardInfo($card_id) 查询卡券详情
 *  getCardColors() 获取颜色列表
 *  getCardLocations() 拉取门店列表
 *  addCardLocations($data) 批量导入门店信息
 *  createCardQrcode($card_id) 生成卡券二维码
 *  consumeCardCode($code) 消耗 code
 *  decryptCardCode($encrypt_code) code 解码
 *  checkCardCode($code) 获取 code 的有效性
 *  getCardIdList($data) 批量查询卡列表
 *  updateCardCode($code,$card_id,$new_code) 更改 code
 *  unavailableCardCode($code,$card_id='') 设置卡券失效**(不可逆)**
 *  modifyCardStock($data) 库存修改
 *  activateMemberCard($data) 激活/绑定会员卡，参数结构请参看卡券开发文档(6.1.1 激活/绑定会员卡)章节
 *  updateMemberCard($data) 会员卡交易，参数结构请参看卡券开发文档(6.1.2 会员卡交易)章节
 *  updateLuckyMoney($code,$balance,$card_id='') 更新红包金额
 *  setCardTestWhiteList($openid=array(),$user=array()) 设置卡券测试白名单
 *  
 *  摇一摇周边接口：
 *  applyShakeAroundDevice($data) 申请设备ID
 *  updateShakeAroundDevice($data) 编辑设备的备注信息
 *  searchShakeAroundDevice($data) 查询设备列表
 *  bindLocationShakeAroundDevice($device_id,$poi_id,$uuid='',$major=0,$minor=0) 配置设备与门店的关联关系
 *  bindPageShakeAroundDevice($device_id,$page_ids=array(),$bind=1,$append=1,$uuid='',$major=0,$minor=0) 配置设备与页面的关联关系
 *  uploadShakeAroundMedia($data) 上传在摇一摇页面展示的图片素材
 *  addShakeAroundPage($title,$description,$icon_url,$page_url,$comment='') 新增摇一摇出来的页面信息
 *  updateShakeAroundPage($page_id,$title,$description,$icon_url,$page_url,$comment='') 编辑摇一摇出来的页面信息
 *  searchShakeAroundPage($page_ids=array(),$begin=0,$count=1) 查询摇一摇已有的页面
 *  deleteShakeAroundPage($page_ids=array()) 删除摇一摇已有的页面，必须是未与设备关联的页面
 *  getShakeInfoShakeAroundUser($ticket) 获取摇周边的设备及用户信息
 *  deviceShakeAroundStatistics($device_id,$begin_date,$end_date,$uuid='',$major=0,$minor=0) 以设备为维度的数据统计接口
 *  pageShakeAroundStatistics($page_id,$begin_date,$end_date) 以页面为维度的数据统计接口
 *  //方法太多请参考源码
 *  微信小程序接口：
 *  getWxappSession($js_code,$appid='',$authorizer_refresh_token='') 获取微信小程序session
 *  wxaModifyDomain($action,$requestdomain='',$wsrequestdomain='',$uploaddomain='',$downloaddomain='',$appid='', $authorizer_refresh_token='') 微信小程序修改服务器地址
 *  wxaSetWebviewDomain($action,$webviewdomain='',$appid='', $authorizer_refresh_token='') 微信小程序设置小程序业务域名（仅供第三方代小程序调用）
 *  wxaBindTester($wechatid,$appid='', $authorizer_refresh_token='') 绑定微信用户为小程序体验者
 *  wxaUnbindTester($wechatid,$appid='', $authorizer_refresh_token='') 解除绑定小程序的体验者
 *  wxaCommit($template_id, $ext_json, $user_version,$user_desc,$appid='', $authorizer_refresh_token='' ) 为授权的小程序帐号上传小程序代码
 *  wxaGetQrcode($appid='', $authorizer_refresh_token='') 获取体验小程序的体验二维码（仅供第三方开发者代小程序调用）
 *  wxaGetCategory($appid='', $authorizer_refresh_token='') 获取授权小程序帐号的可选类目
 *  wxaGetPage($appid='', $authorizer_refresh_token='') 获取小程序的第三方提交代码的页面配置（仅供第三方开发者代小程序调用）
 *  wxaSubmitAudit($item_list,$appid='', $authorizer_refresh_token='' ) 将第三方提交的代码包提交审核（仅供第三方开发者代小程序调用）
 *  wxaGetAuditstatus($auditid,$appid='', $authorizer_refresh_token='' ) 获取第三方提交的审核版本的审核状态（仅供第三方代小程序调用）
 *  wxaGetLatestAuditstatus($appid='', $authorizer_refresh_token='') 查询最新一次提交的审核状态（仅供第三方代小程序调用）
 *  wxaRelease($appid='', $authorizer_refresh_token='') 发布已通过审核的小程序（仅供第三方代小程序调用）
 *  wxaChangeVisitstatus($action,$appid='', $authorizer_refresh_token='') 修改小程序线上代码的可见状态（仅供第三方代小程序调用）
 *  
 *  微信第三方平台接口：
 *  getComponentAccessToken($component_verify_ticket) 获取第三方平台component_access_token
 *  getPreAuthCode() 获取预授权码pre_auth_code
 *  getPreAuthorizationUrl($callbackUrl,  $authCode = null) 生成授权页url
 *  getAuthorizationInfo($authorization_code) 使用授权码换取公众号或小程序的接口调用凭据和授权信息
 *  getAuthorizerInfo($appid) 获取授权方的帐号基本信息
 *  getAuthorizerOption($appid,$option_name) 获取授权方的选项设置信息
 *  setAuthorizerOption($appid,$option_name,$option_value) 设置授权方的选项设置信息
 *  clearQuota($appid='', $authorizer_refresh_token='') 公众号调用或第三方代公众号调用对公众号的所有API调用（包括第三方代公众号调用）次数进行清零

### 扩展规约
如要添加此类未包含的接口，请参考如下规约，主动接口方法以wxaCommit为例：
1. 方法名尽量与微信API地址的方法名一致。
2. 授权方$appid='', $authorizer_refresh_token='' 参数在参数列表最后。
3. 用checkAuth方法检测授权方access_token有效性。
4. 在预定义常量列表代码段添加微信API地址常量。
5. 按微信API接口http请求方式post或get访问微信API。
```php
public function wxaCommit($template_id, $ext_json, $user_version,$user_desc,$appid='', $authorizer_refresh_token='' ){
        if (!$this->access_token && !$this->checkAuth($appid ,$authorizer_refresh_token)) return false;
        $data = array(
            'template_id'=>$template_id,
            'ext_json'=>$ext_json,
            'user_version'=>$user_version,
            'user_desc'=>$user_desc
        );
        $result = $this->http_post(self::API_BASE_URL_PREFIX.self::WXAPP_COMMIT.'access_token='.$this->access_token, self::json_encode($data));
        if ($result)
        {
            $json = json_decode($result,true);
            if (!$json || !empty($json['errcode'])) {
                $this->errCode = $json['errcode'];
                $this->errMsg = $json['errmsg'];
                return false;
            }
            return $json;
        }
        return false;
    }
```

## 2. TPWechatOpen.php SDK适配ThinkPHP5类库
严格来说，此类并非只是适配ThinkPHP5框架，而是在 www.weiyoho.com 产品中使用的一个类，因此与应用系统有一定耦合，仅供开发者参考。开发者可根据各自的应用具体情况进行修改。此类主要在初始化时设置第三方平台的参数，以及重写了父类WechatOpen.php的checkAuth、getJsTicket方法以获取正确的授权方authorizer_access_token。
一般第三方平台需要保存第三方平台信息以及授权方公众号/小程序的信息，维护第三方平台component_access_token、授权方access_token、jsapi_ticket等变量的生命周期。此类通过数据库对这些信息进行持久化，并且未使用缓存。第三方平台信息表uct_mpopen和微信应用信息表uct_wechat_applet结构如下。
```sql
-- ----------------------------
-- Table structure for uct_mpopen
-- ----------------------------
DROP TABLE IF EXISTS `uct_mpopen`;
CREATE TABLE `uct_mpopen` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `appid` varchar(255) NOT NULL COMMENT 'appid',
  `appsecret` varchar(255) NOT NULL COMMENT 'appsecret',
  `encodingAesKey` varchar(43) NOT NULL COMMENT 'encodingAesKey',
  `component_verify_ticket` varchar(255) NOT NULL COMMENT 'component_verify_ticket',
  `component_access_token` varchar(255) NOT NULL COMMENT 'component_access_token',
  `token_overtime` int(15) NOT NULL COMMENT 'token过期时间',
  `pre_auth_code` varchar(255) NOT NULL COMMENT '预授权码',
  `pre_code_overtime` int(15) NOT NULL COMMENT '预授权过期时间',
  `status` int(2) NOT NULL,
  `token` varchar(255) NOT NULL COMMENT 'token',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='微信开放平台表';

-- ----------------------------
-- Records of uct_mpopen
-- ----------------------------
INSERT INTO `uct_mpopen` VALUES ('1', 'wx37......', '5e6......', 'p9......', 'ticket@@@Cp......', '20_Yx......', '1556441737', '1', '1516880730', '1', 'uctoo');

-- ----------------------------
-- Table structure for uct_wechat_applet
-- ----------------------------
DROP TABLE IF EXISTS `uct_wechat_applet`;
CREATE TABLE `uct_wechat_applet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `admin_id` int(10) NOT NULL COMMENT '管理员ID',
  `appletid` varchar(50) NOT NULL COMMENT '微应用标识',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '应用名称',
  `typedata` enum('serv_account','miniapp','sub_account') NOT NULL DEFAULT 'serv_account' COMMENT '应用类型:serv_account=服务号,miniapp=小程序,sub_account=订阅号',
  `token` varchar(100) NOT NULL DEFAULT '' COMMENT 'Token',
  `appid` varchar(255) NOT NULL DEFAULT '' COMMENT 'AppID',
  `appsecret` varchar(255) DEFAULT NULL COMMENT 'AppSecret',
  `mp_appid` varchar(255) DEFAULT '' COMMENT '公众号 AppID',
  `mp_appsecret` varchar(255) DEFAULT NULL COMMENT '公众号 AppSecret',
  `aeskey` varchar(255) DEFAULT NULL COMMENT 'EncodingAESKey',
  `mchid` varchar(50) DEFAULT NULL COMMENT '商户号',
  `mchkey` varchar(50) DEFAULT NULL COMMENT '商户支付密钥',
  `mch_api_cert` varchar(255) DEFAULT NULL COMMENT '商户API证书cert',
  `mch_api_key` varchar(255) DEFAULT NULL COMMENT '商户API证书key',
  `notify_url` varchar(255) DEFAULT NULL COMMENT '微信支付异步通知',
  `principal` varchar(100) DEFAULT NULL COMMENT '主体名称',
  `original` varchar(50) DEFAULT NULL COMMENT '原始ID',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信号',
  `headface_image` varchar(255) DEFAULT NULL COMMENT '头像',
  `qrcode_image` varchar(255) DEFAULT NULL COMMENT '二维码图片',
  `signature` text COMMENT '账号介绍',
  `city` varchar(50) DEFAULT NULL COMMENT '省市',
  `state` enum('enable','disable','unaudit') NOT NULL DEFAULT 'enable' COMMENT '状态:enable=启用,disable=禁用,unaudit=未审核',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deletetime` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `service_type_info` char(10) NOT NULL DEFAULT '0' COMMENT '授权方公众号类型，0代表订阅号/小程序，1代表由历史老帐号升级后的订阅号，2代表服务号',
  `verify_type_info` char(10) NOT NULL DEFAULT '0' COMMENT '授权方认证类型，-1代表未认证，0代表微信认证，1代表新浪微博认证，2代表腾讯微博认证，3代表已资质认证通过但还未通过名称认证，4代表已资质认证通过、还未通过名称认证，但通过了新浪微博认证，5代表已资质认证通过、还未通过名称认证，但通过了腾讯微博认证',
  `business_info` text COMMENT '用以了解公众号功能的开通状况',
  `authorizer_access_token` varchar(255) DEFAULT NULL,
  `access_token_overtime` int(15) DEFAULT NULL,
  `authorizer_refresh_token` varchar(255) DEFAULT NULL,
  `miniprograminfo` text COMMENT '小程序信息',
  `func_info` text COMMENT '公众号授权给开发者的权限集列表',
  `ticket` varchar(100) DEFAULT '' COMMENT 'jsapi ticket',
  `ticket_overtime` int(15) DEFAULT NULL COMMENT 'jsapi ticket 过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='微应用表';

-- ----------------------------
-- Records of uct_wechat_applet
-- ----------------------------
INSERT INTO `uct_wechat_applet` VALUES ('1', '1', '9d......', '微信扫码登录网站', '', '', 'wx......', '3e74......', '', '', '', '', '', '', '', null, '', '', '', '', '', '', '', 'enable', '1532017642', '1552391630', null, '1', '0', '0', null, null, null, null, null, null, '', null);
INSERT INTO `uct_wechat_applet` VALUES ('2', '1', '867e......', '优创...', 'serv_account', '', 'wx2......', '', '', '', '', '1228598202', '......', '', '', null, '深圳优创......', 'gh_1d......', 'UCToo_com', 'http://wx.qlo......', 'htt......', null, '', 'enable', '1532018545', '1556329683', null, '2', '{\"id\":2}', '{\"id\":0}', '{\"open_pay\":1,\"open_shake\":0,\"open_scan\":0,\"open_card\":1,\"open_store\":1}', '20_RWV......', '1556336783', 'refreshtoken@@@......', null, '......', 'U3N......', '1547815763');
INSERT INTO `uct_wechat_applet` VALUES ('3', '2', '722......', '微友货', 'miniapp', '', 'wx40......', 'c0e3......', 'wx2......', '', '', '1228598202', '......', '......pem', '.......pem', null, '深圳优创......', 'gh_e65......', '', 'http://wx.qlogo.cn/m......', 'http://mmbiz.qpic.cn/mmbiz......', '......', '', 'enable', '1532024934', '1556431274', null, '3', '{\"id\":0}', '{\"id\":0}', '{\"open_pay\":1,\"open_shake\":0,\"open_scan\":0,\"open_card\":0,\"open_store\":0}', '20_i......', '1556438374', 'refreshtoken@@@B......', '{\"network\":{\"RequestDomain\":[\"https:\\/\\/www.weiyoho.com\"......', '', null);
```
uct_mpopen表需要填写appid、appsecret、encodingAesKey、token字段，正确配置和已通过全网发布的第三方平台，uct_mpopen表其他字段会自动更新。uct_wechat_applet表的数据一般是通过公众号管理员扫码授权帐号时添加。扫码授权的应用appsecret字段为空，authorizer_access_token字段会自动更新，有更好的安全性。网站应用、APP应用等无法扫码授权的，可以人工添加，一般需要填写appsecret字段。

### 使用方法 
```php
   //被动接口
   $this->weObj = new TPWechatOpen();
   $appid = input('appid');
   $this->weObj->setAppid($appid);
   $appinfo = WechatApplet::get(['appid'=>$appid]);   //取公众号信息，系统用数据库在维护access_token等有效期，不加缓存
   $this->weObj->setAuthorizerRefreshToken($appinfo['authorizer_refresh_token']);
   $this->weObj->decrypt_msg(false);                  //TODO:传参不验核签名signature，解密算法有点问题
   $this->weObj->getRev();
   $data = $this->weObj->getRevData();
   $type = $this->weObj->getRevType();
   $ToUserName = $this->weObj->getRevTo();
   $FromUserName = $this->weObj->getRevFrom();
   
   //主动接口
   $weObj->wxaCommit($wxapptemplate->template_id, $wxapptemplate->ext_json, $wxapptemplate->user_version, $wxapptemplate->user_desc,$appinfo['appid'], $appinfo['authorizer_refresh_token']);
```
被动接口一般需要在第三方平台配置的消息与事件接收URL的方法中，根据应用系统需要实现一个消息中控服务器逻辑，例如：
```php
//与微信交互的中控服务器逻辑可以自己定义，这里实现一个通用的
switch ($type) {
    //事件
    case TPWechatOpen::MSGTYPE_EVENT:         //先处理事件型消息
        $event = $this->weObj->getRevEvent();
        if($appid == 'wx570bc396a51b8ff8'){  //公众号全网测试帐号
            $this->weObj->text($event['event'].'_callback')->reply();
        }
        switch ($event['event']) {
            //关注
            case TPWechatOpen::EVENT_SUBSCRIBE:

                //二维码关注
                if(isset($event['eventkey']) && isset($event['ticket'])){

                    //普通关注
                }else{

                }

                //获取回复数据


                $this->weObj->reply();

                if(!$user["subscribe"]){   //未关注，并设置关注状态为已关注
                   $user["subscribe"] = 1;
                   $user->where(['openid'=>$openid])->update(["subscribe"=>1]);
                }
             break;
            //扫描二维码
            case TPWechatOpen::EVENT_SCAN:

                break;
            //地理位置
            case TPWechatOpen::EVENT_LOCATION:

                break;
            //自定义菜单 - 点击菜单拉取消息时的事件推送
            case TPWechatOpen::EVENT_MENU_CLICK:

                $this->weObj->reply();  //在addons中处理完业务逻辑，回复消息给用户
                break;

            //自定义菜单 - 点击菜单跳转链接时的事件推送
            case TPWechatOpen::EVENT_MENU_VIEW:

                break;
            //自定义菜单 - 扫码推事件的事件推送
            case TPWechatOpen::EVENT_MENU_SCAN_PUSH:

                break;
            //自定义菜单 - 扫码推事件且弹出“消息接收中”提示框的事件推送
            case TPWechatOpen::EVENT_MENU_SCAN_WAITMSG:

                break;
            //自定义菜单 - 弹出系统拍照发图的事件推送
            case TPWechatOpen::EVENT_MENU_PIC_SYS:

                break;
            //自定义菜单 - 弹出拍照或者相册发图的事件推送
            case TPWechatOpen::EVENT_MENU_PIC_PHOTO:

                break;
            //自定义菜单 - 弹出微信相册发图器的事件推送
            case TPWechatOpen::EVENT_MENU_PIC_WEIXIN:

                break;
            //自定义菜单 - 弹出地理位置选择器的事件推送
            case TPWechatOpen::EVENT_MENU_LOCATION:

                break;
            //取消关注
            case TPWechatOpen::EVENT_UNSUBSCRIBE:
                if($user["subscribe"]){
                    $user["subscribe"] = 0;     //取消关注设置关注状态为取消
                    $user->where(['openid'=>$user['openid']])->update(["subscribe"=>0]);
                }
                break;
            //群发接口完成后推送的结果
            case TPWechatOpen::EVENT_SEND_MASS:

                break;
            //模板消息完成后推送的结果
            case TPWechatOpen::EVENT_SEND_TEMPLATE:
                
                break;
            //小程序审核成功
            case TPWechatOpen::EVENT_WXAPP_AUDIT_SUCCESS:
                $wxapp_audit_info = model('wxapp_audit_info');
                $userTemplate = WxappUserTemplate::get(['audit_status' => 2, 'mp_id' => $appinfo['appletid'] ]);
                $wxapp_audit_info->mp_id = $appinfo['appletid'];
                $userTemplate->audit_status = $wxapp_audit_info->audit_status = 0;   //审核状态，其中0为审核成功，1为审核失败，2为审核中
                $wxapp_audit_info->auditid = $userTemplate->auditid;
                $userTemplate->succ_time = $wxapp_audit_info->succ_time = $data['SuccTime'];
                $wxapp_audit_info->save();   //保存审核记录
                $userTemplate->save();       //保存审核结果
                break;
            //小程序审核失败
            case TPWechatOpen::EVENT_WXAPP_AUDIT_FAIL:
                $wxapp_audit_info = model('wxapp_audit_info');
                $userTemplate = WxappUserTemplate::get(['audit_status' => 2, 'mp_id' => $appinfo['appletid'] ]);
                $wxapp_audit_info->mp_id = $appinfo['appletid'];
                $userTemplate->audit_status = $wxapp_audit_info->audit_status = 1;   //审核状态，其中0为审核成功，1为审核失败，2为审核中
                $wxapp_audit_info->auditid = $userTemplate->auditid;
                $userTemplate->fail_time = $wxapp_audit_info->fail_time = $data['FailTime'];
                $userTemplate->reason = $wxapp_audit_info->reason = $data['Reason'];
                $wxapp_audit_info->save();   //保存审核记录
                $userTemplate->save();       //保存审核结果
                break;
            case TPWechatOpen::EVENT_USER_ENTER_TEMPSESSION:
                Hook::listen("user_enter_tempsession", $this->weObj);  //把消息分发到实现了keyword方法的addons中,参数中包含本SDK的实例
                break;
            default:

                break;
        }
        break;
    //文本
    case TPWechatOpen::MSGTYPE_TEXT :
        //全网发布测试
        if($appid == 'wx570bc396a51b8ff8' || $appid == 'wx2cdf8e0dffd4b2b2'){
            if($data['Content']=='TESTCOMPONENT_MSG_TYPE_TEXT') { //回复文本
                $this->weObj->text('TESTCOMPONENT_MSG_TYPE_TEXT_callback')->reply();
            }else{
                    echo '';
                    $rc_data = explode(':',$data['Content']);

                    $aInfo = $this->weObj->getAuthorizationInfo($rc_data[1]);

                    $access_token = $aInfo['authorization_info']['authorizer_access_token'];
                    $kf_data = array(
                    'touser'=>$this->weObj->getRevFrom(),
                    'msgtype'=>'text',
                    "text"=>array(
                        'content'=>"$rc_data[1]_from_api"
                        )
                    );
                $this->weObj->sendTestCustomMessage($kf_data,$appid,$appinfo['authorizer_access_token']);
            }
        }
        if($appid == 'wxd101a85aa106f53e'){
            if($data['Content']=='TESTCOMPONENT_MSG_TYPE_TEXT') { //回复文本
                $this->weObj->text('TESTCOMPONENT_MSG_TYPE_TEXT_callback')->reply();
            }else{
                echo '';
                $rc_data = explode(':',$data['Content']);
                $aInfo = $this->weObj->getAuthorizationInfo($rc_data[1]);
                trace($aInfo, 'test$aInfo');
                $access_token = $aInfo['authorization_info']['authorizer_access_token'];
                $kf_data = array(
                    'touser'=>$this->weObj->getRevFrom(),
                    'msgtype'=>'text',
                    "text"=>array(
                        'content'=>"$rc_data[1]_from_api"
                    )
                );
                $this->weObj->sendTestCustomMessage($kf_data,$appid,$appinfo['authorizer_access_token']);
            }
        }
        //正常业务规则，关键字匹配
        Hook::listen("text_auto_reply", $this->weObj);
        Hook::listen("subscribemsg", $this->weObj);

        break;
    //图像
    case TPWechatOpen::MSGTYPE_IMAGE :

        break;
    //语音
    case TPWechatOpen::MSGTYPE_VOICE :

        break;
    //视频
    case TPWechatOpen::MSGTYPE_VIDEO :

        break;
    //位置
    case TPWechatOpen::MSGTYPE_LOCATION :

        break;
    //链接
    case TPWechatOpen::MSGTYPE_LINK :

        break;
    default:

        break;
}
```
主动接口直接在业务功能实现点调用SDK相应方法。

## 交流群
QQ群：102324323，使用疑问，开发，贡献代码请加群。

## 感谢
此项目基于 https://github.com/dodgepudding/wechat-php-sdk 项目改版而来，在此对开发者 dodgepudding 表示感谢。
由于wechat-php-sdk 项目已长期未维护，近年微信生态已增加了很多新的能力，而且自2015年微信推出微信开放平台第三方开发方式以来，第三方开发方式逐渐流行，因此UCToo开发了第三方平台版本的SDK。

## 捐赠
如果您觉得wechatopen对您有帮助，欢迎请作者一杯咖啡

![捐赠wechat](https://gitee.com/uctoo/uctoo/raw/master/Public/images/donate.png)

License
-------
This is licensed under the GNU LGPL, version 2.1 or later.   
For details, see: http://creativecommons.org/licenses/LGPL/2.1/
