<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 16:48
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule = [
      'ids' => 'require|checkIDs'
    ];

    protected $message = [
      'ids' => 'ids参数必须是以逗号分隔的多个正整数'
    ];

    protected function checkIDs($value)
    {
        $arr = explode(',',$value);
        if(empty($arr)){
            return false;
        }

        foreach ($arr as $id){
            if(!$this->isPositiveInteger($id)){
                return false;
            }
        }
        return true;
    }
}
