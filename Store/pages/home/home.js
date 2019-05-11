// pages/home/home.js

import {Home} from './home-model.js';
var home = new Home;
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
    this._lodaData();
  },

//下划线个人表示private方法
  _lodaData(){
    // var data = home.getBannerData(1); 异步方法不能直接获取数据
    // 定义回调函数或者箭头函数
    // home.getBannerData(1,this.callback);
    var id = 1;
    home.getBannerData(id, (res)=>{
      //数据绑定
      this.setData({
        'bannerArr':res
      })
    });

    home.getThemeData((res)=>{
      this.setData({
        'themeArr':res
      })
    })

    home.getProductData((data) => {
      this.setData({
        'productsArr': data
      })
    })
  },


  /*跳转到商品详情*/
  onProductsItemTap:function(event){
    var id = home.getDataSet(event,'id');
    wx.navigateTo({
      url: '../product/product?id='+id,
    });
  },

  /*跳转到主题列表*/
  onThemesItemTap: function (event) {
    var id = home.getDataSet(event, 'id');
    var name = home.getDataSet(event, 'name');
    wx.navigateTo({
      url: '../theme/theme?id=' + id+'&name='+ name
    })
  },

  // callback:function(res){
  //   console.log(res);
  // }
})
