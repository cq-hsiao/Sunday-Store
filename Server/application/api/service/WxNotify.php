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

require_once __DIR__."/../../../extend/WxPay/WxPay.Api.php";

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($objData, $config, &$msg)
    {
        $config= new \WxPayConfig();

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
