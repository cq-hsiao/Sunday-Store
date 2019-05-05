<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 18:52
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule = [
        'count' => 'isPositiveInteger|between:1,15'
    ];

    protected $message = [
        'count.isPositiveInteger' => 'count必须是正整数',
//        'count.between' => 'count必须在1-15之间'
    ];
}
