<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 16:47
 */

namespace app\api\model;


class Product extends BaseModel
{
    //多对多关系的关联查询时会自动多一个pivot字段信息，存储中间表的关联字段。
    protected $hidden = ['delete_time','create_time','update_time','pivot', 'main_img_id', 'from', 'category_id'];

    public function getMainImgUrlAttr($value,$data){
        $url = $this->prefixImgUrl($value,$data);
        return $url;
    }

    public static function getMostRecent($count) {
        $products = self::limit($count)
            ->order('create_time','desc')
            ->select();
        return $products;
    }
}
