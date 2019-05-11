// pages/theme/theme.js

import {Theme} from "./theme-model.js"

var theme = new Theme

Page({

  /**
   * 页面的初始数据
   */
  data: {

  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    // var id = options.id;
    // var name = options.name;
    // this._loadData(id);
    this.data.id = options.id;
    this.data.name = options.name;
    
    this._loadData();
  },

  onReady: function () {
    wx.setNavigationBarTitle({
      title: this.data.name
    })
  },

  _loadData:function(){
    theme.getProductsData(this.data.id,(data)=>{
      this.setData({
        themeInfo:data
      })
    })
  },


  /*跳转到商品详情*/
  onProductsItemTap: function (event) {
    var id = theme.getDataSet(event,'id')
    wx.navigateTo({
      url: '../product/product?id='+id,
    })
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    return {
      title: 'Sunday 杂货店 Pretty Vendor',
      path: 'pages/theme/theme?id=' + this.data.id
    }
  }
})