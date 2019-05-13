
import {Token} from "./utils/token";

App({
    /**
     * 监听小程序初始化。
     * 当小程序初始化完成时，会触发 onLaunch（全局只触发一次）
     * 生命周期函数
     */
    onLaunch(){
        var token = new Token();
        token.verify();
    }
})
