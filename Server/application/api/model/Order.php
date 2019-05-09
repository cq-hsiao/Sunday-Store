<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/8
 * Time: 13:54
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = ['user_id', 'delete_time', 'update_time'];
    protected $autoWriteTimestamp = true; //自动生成时间戳

    //读取器
    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value){
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }


    public static function getSummaryByUser($uid,$page = 1,$size = 15)
    {
        $orders = self::where('user_id','=',$uid)
            ->order('create_time','desc')
            ->paginate($size,true,['page' => $page]);

        return $orders;
    }
}
