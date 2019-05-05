<?php
/**
 * Created by PhpStorm.
 * User: Su
 * Date: 2018/12/26
 * Time: 15:09
 */

namespace app\lib\exception;



class ProductException extends BaseException
{
    public $code = 404;
    public $msg = '指定商品不存在，请检查商品ID';
    public $errorCode = 20000;
}
