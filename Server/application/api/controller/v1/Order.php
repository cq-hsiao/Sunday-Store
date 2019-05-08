<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/7
 * Time: 23:25
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\OrderPrepare;
use app\api\service\Token as TokenService;
use app\api\service\Order as OrderService;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'prepareOrder']
    ];

    /**
     * 下单
     * @url /order
     * @HTTP POST
     */
    public function prepareOrder()
    {
        (new OrderPrepare())->goCheck();
        $products = input('post.products/a');
        $uid = TokenService::getCurrentUID();

        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }
}
