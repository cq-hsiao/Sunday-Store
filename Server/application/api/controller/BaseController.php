<?php
/**
 * Created by PhpStorm.
 * User: AAA
 * Date: 2019/5/7
 * Time: 22:54
 */

namespace app\api\controller;


use app\api\service\Token;
use think\Controller;

class BaseController extends Controller
{
    protected function checkExclusiveScope()
    {
        Token::needExclusiveScope();
    }
    protected function checkPrimaryScope(){
        Token::needPrimaryScope();
    }
    protected function checkSuperScope()
    {
        Token::needSuperScope();
    }
}
