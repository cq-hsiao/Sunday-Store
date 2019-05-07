<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 18:49
 */

namespace app\api\controller\v1;


use app\api\validate\Count;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;
use think\Exception;

class Product
{
    /**
     * 获取指定数量的最近商品
     * @url /product/recent?count=:count
     * @param int $count
     * @throws Exception
     * @return array
     */
    public function getRecent($count=15)
    {
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        if($products->isEmpty()){
            throw new ProductException();
        }

//        5.0.X版本 在database配置文件修改数据集返回类型为collection
//        $collection = collection($products);
//        $products = $collection->hidden(['summary']);

//        5.1版本 助手函数取消collection方法   resultSetToModelCollection
//        模型的all方法或者select方法返回的是一个包含多个模型实例的数据集对象（默认为\think\model\Collection）

//        注意：如果要判断数据集是否为空，不能直接使用empty判断，而必须使用数据集对象的isEmpty方法判断

        $products = $products->hidden(
            [
                'summary'
            ]);
//            ->toArray();
        return $products;
    }

    /**
     * 获取某分类下全部商品(不分页）
     * @url /product/all?id=:category_id
     * @param int $id 分类id号
     * @return object
     * @throws Exception
     */
    public function getAllInCategory($id = 0)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::where('category_id','=',$id)->select();
        if($products->isEmpty()) {
            throw new ProductException();
        }
        $data = $products
            ->hidden(['summary']);

        return $data;
    }

    //获得商品详情
    public function getOne($id='')
    {
        (new IDMustBePositiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }
}
