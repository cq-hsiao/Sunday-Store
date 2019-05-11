
import {Base} from "../../utils/base.js"

class Category extends Base {

  constructor(){
    super()
  }

  /*获得所有分类*/
  getCategoryData(callback){
    var params = {
      url:'category/all',
      sCallback:function(data){
        callback && callback(data)
      }
    }
    this.request(params)
  }

  /*获得指定分类下的商品*/
  getProductsByCategory(id,callback){
    var params = {
      url:'product/by_category?id='+id,
      sCallback: function (data) {
        callback && callback(data)
      }
    };
    console.log('请求了')
    this.request(params)
  }
}


export {Category}