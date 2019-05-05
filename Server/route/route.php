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
Route::get('api/:version/banner/:id','api/:version.Banner/getBanner');
