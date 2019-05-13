import {Base} from '../../utils/base.js'

class Cart extends Base {

  constructor(){
    super();
    this._storageKeyName = 'cart';
  }


  /*
  * 加入到购物车
  * 如果之前没有样的商品，则直接添加一条新的记录， 数量为 counts
  * 如果有，则只将相应数量 + counts
  * @params:
  * item - {obj} 商品对象,
  * counts - {int} 商品数目,
  * */
  add(item,counts){
      var cartData = this.getCartDataFromLocal();

      var isHasInfo = this._isHasThatOne(item.id,cartData)
      if(isHasInfo.index == -1){
        item.counts = counts
        item.selectStatus = true //默认在购物车为选中状态
        cartData.push(item)
      } else {
        cartData[isHasInfo.index].counts += counts;
      }
      this.execSetStorageSync(cartData);  //更新本地缓存
      return cartData;
  }

    /*
    * 从缓存获取购物车数据
    * param
    * flag - {bool} 是否过滤掉不下单的商品
    */
  getCartDataFromLocal(flag){
    var res = wx.getStorageSync(this._storageKeyName)
    if(!res){
      res = []
    }
    //在下单的时候过滤掉未选择的商品
    if(flag){
      var newRes = [];
      for(let i = 0; i < res.length; i++){
        if(res[i].selectStatus){
          newRes.push(res[i]);
        }
      }
      res=newRes;
    }
    return res;
  }

  /*判断某商品是否已存在，返回序号和数据*/
  _isHasThatOne(id,arr){
    var item,
      result = {index:-1};

    for(let i=0;i<arr.length;i++){
      item = arr[i]
      if(item.id == id){
        result = {
          index:i,
          data:item
        }
        break
      }
    }
    return result;
  }


  /*
  * 获得购物车商品总数目,包括分类和不分类
  * param:
  * flag - {bool} 是否区分选中和不选中
  * return
  * counts1 - {int} 不分类
  * counts2 -{int} 分类
  */
  getCartTotalCounts(flag){
    var data =this.getCartDataFromLocal(),
        counts1 = 0,
        counts2 = 0
    for(let i = 0;i<data.length;i++){
      if(flag){
          if(data[i].selectStatus){
            counts1 += data[i].counts;
            counts2++;
          }
      }else{
        counts1 += data[i].counts;
        counts2++;
      }
    }
    return {
      counts1:counts1,
      counts2:counts2
    };
  }


  /*
  * 增加商品数目
  * */
  addCounts(id){
    this._changeCounts(id,1);
  };

  /*
  * 减商品数目
  * */
  cutCounts(id){
    this._changeCounts(id,-1);
  };


  /*
  * 修改商品数目
  * params:
  * id - {int} 商品id
  * counts -{int} 数目
  * */
  _changeCounts(id,counts){
    var cartData = this.getCartDataFromLocal(),
        hasInfo = this._isHasThatOne(id,cartData);
    if(hasInfo.index != -1){
      if(hasInfo.data.counts + counts > 0){
        cartData[hasInfo.index].counts+=counts;
      }
    }
    this.execSetStorageSync(cartData);  //更新本地缓存
  };

  /*
     * 删除某些商品
     */
  delete(ids){
    //单个id转成数组
    if(!(ids instanceof Array)){
      ids = [ids];
    }
    var cartData = this.getCartDataFromLocal()
    for(let i=0;i<ids.length;i++){
      var hasInfo = this._isHasThatOne(ids[i],cartData)
      if(hasInfo.index != -1){
        cartData.splice(hasInfo.index,1);  //删除数组某一项
      }
    }
    this.execSetStorageSync(cartData)
  }


  /*本地缓存 保存／更新*/
  execSetStorageSync(data){
    wx.setStorageSync(this._storageKeyName, data);
  }


}

export {Cart}
