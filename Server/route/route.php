<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------


//方式一：配置式编写方式
//return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
//];

//方式二：动态注册，易读性强，灵活
//Route::rule('路由表达式','路由地址','请求类型')-> 请求参数 https(false) -> 变量规则 pattern(['name' => '\w+']);;
//Route::rule('hello/:id','sample/Test/hello','GET|POST')->https(false);
//Route::get('hello/:id','sample/Test/hello');
// http://xxx.com/hello/2?name=xxx


// 设置name变量规则（采用正则定义）
Route::pattern('name', '\w+');
// 支持批量添加
Route::pattern([
    'name' => '\w+',
    'id'   => '.*',
]);


/**
 * 路由注册
 * 使用路由分组可以简化定义
 * 并在一定程度上提高路由匹配的效率
 */

//Banner
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');

//Theme
Route::get('api/:version/theme','api/:version.Theme/getSimpleList');
Route::get('api/:version/theme/:id','api/:version.Theme/getOneTheme');

//Product

//Route::get('api/:version/product/:id', 'api/:version.Product/getOne')->pattern(['id' => '\d+']);
//Route::get('api/:version/product/by_category', 'api/:version.Product/getAllInCategory');
//Route::get('api/:version/product/recent','api/:version.Product/getRecent');

// 如果要使用分组路由，建议使用闭包的方式，数组的方式不允许有同名的key
//Route::group('api/:version/theme',[
//    '' => ['api/:version.Theme/getThemes'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct'],
//    ':t_id/product/:p_id' => ['api/:version.Theme/addThemeProduct']
//]);
Route::group('api/:version/product',function (){
    Route::get('/by_category', 'api/:version.Product/getAllInCategory');
    Route::get('/:id','api/:version.Product/getOne');
    Route::get('/recent','api/:version.Product/getRecent');
})->pattern(['id' => '\d+']);

//Category
Route::get('api/:version/category/all','api/:version.Category/getAllCategories');


//Token
Route::post('api/:version/token/user','api/:version.Token/getToken');
Route::post('api/:version/token/verify', 'api/:version.Token/verifyToken');

//Address
Route::post('api/:version/address', 'api/:version.Address/createOrUpdateAddress');
Route::get('api/:version/address', 'api/:version.Address/getUserAddress');

//Order
Route::post('api/:version/order','api/:version.Order/prepareOrder');
Route::get('api/:version/order/by_user','api/:version.Order/getSummaryByUser');
Route::get('api/:version/order/:id','api/:version.Order/getDetail',[],['id'=>'\d+']);

//Pay
Route::post('api/:version/pay/pre_order','api/:version.Pay/getPreOrder');
Route::post('api/:version/pay/notify', 'api/:version.Pay/receiveNotify');
