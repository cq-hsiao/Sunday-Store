
// 引用使用es6的module引入和定义
// 全局变量以g_开头
// 私有函数以_开头(仅个人习惯）

import {Config} from "./config";

class Token {
    constructor(){
        this.verifyUrl = Config.restUrl + 'token/verify';
        this.tokenUrl = Config.restUrl + 'token/user';
    }

    verify(){
        var token = wx.getStorageSync('token')
        if(!token){
            this.getTokenFromServer()
        } else {
            this._verifyFromServer(token)
        }
        console.log(token)
    }

    //从服务器获取token
    getTokenFromServer(callback){
        var that = this;
        wx.login({
            success(res) {
                console.log(res.code)
                wx.request({
                    url : that.tokenUrl,
                    method : "POST",
                    data:{
                        code:res.code
                    },
                    success(res) {
                        wx.setStorageSync('token',res.data.token)
                        callback && callback(res.data.token)
                    }
                })
            }
        })
    }

    //携带令牌去服务器校验令牌
    _verifyFromServer(token) {
        var that = this;
        wx.request({
            url:that.verifyUrl,
            method:'POST',
            data:{
                token: token
            },
            success(res) {
                var valid = res.data.isValid;
                if(!valid){
                    that.getTokenFromServer();
                }
            }
        })
    }
}


export {Token}
