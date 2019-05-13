// pages/product/product.js
import {Product} from "./product-model.js"
import { Cart } from "../cart/cart-model.js"
var product = new Product();
var cart = new Cart();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    countsArray: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    productCounts: 1,
    tagContent: ['商品详情', '产品参数', '售后保障'],
    currentTabsIndex : 0,
    cartTotalCounts:0,
    loadingHidden: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var id = options.id;
    this.data.id = id;
    this._loadData();

  },

  _loadData: function () {
    product.getDetailInfo(this.data.id,(data)=>{

      this.setData({
        cartTotalCounts:cart.getCartTotalCounts().counts1,
        product: data,
        loadingHidden: true
      })
    })
  },

  //选择购买数目
  bindPickerChange: function(event){
    this.setData({
      productCounts:this.data.countsArray[event.detail.value]
    })
  },

  //切换详情面板
  onTabsItemTap:function(event){
    var index = product.getDataSet(event,'index');
    this.setData({
      currentTabsIndex:index
    });
  },

  onAddingToCartTap:function (event) {
    //防止快速点击
    if(this.data.product.stock == 0) {
      wx.showToast({
        title:"该商品库存不足~",
        icon:"none",
        mask:true
      })
      return false
    }
    if (this.data.isFly) {
      return;
    }
    this._flyToCartEffect(event);
    this.addToCart();
    // var counts = this.data.cartTotalCounts + this.data.productCounts
    // this.setData({
    //   cartTotalCounts: cart.getCartTotalCounts().counts1
    // })
  },

  addToCart:function () {
    var tempObj = {};
    var keys = ['id','name','main_img_url','price']
    for(var key in this.data.product){
      if(keys.indexOf(key) >= 0){
        tempObj[key] = this.data.product[key]
      }
    }
    cart.add(tempObj, this.data.productCounts);
  },


  /*加入购物车动效*/
  _flyToCartEffect: function (events) {
    //获得当前点击的位置，距离可视区域左上角
    var touches = events.touches[0];

    var diff = {
      x: '25px',
      y: 25 - touches.clientY + 'px'
    },
      style = 'display: block;-webkit-transform:translate(' + diff.x + ',' + diff.y + ') rotate(350deg) scale(0)';  //移动距离
    this.setData({
      isFly: true,
      translateStyle: style
    });
    var that = this;
    setTimeout(() => {
      that.setData({
        isFly: false,
        translateStyle: '-webkit-transform: none;',  //恢复到最初状态
        isShake: true,
      });
      setTimeout(() => {
        var counts = that.data.cartTotalCounts + that.data.productCounts;
        that.setData({
          isShake: false,
          cartTotalCounts: counts
        });
      }, 200);
    }, 1000);
  },

  /*跳转到购物车*/
  onCartTap:function(){
    wx.switchTab({
      url: '/pages/cart/cart'
    });
  },
})
