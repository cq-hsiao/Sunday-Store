<?php

namespace app\api\controller\v1;

use app\api\model\Theme as ThemeModel;
use app\api\validate\IDCollection;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ThemeException;


/**
 * 主题推荐,主题指首页里多个聚合在一起的商品
 * 注意同专题区分
 * 常规的REST服务在创建成功后，需要在Response的
 * header里附加成功创建资源的URL，但这通常在内部开发中
 * 并不常用，所以本项目不采用这种方式
 */

class Theme
{
    /**
     * @url     /theme?ids=:id1,id2,id3...
     * @return  array of theme
     * @throws  ThemeException
     * @note 实体查询分单一和列表查询，可以只设计一个接收列表接口，
     *       单一查询也需要传入一个元素的数组
     *       对于传递多个数组的id可以选用post传递、
     *       多个id+分隔符或者将多个id序列化成json并在query中传递
     */
    public function getSimpleList($ids='')
    {
        (new IDCollection())->goCheck();
        $ids = explode(',',$ids);
        $result = ThemeModel::with(['topicImg','headImg'])
            ->select($ids);

        if($result->isEmpty()) {
            throw new ThemeException();
        }

        return $result;
    }


    public function getOneTheme($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $theme = ThemeModel::getThemeWithProducts($id);

        if(!$theme){
            throw new ThemeException();
        }
//        return $theme;
        return $theme->hidden(['products.summary'])->toArray();
    }
}
