<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/7
 * Time: 23:25
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Order as OrderService;
use app\api\service\Token as TokenService;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPrepare;
use app\api\validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'prepareOrder'],
        'checkPrimaryScope' => ['only' => 'getSummaryByUser,getDetail'],
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


    /**
     * 根据用户id分页获取订单列表（简要信息）
     * @param int $page
     * @param int $size
     * @return array
     * @throws \app\lib\exception\ParameterException
     */
    public function getSummaryByUser($page = 1,$size = 15)
    {
        (new PagingParameter())->goCheck();
        $uid = TokenService::getCurrentUID();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);

        if($pagingOrders->isEmpty())
        {
            //不建议抛异常和return null
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }

        $data = $pagingOrders->hidden(['snap_items', 'snap_address','prepay_id'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    /**
     * 获取订单详情
     * @param $id
     * @return static
     * @throws OrderException
     * @throws \app\lib\exception\ParameterException
     */
    public function getDetail($id)
    {
        //由于'snap_items', 'snap_address'是JSON字符串，用读取器将其转化为JSON对象
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if (!$orderDetail)
        {
            throw new OrderException();
        }
        return $orderDetail
            ->hidden(['prepay_id']);
    }
}
