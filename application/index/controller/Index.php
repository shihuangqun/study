<?php
namespace app\index\controller;

class Index
{
    public function index()
    {
        phpinfo();
//        $server = new swoole_websocket_server("0.0.0.0", 9502);
//
//        $server->on('open',function(){
//            echo '连接上了';
//        });
//
//        $server->on('message',function(){
//            echo '收到消息了';
//        });
//
//        $server->on('close',function(){
//            echo '断开连接';
//        });
//
//        $server->start();
    }


}
