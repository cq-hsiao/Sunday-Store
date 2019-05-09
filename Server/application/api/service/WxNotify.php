<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/8
 * Time: 23:37
 */

namespace app\api\service;

use app\api\model\Order as OrderModel;
use app\api\model\Product;
use app\lib\enum\OrderStatusEnum;
use app\api\service\Order as OrderService;
use think\Db;
use think\facade\Log;


//    protected $data = <<<EOD
//<xml><appid><![CDATA[wxaaf1c852597e365b]]></appid>
//<bank_type><![CDATA[CFT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[N]]></is_subscribe>
//<mch_id><![CDATA[1392378802]]></mch_id>
//<nonce_str><![CDATA[k66j676kzd3tqq2sr3023ogeqrg4np9z]]></nonce_str>
//<openid><![CDATA[ojID50G-cjUsFMJ0PjgDXt9iqoOo]]></openid>
//<out_trade_no><![CDATA[A301089188132321]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[944E2F9AF80204201177B91CEADD5AEC]]></sign>
//<time_end><![CDATA[20170301030852]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4004312001201703011727741547]]></transaction_id>
//</xml>
//EOD;

require_once __DIR__."/../../../extend/WxPay/WxPay.Api.php";

/**
 * Class WxNotify 微信回调类
 * @package app\api\service
 */
class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($objData, $config, &$msg)
    {

        if($objData['result_code'] == 'SUCCESS'){
            $orderNo = $objData['out_trade_no'];
            //增加事务和锁，防止多次减库存
            Db::startTrans();
            try{
                $order = OrderModel::where('order_no','=',$orderNo)
                    ->lock(true)->find();  //增加锁
                if($order->status == 1){
                    //判断库存量
                    $service = new OrderService();
                    $stockStatus = $service->checkOrderStock($order->id);
                    if($stockStatus['pass']) {
                        $this->updateOrderStatus($order->id,true);
                        $this->reduceStock($stockStatus);
                    } else {
                        $this->updateOrderStatus($order->id,false);
                    }
                }
                Db::commit();
                return true;
            } catch (\Exception $ex) {
                Db::rollback();
                Log::write($ex,'error');
                // 如果出现异常，向微信返回false，请求重新发送通知
                return false;
            }
        } else {
            //微信支付失败，不再回调通知
            return true;
        }
    }


    private function reduceStock($stockStatus)
    {
        foreach ($stockStatus['pStatusArray'] as $item){
            Product::where('id','=',$item['product_id'])
                ->setDec('stock',$item['count']);
        }
    }

    private function updateOrderStatus($orderID,$success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id','=',$orderID)
            ->update(['status'=>$status]);
    }
}
