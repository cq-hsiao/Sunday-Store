
//构建请求基类

import {Config} from "./config.js";

class Base{

  constructor(){
    //静态属性调用不用实例化
    this.baseRequestUrl = Config.restUrl;
  }

  request(params){

    var url = this.baseRequestUrl + params.url;
    if (!params.type) {
      params.type = 'GET';
    }
    wx.request({
      url: url,
      data: params.data,
      header: {
        'content-type':'application/json',
        'token':wx.getStorageSync('token')
      },
      method: params.type,
      // dataType: 'json',
      // responseType: 'text',
      success: function (res) {
        // if(params.sCallback){
        //   params.sCallback(res);
        // }
        params.sCallback&&params.sCallback(res.data)
      },
      fail: function (err) {
        console.log(err)
      }
      // complete: function (res) { },
    })


  }

  /* 获得元素上的绑定的值*/
  getDataSet(event, key) {
    return event.currentTarget.dataset[key];
  };
}


export {Base};
