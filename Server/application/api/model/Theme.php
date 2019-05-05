<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 16:46
 */

namespace app\api\model;


class Theme extends  BaseModel
{
    /**
     * 关联Image
     * 要注意belongsTo和hasOne的区别
     * 带外键的为主表 一般定义belongsTo，另外一方定义hasOne
     */
    public function topicImg()
    {
        return $this->belongsTo('Image','topic_img_id','id');
    }


    public function headImg()
    {
        return $this->belongsTo('Image','head_img_id','id');
    }

    /**
     * 关联product，多对多关系
     */
    public function products()
    {
        return $this->belongsToMany(
            'Product','theme_product','product_id','theme_id');
    }

    public static function getThemeWithProducts($id)
    {
        $theme = self::with(['topicImg','headImg','products'])
            ->find($id);

        return $theme;
    }
}
