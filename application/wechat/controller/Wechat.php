<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/19
 * Time: 15:21
 */
namespace app\wechat\controller;

use think\Controller;
Class Wechat extends Controller{


    public function index(){

        $timestamp = input('timestamp');
        $nonce = input('nonce');
        $token = 'future';
        $signature = input('signature');

        $array = array($timestamp,$nonce,$token);
        sort($array,SORT_STRING);
        $tmpStr = sha1(implode('',$array));

        if($tmpStr == $signature && input('echostr')){
            echo input('echostr');
            die;
        }else{
            $this->reponseMsg();
        }
    }

    //接收事件推送并回复
    public function reponseMsg(){

        $postArr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");

//        $postArr = file_get_contents("php://input");
        $postObj = simplexml_load_string($postArr);

        if(strtolower($postObj->MsgType) == 'event'){
            if(strtolower($postObj->Event == 'subscribe')){
                $toUser = $postObj->FromUserName;
                $formUser = $postObj->ToUserName;
                $time = time();
                $msgType = 'text';
                $content = '欢迎关注闻说者';
                $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                             </xml>";
                $info = sprintf($template,$toUser,$formUser,$time,$msgType,$content);
                echo $info;
            }
        }

        if(strtolower($postObj->MsgType == 'text')){
            $toUser = $postObj->FromUserName;
            $formUser = $postObj->ToUserName;
            $time = time();
            $msgType = 'text';
            $template = "<xml>
                                <ToUserName><![CDATA[%s]]></ToUserName>
                                <FromUserName><![CDATA[%s]]></FromUserName>
                                <CreateTime>%s</CreateTime>
                                <MsgType><![CDATA[%s]]></MsgType>
                                <Content><![CDATA[%s]]></Content>
                             </xml>";



            switch(trim($postObj->Content)){
                case 1:
                    $content = "输入为11111";
                    break;
                case 2:
                    $content = "输入为22222";
                    break;
                case 3:
                    $content = "输入为33333";
                    break;
                case '百度':
                    $content = "<a href='www.baidu.com'>百度</a>";
                    break;
                case '图文':
                    $msgType = 'news';
                    $arr = array(
                        array(
                          'title' => '测试标题',
                            'description' => '描述内容',
                            'picurl' => 'http://s.justwaityou1314.com/timg.gif',
                            'url' => 'www.baidu.com'
                        )
                    );
                    $template = "<xml>
                                  <ToUserName><![CDATA[%s]]></ToUserName>
                                  <FromUserName><![CDATA[%s]]></FromUserName>
                                  <CreateTime>%s</CreateTime>
                                  <MsgType><![CDATA[%s]]></MsgType>
                                  <ArticleCount>".count($arr)."</ArticleCount>
                                  <Articles>";
                   foreach($arr as $k => $v){
                       $template .= "<item>
                                      <Title><![CDATA[".$v['title']."]]></Title>
                                      <Description><![CDATA[".$v['description']."]]></Description>
                                      <PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
                                      <Url><![CDATA[".$v['url']."]]></Url>
                                    </item>";
                   }

                    $template .= "</Articles>
                                </xml>";
                   break;
                case '天气':
                    header('Content-type:text/html;charset=utf-8');
                    //配置您申请的appkey
                    $appkey = "af54284f87efbc41672b10d5df4c69dc";

                    //************1.根据城市查询天气************
                    $url = "http://op.juhe.cn/onebox/weather/query";
                    $params = array(
                        "cityname" => "深圳",//要查询的城市，如：温州、上海、北京
                        "key" => $appkey,//应用APPKEY(应用详细页查询)
                        "dtype" => "json",//返回数据的格式,xml或json，默认json
                    );
                    $paramstring = http_build_query($params);
                    $content = $this->juhecurl($url,$paramstring);
                    $result = json_decode($content,true);
                    if($result){
                        if($result['error_code']=='0'){
//                            echo "<pre>";
                            $content = $result['result']['data']['realtime']['city_name'].'----'.$result['result']['data']['realtime']['date'].'----'.$result['result']['data']['realtime']['weather']['info'];
//                            echo "</pre>";
                        }else{
                            echo $result['error_code'].":".$result['reason'];
                        }
                    }else{
                        echo "请求失败";
                    }
                    break;
                default:
                    $content = '该指令未被收录';
                    break;
            }

            $info = sprintf($template,$toUser,$formUser,$time,$msgType,$content);
            echo $info;
        }

    }

    public function http_curl(){
        $ch = curl_init();
        $url = "www.baidu.com";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $output = curl_exec($ch);

        var_dump($output);
    }

    public function getAccessToken(){

        $appid = 'wx7f90d2f4d6fefd1c';
        $secret = 'a7e2ba33c708521b9af8d6575a6dfd51';
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $res = curl_exec($ch);


        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }

        $res = json_encode($res);
        var_dump($res);
        curl_close($ch);

    }

    public function getServerIp(){
        $accesstoken = '22_bIDrTOHKtbTCmuP2cwNoYzuEOXG--Z3kLlHSAc9Vo9nnhcLiFGf7e0o5dGHRlox8zpM4C0egIn__9YIw8-4vmhDjvKfPm4QzKniwDGRHsKOcxFYoeKjni6dbC34yefqEn9StUE1Hze1iJAxaRIMjAFAEVZ';
        $url = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.$accesstoken;

        $ch = curl_init();

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        $res = curl_exec($ch);
        if(curl_errno($ch)){
            var_dump(curl_error($ch));
        }

        echo "<pre>";
        var_dump(json_decode($res,true));
        echo "</pre>";
        curl_close($ch);
    }

    public function juhecurl($url,$params=false,$ispost=0){
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
        curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
        curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if( $ispost )
        {
            curl_setopt( $ch , CURLOPT_POST , true );
            curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
            curl_setopt( $ch , CURLOPT_URL , $url );
        }
        else
        {
            if($params){
                curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
            }else{
                curl_setopt( $ch , CURLOPT_URL , $url);
            }
        }
        $response = curl_exec( $ch );
        if ($response === FALSE) {
//echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
        $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
        curl_close( $ch );
        return $response;
    }
}