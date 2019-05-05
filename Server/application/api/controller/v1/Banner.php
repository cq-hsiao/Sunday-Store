<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/4/28
 * Time: 22:25
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;
use think\Exception;

class Banner
{
    /**
     * 获得指定id的banner信息
     * @url     /banner/:id
     * @http    get
     * @param   int $id banner id
     * @return  object
     * @throws  Exception
     * @throws  BannerMissException
     */
    public function getBanner($id){

//        $data = [
//            'id' => $id
//        ];
//
//        $validate = new IDMustBePositiveInt();
//        $result = $validate->batch()
//            ->check($data);
//        var_dump($validate->getError());

        (new IDMustBePositiveInt())->goCheck();

        $banner = BannerModel::getBannerByID($id);


//  模型查询的最佳实践原则是：在模型外部使用静态方法进行查询，内部使用动态方法查询，包括使用数据库的查询构造器。模型的查询始终返回对象实例，但可以和数组一样使用。

        //静态调用（推荐）
//         $banner = BannerModel::with(['items','items.img'])->find($id);
        //实例化对象调用
//        $banner = new BannerModel();
//        $banner = $banner->get();

        if(!$banner) {
            throw new BannerMissException();
        }
        return $banner; //框架自动调用toArray
    }
}
