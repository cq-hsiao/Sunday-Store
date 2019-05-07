<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/7
 * Time: 0:54
 */

namespace app\api\model;


class ProductProperty extends BaseModel
{
    protected $hidden = ['delete_time','id','product_id'];
}
