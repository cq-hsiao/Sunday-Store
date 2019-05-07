<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/7
 * Time: 0:47
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['delete_time','img_id','product_id'];

    public function imgUrl()
    {
        return $this->belongsTo('Image','img_id','id');
    }
}
