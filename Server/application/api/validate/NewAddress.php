<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/7
 * Time: 18:36
 */

namespace app\api\validate;


class NewAddress extends BaseValidate
{

    // 获取post参数时过滤掉user_id 为防止欺骗重写user_id
    // rule中严禁使用user_id
    // 所有数据库和user关联的外键统一使用user_id，而不要使用uid
    protected $rule = [
        'name' => 'require|isNotEmpty',
        'mobile' => 'require|mobile',
        'province' => 'require|isNotEmpty',
        'city' => 'require|isNotEmpty',
        'country' => 'require|isNotEmpty',
        'detail' => 'require|isNotEmpty'
    ];
}
