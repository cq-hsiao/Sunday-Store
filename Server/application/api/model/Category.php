<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 20:06
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['delete_time','update_time','create_time'];

    public function img()
    {
        return $this->belongsTo('Image','topic_img_id','id');
    }


}
