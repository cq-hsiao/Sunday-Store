<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/6
 * Time: 20:28
 */

namespace app\api\service;


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
}
