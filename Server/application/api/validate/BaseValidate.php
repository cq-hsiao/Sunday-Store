<?php

namespace app\api\validate;

use app\lib\exception\ParameterException;
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
        $result = $this->batch()->check($params);
        if(!$result){

//            $e = new ParameterException();
//            $e->msg =  $this->error;
//            $e->errorCode =  'XXX';
//初始化赋值最好用构造方法，而不是创建对象后再更改成员变量

            $exception = new ParameterException(
                [
                    // $this->error有一个问题，并不是一定返回数组，需要判断
                    'msg' => is_array($this->error) ? implode(
                        ';', $this->error) : $this->error,
                ]);
            throw $exception;
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

    protected function isNotEmpty($value, $rule='', $data='', $field='')
    {
        $value = trim($value);
        if (empty($value)) {
//            return $field . '不允许为空';
            return false;
        } else {
            return true;
        }
    }

}
