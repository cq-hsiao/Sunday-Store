<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/6
 * Time: 19:44
 */

namespace app\api\model;


class User extends BaseModel
{
    public function address(){
        return $this->hasOne('UserAddress','user_id','id');
    }
    public static function getByOpenID($openid)
    {
        $user = self::where('openid','=',$openid)
            ->find();
        return $user;
    }
}
