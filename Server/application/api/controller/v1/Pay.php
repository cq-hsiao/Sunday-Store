<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/8
 * Time: 16:48
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    //获得微信预订单信息
    public function getPreOrder($id='')
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    //微信异步回调
    public function receiveNotify()
    {
        // 通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒
        // 检查库存量（超卖，高并发） 更新订单状态 减库存
        // 成功处理返回微信处理成功的信息，否则需要返回失败。
        // 特点： POST方式携带XML格式参数

        $notify = new WxNotify();
        $config= new \WxPayConfig();
        $notify->Handle($config);
    }


    //调试接口
//    public function receiveNotify()
//    {
//        $xmlData = file_get_contents('php://input');
//        Log::error($xmlData);
//        $notify = new WxNotify();
//        $config= new \WxPayConfig();
//        $notify->Handle($config);
//        $xmlData = file_get_contents('php://input');
//        $result = curl_post_raw('http:/zerg.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=13133',
//            $xmlData);
//        return $result;
//        Log::error($xmlData);
//    }
}
