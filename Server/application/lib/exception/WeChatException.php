<?php
/**
 * Created by PhpStorm.
 * User: Su
 * Date: 2018/12/28
 * Time: 16:39
 */

namespace app\lib\exception;


class WeChatException extends BaseException
{
    public $code = 404;
    public $msg = '微信服务器接口调用失败';
    public $errorCode = 999;
}
