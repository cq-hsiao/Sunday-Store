<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/8
 * Time: 16:55
 */

namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Log;


// extend/WxPay/WxPay.Api.php 导入所需的类库
// Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');
//因为严格遵循PSR-4规范，不再建议手动导入类库文件，所以5.1版本开始取消了Loader::import方法以及import和vendor助手函数，
//推荐全面采用命名空间方式的类以及自动加载机制，如果必须使用请直接改为php内置的include或者require语法。

//在extend中带有命名空间的类会被自动加载
//use Wxpay\WxPayApi;
//use Wxpay\WxPayUnifiedOrder;

require_once __DIR__."/../../../extend/WxPay/WxPay.Api.php";

class Pay
{
    private $orderID;
    private $orderNo;

    function __construct($orderID)
    {
        if(!$orderID){
            throw new Exception('订单号不允许为NULL');
        }
        $this->orderID = $orderID;
    }

    public function pay()
    {
        //订单可能不存在
        //订单存在，但与当前用户不匹配
        //订单已经被支付
        $this->checkOrderValid();
        //判断以上再进行库存量检测
        $orderService = new Order();
        $status = $orderService->checkOrderStock($this->orderID);
        if (!$status['pass'])
        {
            return $status;
        }

        //生成微信预订单
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    /**
     * 检查订单是否存在，是否与用户匹配，是否已支付
     * @return bool
     * @throws Exception
     * @throws OrderException
     * @throws TokenException
     */
    private function checkOrderValid()
    {
        $order = OrderModel::where('id','=',$this->orderID)
            ->find();
        if(!$order){
            throw new OrderException();
        }

        //判断订单号是否与当前用户匹配
        if(!Token::isValidOperate($order->user_id)){
            throw new TokenException([
                'msg' => '订单与用户不匹配',
                'errorCode' => 10003
            ]);
        }

        if($order->status != OrderStatusEnum::UNPAID){
            throw new OrderException([
                'msg' => '订单不是待支付状态',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }

        $this->orderNo = $order->order_no;
        return true;
    }


    private function makeWxPreOrder($totalPrice){

        $openid = Token::getCurrentTokenVar('openid');
        if(!$openid){
            throw new TokenException();
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody('Sunday杂货店');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('weixin.pay_back_url')); //接收回调通知

        return $this->getPaySignature($wxOrderData);

    }

    //向微信请求订单号并生成签名
    private function getPaySignature($wxOrderData)
    {
        $config= new \WxPayConfig();
        $wxOrder = \WxPayApi::unifiedOrder($config,$wxOrderData);
        // 失败时不会返回result_code
        if($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] !='SUCCESS'){
//            Log::record($wxOrder,'error');
//            Log::record('获取预支付订单失败','error');
            $wxOrder['write_msg'] =  '获取预支付订单失败';
            Log::write($wxOrder,'error');
//            throw new Exception('获取预支付订单失败');
        }

        if(empty($wxOrder['prepay_id'])){
            throw new OrderException([
                'msg' => '没有返回prepay_id，支付失败',
                'errorCode' => 80005,
            ]);
        }
        //保存prepay_id
        $this->recordPreOrder($wxOrder);
        //生成签名
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id','=',$this->orderID)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);

    }

    //生成签名并返回数据
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('weixin.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0,1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']); //必须带 prepay_id= ，这是一个坑
        $jsApiPayData->SetSignType('md5');
        $config= new \WxPayConfig();
        $sign = $jsApiPayData->MakeSign($config);
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
        unset($rawValues['appId']); //没必要返回客户端
        return $rawValues;
    }
}
