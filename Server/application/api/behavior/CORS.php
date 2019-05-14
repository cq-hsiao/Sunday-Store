<?php


namespace app\api\behavior;

class CORS
{
    public function appInit()
    {
        header('Access-Control-Allow-Origin: *'); //允许所有域访问API
        //header允许携带的键值对 token...
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET,PUT');
        //如果是OPTIONS请求则中断，因为找不到接口
        if(request()->isOptions()){
            exit();
        }
    }
}





