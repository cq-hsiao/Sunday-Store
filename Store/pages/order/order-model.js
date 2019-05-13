import {Base} from "../../utils/base";

class Order extends Base{

    constructor(){
        super();
        this._storageKeyName = 'newOrder';
    }

    doOrder(param,callback){
        var that = this;
        var allParams = {
            url: 'order',
            type: 'POST',
            data:{
                products:param
            },
            sCallback:function (data) {
                that.execSetStorageSync(true);
                callback && callback(data)
            },
            eCallback:function (res) {
            }
        };
        this.request(allParams)
    }


    /*
    * 拉起微信支付
    * params:
    * oorderId - {int} 订单id
    * return：
    * callback - {obj} 回调方法 ，返回参数 可能值 0:商品缺货等原因导致订单不能支付;  1: 支付失败或者支付取消； 2:支付成功；
    * */
    execPay(orderId,callback){
        var allParams = {
            url : 'pay/pre_order',
            type: 'POST',
            data: {id:orderId},
            sCallback:function (data) {
                var timeStamp = data.timeStamp;
                if(timeStamp){ //可以支付
                  wx.requestPayment({
                      'timeStamp': timeStamp.toString(),
                      'nonceStr': data.nonceStr,
                      'package': data.package,
                      'signType': data.signType,
                      'paySign': data.paySign,
                      success: function () {
                          callback && callback(2);
                      },
                      fail: function () {
                          callback && callback(1);
                      }
                  })
                }else{
                    //服务器返回的错误，库存不足或缺货
                    callback && callback(0);
                }
            },
            //没有prepay_id等
            eCallback(data){
                callback && callback(-1);
            }
        };
        this.request(allParams,true);
    }

    /*获得订单的具体内容*/
    getOrderInfoById(id,callback){
        var that=this;
        var allParams = {
            url: 'order/'+id,
            sCallback: function (data) {
                callback &&callback(data);
            },
            eCallback:function(){

            }
        };
        this.request(allParams);
    }

    /*获得所有订单,pageIndex 从1开始*/
    getOrders(pageIndex,callback){
        var allParams = {
            url: 'order/by_user',
            data:{page:pageIndex},
            type:'get',
            sCallback: function (data) {
                callback && callback(data);  //1 未支付  2，已支付  3，已发货，4已支付，但库存不足
            }
        };
        this.request(allParams);
    }

    /*是否有新的订单*/
    hasNewOrder(){
        var flag = wx.getStorageSync(this._storageKeyName);
        return flag==true;
    }

    /*本地缓存 保存／更新*/
    execSetStorageSync(data){
        wx.setStorageSync(this._storageKeyName, data);
    }
}

export {Order}
