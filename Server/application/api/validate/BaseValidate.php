<?php

namespace app\api\validate;

use think\Exception;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     *
     */
    public function goCheck(){

        //获得http传入的参数并做校验
        $params = Request::param();
        $result = $this->check($params);
        if(!$result){
            $error = $this->error;
            throw new Exception($error);
        } else {
            return true;
        }
    }


    protected function isPositiveInteger($value, $rule='', $data='', $field='')
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
//        return $field . '必须是正整数';
        return false;
    }
}
