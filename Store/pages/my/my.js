// pages/my/my.js

import {Address} from "../../utils/address";
import {My} from "./my-model";
import {Order} from "../order/order-model";

var address = new Address();
var my = new My();
var order = new Order();

Page({


  data: {
      pageIndex:1,
      isLoadedAll:false,
      loadingHidden:false,
      orderArr:[],
      addressInfo:null
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this._loadData();
  },

/**
 * 生命周期函数--监听页面显示
 */
onShow: function () {
    //更新订单,相当自动下拉刷新,只有  非第一次打开 “我的”页面，且有新的订单时 才调用。
    var newOrderFlag=order.hasNewOrder();
    if(this.data.loadingHidden && newOrderFlag){
        this.onPullDownRefresh();
    }
},

 _loadData(){
    my.getUserInfo((data) => {
        this.setData({
            userInfo : data
        })
    });

    //获取地址信息
     address.getAddress((addressInfo) => {
        this._bindAddressInfo(addressInfo)
     })

     this._getOrders();
     // order.execSetStorageSync(false);  //更新标志位
 },

    /*下拉刷新页面*/
    onPullDownRefresh: function(){
        var that=this;
        this.data.orderArr=[];  //订单初始化
        this._getOrders(()=>{
            that.setData({
                isLoadedAll : false,
                pageIndex : 1
            });
            // that.data.isLoadedAll=false;  //是否加载完全
            // that.data.pageIndex=1;
            wx.stopPullDownRefresh();
            order.execSetStorageSync(false);  //更新标志位
        });
    },

    _bindAddressInfo(addressInfo){
        this.setData({
            addressInfo:addressInfo,
        });
    },

    /*修改或者添加地址信息*/
    editAddress: function (event) {
        var that = this;
        wx.chooseAddress({
            success(res) {

                var addressInfo = {
                    name:res.userName,
                    mobile:res.telNumber,
                    totalDetail:address.setAddressInfo(res)
                }

                //保存地址
                address.submitAddress(res,(flag)=>{
                    if(!flag) {
                        that.showTips('提示','地址信息更新失败,请重试！');
                    } else {
                        that._bindAddressInfo(addressInfo)
                        wx.showToast({
                            title: '更新成功',
                            mask: true
                        });
                    }
                });
            }
        })
    },

    _getOrders(callback){
        var that = this;
        order.getOrders(this.data.pageIndex,(res) => {
            var data = res.data.data;
            that.setData({
                loadingHidden: true
            });

            if(data && data.length > 0) {
                that.data.orderArr.push.apply(that.data.orderArr,data) //数组合并
                that.setData({
                    orderArr: that.data.orderArr
                });
            }else{
                that.setData({
                    isLoadedAll : true //已经全部加载完毕
                });
                // that.data.isLoadedAll=true;  //已经全部加载完毕
                that.data.pageIndex=1;
            }
            callback && callback();
        })
    },


    /*未支付订单再次支付*/
    rePay:function(event){
        var id=order.getDataSet(event,'id'),
            index=order.getDataSet(event,'index');

        //online 上线实例，屏蔽支付功能
        if(order.onPay) {
            this._execPay(id,index);
        }else {
            this.showTips('支付提示','本产品仅用于演示，支付系统已屏蔽');
        }
    },

    /*支付*/
    _execPay:function(id,index){
        var that=this;
        order.execPay(id,(statusCode)=>{
            if(statusCode>0){
                var flag=statusCode==2;

                //更新订单显示状态
                if(flag){
                    that.data.orderArr[index].status=2;
                    that.setData({
                        orderArr: that.data.orderArr
                    });
                }

                //跳转到 成功页面
                wx.navigateTo({
                    url: '../pay-result/pay-result?id='+id+'&flag='+flag+'&from=my'
                });
            }
            else if(statusCode== -1) {
                this.showTips('支付提示','本产品仅用于演示，支付系统已屏蔽',false);
                wx.navigateTo({
                    url: '../pay-result/pay-result?flag=0&id='+id+ '&from=my'
                });
            }
            else{
                that.showTips('支付失败','商品已下架或库存不足');
            }
        });
    },


    /*显示订单的具体信息*/
    showOrderDetailInfo:function(event){
        var id=order.getDataSet(event,'id');
        wx.navigateTo({
            url:'../order/order?from=order&id='+id
        });
    },

    //上拉加载
    onReachBottom:function(){
        if(!this.data.isLoadedAll) {
            this.data.pageIndex++;
            this._getOrders();
        }
    },

    //监听用户滑动页面事件
    onPageScroll:function(event){
        if(event.scrollTop >= 1000 && !this.data.showTop){
            this.setData({
                showTop:true
            })
        }
        if(event.scrollTop < 1000 && this.data.showTop){
            this.setData({
                showTop:false
            })
        }
    },

    //返回页首
    goTop(){
      wx.pageScrollTo({
          scrollTop: 0,
          duration: 300
      })

        this.setData({
            showTop:false
        })
    },


    /*
     * 提示窗口
     * params:
     * title - {string}标题
     * content - {string}内容
     */
    showTips:function(title,content){
        wx.showModal({
            title: title,
            content: content,
            showCancel:false,
            success: function(res) {
            }
        });
    }


})
