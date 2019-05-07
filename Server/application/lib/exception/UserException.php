<?php
/**
 * Created by PhpStorm.
 * User: Su
 * Date: 2019/1/3
 * Time: 17:59
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $message = '用户不存在';
    public $errorCode = 60000;
}
