<?php
/**
 * Created by PhpStorm.
 * User: Su
 * Date: 2018/12/12
 * Time: 18:03
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    public $code = 404;
    public $msg = '请求的Banner不存在';
    public $errorCode = 40000;
}