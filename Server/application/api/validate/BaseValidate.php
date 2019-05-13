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

    //手机号的验证规则
    protected function isMobile($value)
    {
//        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $rule = '^((0\d{2,3}-\d{7,8})|(1[3584]\d{9}))$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 通用方法 根据各个验证器的规则中校验数据
     * @param array $array 通常传入request.post变量数组
     * @return array 按照规则key过滤后的变量数组
     * @throws ParameterException
     */
    public function getDataByRule($array)
    {
        if(array_key_exists('user_id',$array) || array_key_exists('uid',$array)){
            // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或者uid'
            ]);
        }

        $newArray = array();
        foreach ($this->rule as $k => $v){
            if(array_key_exists($k,$array)){
                $newArray[$k] = $array[$k];
            }
        }
        return $newArray;
    }
}
