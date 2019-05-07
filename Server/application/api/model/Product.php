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

    public function imgs(){
        return $this->hasMany('ProductImage','product_id','id');
    }

    public function properties() {
        return $this->hasMany('ProductProperty','product_id','id');
    }

    public static function getProductDetail($id){

//支持使用数组方式定义嵌套预载入
//        $product = self::with(['properties',
//            'imgs'=> ['imgUrl']]
//            )
//            ->find($id);

//        $product = self::with(['properties','imgs.imgUrl'])
//            ->find($id);

        /* 闭包函数构建query查询器  */
        $product = self::with(['properties','imgs' => function($query){
            $query->with(['imgUrl'])
                ->order('order','asc');
        }])
            ->find($id);
        return $product;

//  问题：以下写法在5.1版本无效，之前5.0.X版本有效
//        $product = self::with([
//            'imgs' => function($query){
//                $query->with(['imgUrl'])
//                    ->order('order','asc');
//            }
//        ])
//            ->with(['properties'])
//            ->find($id);

    }
}
