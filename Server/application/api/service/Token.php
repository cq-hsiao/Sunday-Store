<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/6
 * Time: 20:28
 */

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Cache;
use think\facade\Request;

class Token
{
    // 生成令牌
    public static function generateToken()
    {
        $randChars = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME'];
        $tokenSalt = config('setting.token_salt');
        return md5($randChars . $timestamp . $tokenSalt);
    }

    public static function getCurrentUID(){
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    public static function getCurrentTokenVar($key)
    {
        $token = Request::header('token');
        $vars = Cache::get($token);

        if(!$vars){
            throw new TokenException();
        } else {
            if(!is_array($vars)){
                $vars = json_decode($vars,true);
            }
            if(array_key_exists($key,$vars)) {
                return $vars[$key];
            } else {
                throw new Exception('尝试获取的Token变量并不存在');
            }
        }
    }

    //验证token是否合法或者是否过期
    //验证器验证只是token验证的一种方式
    //另外一种方式是使用行为拦截token，根本不让非法token
    //进入控制器
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if($scope){
            if($scope >= ScopeEnum::User){
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    // 用户专有权限
    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    // 管理员专有权限
    public static function needSuperScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope){
            if ($scope == ScopeEnum::Super) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    /**
     * 检查操作UID是否合法或匹配
     * @param $checkedUID
     * @return bool
     * @throws Exception
     */
    public static function isValidOperate($checkedUID)
    {
        if(!$checkedUID){
            throw new Exception('必须传入一个被检查的UID');
        }

        $currentUID = self::getCurrentUID();
        if($checkedUID == $currentUID){
            return true;
        }
        return false;
    }

    public static function verifyToken($token)
    {
        $isExist = Cache::get($token);
        if($isExist){
            return true;
        } else {
            return false;
        }
    }
}
