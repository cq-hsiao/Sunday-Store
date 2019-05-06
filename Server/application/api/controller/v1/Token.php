<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 21:49
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\validate\TokenGet;

class Token
{
    /**
     * 用户获取令牌（登陆）
     * @url /token/user
     * @POST code
     * @note 虽然查询应该使用get，但为了稍微增强安全性，所以使用POST
     */
    public function getToken($code = ''){
        (new TokenGet())->goCheck();
        $ut = new UserToken($code);
        $token = $ut->get();
        return $token;
    }
}
