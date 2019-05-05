<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/5
 * Time: 20:04
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\lib\exception\CategoryException;

class Category
{
    public function getAllCategories(){
        $categories = CategoryModel::all([],'img');
//        $categories = CategoryModel::with('img')->select();
//        $categories = $categories->hidden(['img.update_time']);
        if(!$categories){
            throw new CategoryException();
        }
        return $categories;
    }


}
