
//构建请求基类

import {Config} from "./config.js";
import {Token} from "./token";

class Base{

  constructor(){
    //静态属性调用不用实例化
    this.baseRequestUrl = Config.restUrl;
    this.onPay=Config.onPay;
  }

  //http 请求类, 当noRefetch为true时，不做未授权重试机制
  request(params,noRefetch){
    var that = this;
    var url = this.baseRequestUrl + params.url;
    if (!params.type) {
      params.type = 'GET';
    }
    /*不需要再次组装地址*/
    if(params.setUpUrl==false){
      url = params.url;
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
          // statusCode 开发者服务器返回的 HTTP 状态码
          // 判断以2（2xx)开头的状态码为正确
          // 异常不要返回到回调中，就在request中处理，记录日志并showToast一个统一的错误即可
          var code = res.statusCode.toString();
          var startChar = code.charAt(0);
          if (startChar == '2') {
            params.sCallback && params.sCallback(res.data);
          } else {
            if (code == '401') {
              if (!noRefetch) {
                that._refetch(params);
              }
            }
            // else if(code == '500') {
            //   wx.navigateTo({
            //     url: '../pay-result/pay-result?flag=0&id=0' + '&from=order'
            //   });
            // }

            //避免重复发送错误信息
            if(noRefetch){
              params.eCallback && params.eCallback(res.data);
            }
          }
        },
        fail: function (err) {
          console.log(err)
          params.eCallback && params.eCallback(err.data)
        }
        // complete: function (res) { },
      })


  }

  /* 获得元素上的绑定的值*/
  getDataSet(event, key) {
    return event.currentTarget.dataset[key];
  };

  _refetch(params) {
    var token = new Token();
    token.getTokenFromServer((token) => {
      //**使用箭头函数，保持环境变量不改变，所以这里的回调函数里可用this
      this.request(params, true);
    });
  };

}


export {Base};
