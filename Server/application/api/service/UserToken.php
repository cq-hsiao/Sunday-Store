<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 21:44
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;
use think\Request;
use app\api\model\User as UserModel;

/**
 * 微信登录
 * 如果担心频繁被恶意调用，请限制ip
 * 以及访问频率
 */

class UserToken extends Token
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('weixin.app_id');
        $this->wxAppSecret = config('weixin.app_secret');
        $this->wxLoginUrl = sprintf(
            config('weixin.login_url'),
            $this->wxAppID,$this->wxAppSecret,$this->code);
    }

    /**
     * 登陆
     * 思路1：每次调用登录接口都去微信刷新一次session_key，生成新的Token，不删除旧的Token
     * 思路2：检查Token有没有过期，没有过期则直接返回当前Token
     * 思路3：重新去微信刷新session_key并删除当前Token，返回新的Token
     */
    public function get()
    {
        $result = curl_get($this->wxLoginUrl);

        $wxResult = json_decode($result,true);
        if(empty($wxResult)){
            // 这种情况通常是由于传入不合法的code
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        } else {
            // 建议用明确的变量来表示是否成功
            // 微信服务器并不会将错误状态码标记为400，无论成功还是失败都标记成200
            // 这样非常不好判断，只能使用errcode是否存在来判断
            $loginFail = array_key_exists('errcode',$wxResult);
            if ($loginFail) {
                $this->processLoginError($wxResult);
            }
            else {
                return $this->grantToken($wxResult);
            }
        }
    }

    // 颁发令牌，把缓存数据写入缓存，把令牌返回给客户端
    // 只要调用登陆就颁发新令牌
    // 但旧的令牌依然可以使用
    // 所以通常令牌的有效时间比较短
    // 目前微信的express_in时间是7200秒
    // 在不设置刷新令牌（refresh_token）的情况下
    // 只能延迟自有token的过期时间超过7200秒（目前还无法确定，在express_in时间到期后
    // 还能否进行微信支付
    // 没有刷新令牌会有一个问题，就是用户的操作有可能会被突然中断
    // 缓存数据 key:令牌  value:wxResult,uid,scope权限
    private function grantToken($wxResult)
    {
        // 此处生成令牌使用的是TP5自带的令牌
        // 如果想要更加安全可以考虑自己生成更复杂的令牌
        // 比如使用JWT并加入盐，如果不加入盐有一定的几率伪造令牌
//                $token = Request::token('token', 'md5');

        $openid = $wxResult['openid'];
        // 借助微信的openid作为用户标识
        // 但在系统中的相关查询还是使用自己的uid
        $user = UserModel::getByOpenID($openid);
        if(!$user){
            $uid = $this->newUser($openid);
        } else {
            $uid = $user->id;
        }

        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return [
            'token' => $token
        ];
    }

    private function processLoginError($result) {
        throw new WeChatException(
            [
                'msg' => $result['errmsg'],
                'errorCode' => $result['errcode']
            ]);
    }


    // 创建新用户
    private function newUser($openid)
    {
        // 有可能会有异常，如果没有特别处理
        // 这里不需要try——catch
        // 全局异常处理会记录日志
        // 并且这样的异常属于服务器异常
        // 也不应该定义BaseException返回到客户端
        $user = UserModel::create(
            [
                'openid'=>$openid
            ]);
        return $user->id;
    }

    private function prepareCachedValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    private function saveToCache($cachedValue)
    {
        $key = self::generateToken();

        if(empty($key)){
            throw new TokenException([
                'msg' => '获取令牌失败',
                'errorCode' => 10001
            ]);
        }

        $value = json_encode($cachedValue);
        $expire_in = config('setting.token_expire_in');
        $result = cache($key, $value, $expire_in);

        if (!$result){
            throw new TokenException([
                'msg' => '服务器缓存异常',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }



}
