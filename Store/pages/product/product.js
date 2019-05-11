// pages/product/product.js
import {Product} from "./product-model.js"

var product = new Product();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    countsArray: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
    productCounts: 1,
    tagContent: ['商品详情', '产品参数', '售后保障'],
    currentTabsIndex : 0
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
      console.log(data)
      this.setData({
        product: data,
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
  }

  
})