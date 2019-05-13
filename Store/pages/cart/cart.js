// pages/cart/cart.js

import { Cart } from "./cart-model.js";
var cart = new Cart();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    loadingHidden:false,
    selectedCounts: 0, //总的商品数
    selectedTypeCounts: 0, //总的商品类型数
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

  },



  /**
   * 生命周期函数--监听页面显示
   * 页面重新渲染(包括第一次),从缓存拿数据，不会对服务器端造成压力
   */
  onShow: function () {
    var cartData = cart.getCartDataFromLocal();
    // var countsInfo = cart.getCartTotalCounts(true);
    var cal = this._calcTotalAccountAndCounts(cartData);
    this.setData({
      selectedCounts : cal.selectedCounts,
      selectedTypeCounts : cal.selectedTypeCounts,
      account:cal.account,
      cartData : cartData,
      loadingHidden:true,
    })
  },

  /*离开页面时，更新本地缓存*/
  onHide:function(){
    cart.execSetStorageSync(this.data.cartData);
  },

  /*
  * 计算总金额和选择的商品总数
  * */
  _calcTotalAccountAndCounts:function(data){
    var len = data.length,
        // 总价，排除未选择商品
        account = 0,
        // 购买商品的总个数
        selectedCounts=0,
        // 购买商品种类的总数
        selectedTypeCounts=0;
    let multiple =100;
    for(let i = 0; i < len; i++ ){
      //浮点数计算会出现误差，避免 0.05 + 0.01 = 0.060 000 000 000 000 005 的问题，乘以 100 *100
      if(data[i].selectStatus) {
        account += data[i].counts * multiple *  Number(data[i].price)*multiple;
        selectedCounts+=data[i].counts;
        selectedTypeCounts++;
      }
    }
    return{
      selectedCounts:selectedCounts,
      selectedTypeCounts:selectedTypeCounts,
      account:account/(multiple*multiple)
    }
  },

  /* 选择商品 */
  toggleSelect(event){
    var id = cart.getDataSet(event,'id'),
        status = cart.getDataSet(event,'status'),
        index = this._getProductIndexById(id);

    this.data.cartData[index].selectStatus = !status;
    this._resetCartData();
  },

  /*全选*/
  toggleSelectAll:function(event){
    var allstatus=cart.getDataSet(event,'allstatus')=='true';
    var data=this.data.cartData,
        len=data.length;
    for(let i=0;i<len;i++) {
      data[i].selectStatus=!allstatus;
    }
    this._resetCartData();

    // wx.showModal({
    //   title: '提示',
    //   content: '是否要从购物车中删除选择商品？',
    //   //回调函数中不能直接使用this
    //   success: function (res) {
    //
    //   }
    // })
  },


  changeCounts:function(event){
    var id = cart.getDataSet(event,'id'),
        type = cart.getDataSet(event,'type'),
        index = this._getProductIndexById(id),
        count = 1

    if(type == 'add'){
      cart.addCounts(id)
    } else {
      count = -1;
      cart.cutCounts(id)
    }
    this.data.cartData[index].counts += count;
    this._resetCartData()
  },

  /*删除商品*/
  delete:function(event){
    var that = this;
    wx.showModal({
      title: '提示',
      content: '是否要从购物车中删除选择商品？',
      //回调函数中不能直接使用this
      success: function(res) {
        if (res.confirm) {
          // console.log('用户点击确定');
          var id=cart.getDataSet(event,'id'),
              index=that._getProductIndexById(id);
          that.data.cartData.splice(index,1);//删除某一项商品

          that._resetCartData();
          //this.toggleSelectAll();

          cart.delete(id);  //内存中删除该商品

        } else if (res.cancel) {
          return false;
        }
      }
    })
  },


  /*根据商品id得到 商品所在下标*/
  _getProductIndexById:function(id){
      var data = this.data.cartData,
          len = data.length
    for(let i = 0 ;i < len;i++){
      if(data[i].id == id){
        return i;
      }
    }
  },

  /*更新购物车商品数据*/
  _resetCartData(){
    var newData = this._calcTotalAccountAndCounts(this.data.cartData);
    this.setData({
      account: newData.account,
      selectedCounts:newData.selectedCounts,
      selectedTypeCounts:newData.selectedTypeCounts,
      cartData:this.data.cartData
    })
  },


  onProductsItemTap(event){
    var id = cart.getDataSet(event,'id')
    wx.navigateTo({
      url: '../product/product?id='+id,
    })
  },

  /*提交订单*/
  submitOrder: function () {
    wx.navigateTo({
      url: '../order/order?account=' + this.data.account + '&from=cart'
    });
  },


})
