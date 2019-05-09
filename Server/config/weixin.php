<?php
/**
 * 微信配置文件
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/4
 * Time: 21:54
 */

return [
    //  +---------------------------------
    //  微信相关配置
    //  +---------------------------------

    // 小程序app_id
    'app_id' => '',
    // 小程序app_secret
    'app_secret' => '',

    // 微信使用code换取用户openid及session_key的url地址
    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

    //接收回调通知地址
    'pay_back_url' => ''
];
