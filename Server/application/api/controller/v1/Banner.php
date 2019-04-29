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

        if(!$banner) {
            throw new BannerMissException();
        }
        return $banner;
    }
}
