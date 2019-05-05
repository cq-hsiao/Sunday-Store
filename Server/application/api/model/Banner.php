<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/4/29
 * Time: 15:30
 */

namespace app\api\model;


use think\Db;
use think\Model;

class Banner extends BaseModel
{
    public function items(){
        return $this->hasMany('BannerItem','banner_id','id');
    }

    public static function getBannerByID($id){

        //TP框架三种数据库操作方法
        //1、使用原始SQL
//        $result = Db::query(
//            'select * from banner_item where banner_id=?',[$id]);
//        return $result;

        //2、查询构造器
        //查询语法：表达式法、数组法(不够灵活，安全性不好)、闭包(匿名函数)
//        $result = Db::table('banner_item')->where('banner_id','=',$id)
//        ->select();
//        $result = Db::table('banner_item')
//            ->where(function ($query) use ($id){
//             $query->where('banner_id','=',$id);
//         })
//            ->select();


        //3、ORM与模型 Object Relation Mapping 对象关系映射

        $banner =  self::with(['items','items.img'])
            ->find($id);
        return $banner;
    }
}
