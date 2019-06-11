<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/6
 * Time: 14:40
 */
namespace app\index\controller;

use think\App;
use think\Controller;
use think\Db;

Class Common extends Controller{

    public function initialize(){

    }
    public function return_msg($code,$msg = '',$data = ''){

        $res['code'] = $code;
        $res['msg'] = $msg;
        $res['data'] = $data;

        echo json_encode($res);
        die;
    }
}