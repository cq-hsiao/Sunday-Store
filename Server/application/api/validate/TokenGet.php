<?php
/**
 * Created by PhpStorm.
 * User: Su
 * Date: 2018/12/28
 * Time: 14:39
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => '来者何人？？？'
    ];
}
