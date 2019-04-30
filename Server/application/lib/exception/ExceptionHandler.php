<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/4/29
 * Time: 16:03
 */

namespace app\lib\exception;



use Exception;
use think\exception\Handle;
use think\facade\Log;
use think\facade\Request;


/*
 * 重写Handle的render方法，实现自定义异常消息
 */

class ExceptionHandler extends Handle
{
    private $code;
    private $msg;
    private $errorCode;

    public function render(Exception $e)
    {
        if($e instanceof BaseException)
        {
            //如果是自定义异常，则控制http状态码，不需要记录日志
            //因为这些通常是因为客户端传递参数错误或者是用户请求造成的异常
            //不应当记录日志

            $this->code = $e->code;
            $this->msg = $e->msg;
            $this->errorCode = $e->errorCode;

        } else {

            // 如果是服务器未处理的异常，将http状态码设置为500，并记录日志
            // Config::get('app_debug');
            if(config('app_debug')){
                // 调试状态下需要显示TP默认的异常页面，因为TP的默认页面容易看出问题
                Log::write($e->getMessage(),'error');
                return parent::render($e);
            }

            // 生产环境下只记录error类型的异常信息
            $this->code = 500;
            $this->msg = 'Sorry,we make a mistake. ):';
            $this->errorCode = 999;
            $this->recordErrorLog($e);
        }

        $result = [
            'msg' => $this->msg,
            'error_code' => $this->errorCode,
            'request_url' => Request::url()
        ];

        return json($result,$this->code);
    }

    /*
    * 将异常写入日志
    */
    private function recordErrorLog(Exception $e)
    {

        Log::init([
            'type'  =>  'File',
            'path'  => __DIR__.'/../../../logs/',
//            'path'  => dirname(dirname(realpath($_SERVER['SCRIPT_FILENAME']))).'/logs',
            'level' => ['error'],
        ]);

//        Log::record($e->getMessage(),'error');

//      V5.1.8+版本开始，支持配置close参数关闭全局日志写入（但不影响write方法写入日志）。
        Log::write($e->getMessage(),'error');
    }

}
