<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/18
 * Time: 11:27
 */
        $server = new swoole_websocket_server("0.0.0.0", 9502);

        $server->on('open',function(){
            echo '连接上了';
        });

        $server->on('message',function($server,$frame){
            echo "收到消息了:{$frame->data}";

            foreach($server->connections as $fd){
                $server->push($fd,$frame->data);
            }
//            echo '当前服务器有'.count($server->connections).'连接';
        });

        $server->on('close',function(){
            echo '断开连接';
        });

        $server->start();