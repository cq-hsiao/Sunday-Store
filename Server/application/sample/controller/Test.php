<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/4/28
 * Time: 17:30
 */

namespace app\sample\controller;
//use think\facade\Request;
use think\Request;
use think\Controller;

//1.函数方法对应参数获取变量 function hello($id,$name...)
//2.依赖注入：当前的请求对象由think\Request类负责，在很多场合下并不需要实例化调用，通常使用依赖注入即可
//2-1.构造方法注入 如果你继承了系统的控制器基类think\Controller的话，系统已经自动完成了请求对象的构造方法注入了，你可以直接使用$this->request属性调用当前的请求对象。
//2-2.操作方法注入 public function index(Request $request)。
//3.Facade调用 在其它场合（例如模板输出等）或无法使用依赖注入场合，则可以使用think\facade\Request静态类操作（注意use引入的类库区别）。 Request::param('name')
//4.使用助手函数request或input获取参数变量 request()->param('id'); $all = input('param.');

class Test
{




//    public function hello(Request $request){


//        1、函数方法对应参数获取变量 function hello($id,$name...)

//        2、使用Request对象获取参数变量
//        $id = Request::instance()->param('id');
//        5.1版本Request类不再需要instance方法，直接调用类的方法即可。
//        $name = Request::param('name');
//        $age = Request::param('age');
//        $all = Request::param();

//        3、使用助手函数input获取参数变量
//        $all = input('get.');
//        $all = input('param.');
//        $name = input('param.name');

//        4、依赖注入
//        $all = $request->param();
//        var_dump($all);

//        5、继承系统的控制器基类直接调用当前请求对象
//        $all =$this->request->param();
//        var_dump($this);

//    }
}
