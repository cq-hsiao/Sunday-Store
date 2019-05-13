// pages/category/category.js

import {Category} from "./category-model.js"
var category = new Category()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    transClassArr: ['tanslate0', 'tanslate1', 'tanslate2', 'tanslate3', 'tanslate4', 'tanslate5'],
    currentMenuIndex: 0,
    // productInfo:[],
    loadingHidden: false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    this._loadDate()
  },

  _loadDate:function(callback){
    category.getCategoryData((categoryData)=>{
      this.setData({
        categoryTypeArr: categoryData
      });
      //异步回调-要在回调里再进行获取分类商品详情
      category.getProductsByCategory(categoryData[0].id,(data)=>{
        var dataObj = {
          product: data,
          topImgUrl: categoryData[0].img.url,
          title: categoryData[0].name
        }


        this.setData({
          loadingHidden: true,
          'productInfo[0]': dataObj,
        });

        // this.setData({
        //   categoryInfo0: dataObj,
        // });
        callback && callback();
      })
    })
  },

  //切换分类
  changeCategory:function (event) {
    // console.log(this);
    // console.log(category)
    var index = category.getDataSet(event, 'index');
    var id = category.getDataSet(event, 'id');
    this.setData({
      currentMenuIndex: index,
    });


    //拒绝频繁向服务器发送请求,如果数据是第一次请求
    if(!this.isLoadedData(index)){
      // var that = this;
      // this.getProductsByCategory(id, (data) => {
      //   that.setData(that.getDataObjForBind(index, data));
      // })

      this.setData({
        loadingHidden: false
      })
      category.getProductsByCategory(id,(data)=>{
        this.setData(this.getDataObjForBind(index, data))
      })
    }
  },

  isLoadedData:function(index){
    // if(this.data['categoryInfo'+index]){
    //   return true
    // } else {
    //   return false
    // }
    return this.data.productInfo[index] ? true : false
  },

  getDataObjForBind:function(index,data){
        // var obj = {},
        //   arr = [0, 1, 2, 3, 4, 5],
        //   baseData = this.data.categoryTypeArr[index];
        // for (var item in arr) {
        //   if (item == arr[index]) {
        //     obj['categoryInfo' + item] = {
        //       product: data,
        //       topImgUrl: baseData.img.url,
        //       title: baseData.name
        //     };

        //     return obj;
        //   }
        // }

        var selectType = this.data.categoryTypeArr[index];

        var obj = {};
        obj['productInfo['+index+']'] = {
            product: data,
            topImgUrl: selectType.img.url,
            title: selectType.name
          }
        obj['loadingHidden'] = true

        return obj;

  },

  getProductsByCategory: function (id, callback) {
    category.getProductsByCategory(id, (data) => {
      callback && callback(data);
    });
  },

  onProductsItemTap: function (event) {
    var id = category.getDataSet(event, 'id');
    wx.navigateTo({
      url: '../product/product?id=' + id
    })
  }

})
