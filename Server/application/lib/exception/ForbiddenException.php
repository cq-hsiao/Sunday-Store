<?php


namespace app\lib\exception;

/**
 * token验证失败时抛出此异常
 */
class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '抱歉，您没有此权限～';
    public $errorCode = 10001;
}
