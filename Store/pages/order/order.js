// pages/order/order.js
import {Cart} from "../cart/cart-model.js"
import {Address} from "../../utils/address";
import {Order} from "./order-model";

var cart = new Cart();
var address = new Address();
var order = new Order();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    loadingHidden: false,
    id : null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    // var productsArr;
    // this.data.account = options.account
    // productsArr = cart.getCartDataFromLocal(true)
    // this.setData({
    //   productsArr:productsArr,
    //   account:options.account,
    //   orderStatus:0
    // })
    //
    // /*显示收货地址*/
    // address.getAddress((res)=>{
    //     this._bindAddressInfo(res)
    // })

    /*
    * 订单数据来源包括两个：
    * 1.购物车下单
    * 2.旧的订单
    * */
    var from = options.from;
    if(from == 'cart'){
      this._fromCart(options.account)
    } else {
      this._fromOrder(options.id)
    }
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    if(this.data.id) {
      this._fromOrder(this.data.id)
    }
  },

  _fromCart(account){
    var productsArr;
    this.data.account = account;
    productsArr = cart.getCartDataFromLocal(true);
    this.setData({
      productsArr:productsArr,
      account:account,
      orderStatus:0
    })

    /*显示收货地址*/
    address.getAddress((res)=>{
      this._bindAddressInfo(res)
    })
  },

  _fromOrder(id){
    if(id) {
      //下单后，支付成功或者失败后，点左上角返回时能够更新订单状态 所以放在onshow中
      this.setData({
        id:id,
        loadingHidden: false,
      });
      order.getOrderInfoById(id, (data)=> {
        this.setData({
          orderStatus: data.status,
          productsArr: data.snap_items,
          account: data.total_price,
          basicInfo: {
            orderTime: data.create_time,
            orderNo: data.order_no
          },
        });

        // 快照地址
        var addressInfo=data.snap_address;
        addressInfo.totalDetail = address.setAddressInfo(addressInfo);
        this._bindAddressInfo(addressInfo);
      });
    }
  },



  /*修改或者添加地址信息*/
  editAddress: function (event) {
    var that = this;
    wx.chooseAddress({
      success(res) {
        console.log(res)
        var addressInfo = {
          name:res.userName,
          mobile:res.telNumber,
          totalDetail:address.setAddressInfo(res)
        }
        that._bindAddressInfo(addressInfo)
        //保存地址
        address.submitAddress(res,(flag)=>{
          if(!flag) {
            that.showTips('提示','地址信息更新失败！');
          }
        });
      }
    })
  },


  /*绑定地址信息*/
  _bindAddressInfo:function(addressInfo){
    this.setData({
      addressInfo: addressInfo,
      loadingHidden: true,
    });
  },

  /*下单和付款*/
  pay:function(){
    if(!this.data.addressInfo){
      this.showTips('下单提示','请填写您的收货地址');
      return;
    }
    if(this.data.orderStatus==0){
      this._firstTimePay();
    } else {
      this._oneMoresTimePay();
    }
  },

  //第一次支付
  _firstTimePay(){
    var orderInfo = [],
        productInfo = this.data.productsArr,
        order = new Order()

    for(let i=0;i<productInfo.length;i++){
      orderInfo.push({
        product_id:productInfo[i].id,
        count:productInfo[i].counts
      })
    }

    var that = this;
    //支付分两步，第一步是生成订单号，然后根据订单号支付
    order.doOrder(orderInfo,(data) => {
      //订单生成成功
      if(data.pass){
        //更新订单状态
        var id = data.order_id;
        that.data.id = id;
        // that.data.fromCartFlag=false;
        //开始支付
        that._execPay(id);

      } else {
        that._orderFail(data);  // 下单失败
      }
    })

  },


  /* 历史订单支付*/
  _oneMoresTimePay:function(){
    this._execPay(this.data.id);
  },

  /*
   * 开始支付
   * params:
   * id - {int}订单id
   */
   _execPay:function(id){
    // if(!order.onPay) {
    //     this.showTips('支付提示','本产品仅用于演示，支付系统已屏蔽',true);//屏蔽支付，提示
    //     this.deleteProducts(); //将已经下单的商品从购物车删除
    //     return;
    // }
    var that = this;
    order.execPay(id,(statusCode)=>{
      if(statusCode > 0){
        that.deleteProducts(); //将已经下单的商品从购物车删除   当状态为0时，表示
        var flag = statusCode == 2;
        wx.navigateTo({
          url: '../pay-result/pay-result?id=' + id + '&flag=' + flag + '&from=order'
        });
      } else if(statusCode== -1) {
          that.deleteProducts();
          wx.navigateTo({
            url: '../pay-result/pay-result?flag=0&id=0' + '&from=order'
          });
      }

    })
  },


  /*
  * 下单失败
  * params:
  * data - {obj} 订单结果信息
  * */
  _orderFail:function(data) {
    var nameArr = [],
        name = '',
        str = '',
        pArr = data.pStatusArray;
    for (let i = 0; i < pArr.length; i++) {
      if (!pArr[i].haveStock) {
        name = pArr[i].name;
        if (name.length > 15) {
          name = name.substr(0, 12) + '...';
        }
        nameArr.push(name);
        // if (nameArr.length >= 2) {
        //   break;
        // }
      }
    }
    // str += nameArr.join('、');
    if (nameArr.length > 2) {
      str += nameArr[0] + '、' + nameArr[1];
      str += ' 等';
    } else {
      str += nameArr.join('、');
    }
    str += ' 缺货';

    this.showTips('下单失败', str, false)
  },


  //将已经下单的商品从购物车删除
  deleteProducts(){
    var ids = [],
        arr = this.data.productsArr;
    for(let i = 0; i < arr.length; i++){
      ids.push(arr[i].id);
    }
    cart.delete(ids);
  },

  /*
  * 提示窗口
  * params:
  * title - {string}标题
  * content - {string}内容
  * flag - {bool}是否跳转到 "我的页面"
  */
    showTips:function(title,content,flag){
      wx.showModal({
        title: title,
        content: content,
        showCancel:false,
        success: function(res) {
          if(flag) {
            wx.switchTab({
              url: '/pages/my/my'
            });
          }
        }
      });
    },


})
