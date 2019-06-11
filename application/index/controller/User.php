<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/6
 * Time: 14:40
 */
namespace app\index\controller;

use think\cache\driver\Redis;
use think\Db;

Class User extends Common{

    public function index(){

        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);

        $redis_name = 'miaosha';

        for($i=0;$i<100;$i++){
            $uid = rand(1000,9999);

            if($redis->lLen($redis_name) < 10){

                $redis->rPush($redis_name,$uid.'%'.microtime());
                echo '秒杀成功';
            }else{
                echo "秒杀已结束";
            }
        }
        $redis->close();
    }
    public function add(){
        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);
        $redis_name = 'miaosha';

        while(1){

            $user = $redis->lPop($redis_name);

            if(!$user || $user == ''){
                continue;
            }

            $arr = explode('%',$user);
            $res = array(
                'uid' => $arr[0],
                'time' => $arr[1]
            );

            $data = Db::name('order')->insert($res);

            if(!$data){
                $redis->rPush($redis_name,$user);
            }

        }
        $redis->close();
    }

    public function show(){

        $redis = new \Redis();
        $redis->connect('127.0.0.1',6379);

        $aa = $redis->lLen('miaosha');
        dump($aa);
    }

}