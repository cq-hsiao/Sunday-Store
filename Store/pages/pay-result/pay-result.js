
Page({
        data: {

        },
        onLoad: function (options){
            this.setData({
                payResult:options.flag,
                id:options.id,
                from:options.from
            });
            if(this.data.id == 0) {
                this.showTips('支付提示','本产品仅用于演示，支付系统已屏蔽',false);//屏蔽支付，提示
                // wx.setStorageSync('cart',[]);
                return;
            }
        },
        viewOrder:function(){
            if(this.data.from=='my'){
                wx.redirectTo({
                    url: '../order/order?from=order&id=' + this.data.id
                });
            }else{
                //返回上一级
                wx.navigateBack({
                    delta: 1
                })
            }
        },

        /*
        * 提示窗口
        * params:
        * title - {string}标题
        * content - {string}内容
        * flag - {bool}是否跳转到 "我的页面"
        */
        showTips:function(title,content,flag){
            wx.showModal({
                title: title,
                content: content,
                showCancel:false,
                success: function(res) {
                    if(flag) {
                        wx.switchTab({
                            url: '/pages/my/my'
                        });
                    }
                }
            });
        }
    }
)
