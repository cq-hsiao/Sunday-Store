
import {Base} from "../../utils/base.js"

class Theme extends Base{

  constructor(){
    super()
  }

  getProductsData(id,callback){
    var params = {
      'url' : 'theme/' + id,
      sCallback : function(data){
        callback && callback(data);
      }
    };
    this.request(params)
  }
}

export {Theme}