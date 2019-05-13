
import {Base} from "../../utils/base";

class My extends Base {
    constructor() {
        super();
    }

    //获取用户信息
    getUserInfo(cb){
        var that = this;
        wx.login({
            success:function () {
                wx.getUserInfo({
                    success:function (res) {

                        //判断cb是不是函数类型,是就执行这个function
                        typeof cb == 'function' && cb(res.userInfo);

                        //将用户昵称 提交到服务器
                        if(!that.onPay) {
                            // that._updateUserInfo(res.userInfo);
                        }
                    },
                    fail:function(res){
                        console.log(res);
                        typeof cb == "function" && cb({
                            avatarUrl:'../../imgs/icon/user@default.png',
                            nickName:'零食小贩'
                        });
                    }
                })
            }
        });
    }
}

export {My}
