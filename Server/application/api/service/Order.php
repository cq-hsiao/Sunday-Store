<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/8
 * Time: 0:34
 */

namespace app\api\service;


use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Db;
use think\Exception;

class Order
{
    //订单的商品列表，即客户端传递过来的参数
    protected $oProducts;
    //数据库商品信息
    protected $products;
    protected $uid;

    /**
     * @param int $uid 用户id
     * @param array $oProducts 订单商品列表
     * @return array 订单商品状态
     * @throws Exception
     */
    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->uid = $uid;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();

        if(!$status['pass']){
            $status['order_id'] = -1;
            return $status;
        }

        //开始创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;

        return $order;
    }

    /**
     * 生成订单快照
     * @param $status
     * @return array
     * @throws UserException
     */
    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => ''
        ];

        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];

        if(count($this->products) > 1){
            $snap['snapName'] .= ' 等 '.$status['totalCount'].' 件商品';
        }
        return $snap;
    }

    // 创建订单时没有预扣除库存量，简化处理
    // 如果预扣除了库存量需要队列支持，且需要使用锁机制
    private function createOrder($snap)
    {
        Db::startTrans();
        try{

            $orderNo = self::makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();

            $orderID = $order->id;
            $create_time = $order->create_time;

            foreach ($this->oProducts as &$p){
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);

            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
            Db::commit();
        } catch (\Exception $ex){
            Db::rollback();
            throw $ex;
        }


    }

    public function getProductsByOrder($oProducts){
        //注意避免循环查询数据库
        $IDs = [];
        foreach ($oProducts as $item){
            array_push($IDs,$item['product_id']);
        }
        $products = Product::where('id','in',$IDs)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->select()
            ->toArray();
//        $products = Product::all($IDs)
//            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
//            ->toArray();
        return $products;
    }

    /**
     * @return array 获得订单的状态信息和所有商品信息
     * @throws OrderException
     */
    private function getOrderStatus()
    {
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $item){
            $productInfo = $this->getProductStatus(
                $item['product_id'],$item['count'],$this->products);

            if(!$productInfo['haveStock']){
                $status['pass'] = false;
            }
            $status['orderPrice'] += $productInfo['totalPrice'];
            $status['totalCount'] += $productInfo['counts'];
            array_push($status['pStatusArray'],$productInfo);
        }
        return $status;
    }

    /**
     * @param $oPID
     * @param $oCount
     * @param  array $products
     * @return array 与数据库商品做校验,返回单个商品的状态和信息
     * @throws OrderException
     */
    private function getProductStatus($oPID,$oCount,$products)
    {
        $pIndex = -1;
        $productInfo = [
            'id' => null,
            'haveStock' => false,
            'counts' => 0,
            'price' => 0,
            'main_img_url' => '',
            'name' => '',
            'totalPrice' => 0
        ];

        for($i=0;$i<count($products);$i++){
            if($oPID == $products[$i]['id']){
                $pIndex = $i;
            }
        }

        if($pIndex == -1) {
            throw new OrderException([
                'msg' => 'id为' . $oPID . '的商品不存在，订单创建失败'
            ]);
        } else {
            $product = $products[$pIndex];
            $productInfo['id'] = $product['id'];
            $productInfo['name'] = $product['name'];
            $productInfo['counts'] = $oCount;
            $productInfo['price'] = $product['price'];
            $productInfo['main_img_url'] = $product['main_img_url'];
            $productInfo['totalPrice'] = $product['price'] * $oCount;

            if ($product['stock'] - $oCount >= 0) {
                $productInfo['haveStock'] = true;
            }
        }
        return $productInfo;
    }

    private function getUserAddress(){
        $userAddress = UserAddress::where('user_id','=',$this->uid)
            ->find()->toArray();
        if(!$userAddress){
            throw new UserException([
                'msg' => '用户收货地址不存在，下单失败！',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }

    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J','K','L','M','N');
        $orderSn =
            $yCode[intval(date('Y')) - 2019] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
}
