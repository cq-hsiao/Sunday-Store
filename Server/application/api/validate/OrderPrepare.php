<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/7
 * Time: 23:56
 */

namespace app\api\validate;


use app\lib\exception\ParameterException;

class OrderPrepare extends BaseValidate
{
    protected $rule = [
        'products' => 'require|checkProducts'
    ];

    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];

    protected $message = [
        'products.require' => '请选择要购买的商品～'
    ];

    protected function checkProducts($values){

        if(empty($values)){
            throw new ParameterException([
                'msg' => '请选择要购买的商品～'
            ]);
        }
        if(!is_array($values)){
            throw new ParameterException([
                'msg' => '商品参数不正确'
            ]);
        }
        foreach ($values as $v) {
            $this->checkProduct($v);
        }
        return true;
    }

    protected function checkProduct($value){

//        $validate = new BaseValidate($this->singleRule); //validate基类构造方法参数rules message filed
//        $result = $validate->goCheck($value);
        $result = $this->check($value,$this->singleRule);
        if(!$result){
            throw new ParameterException([
                'msg' => '商品列表参数错误',
            ]);
        }
    }
}
