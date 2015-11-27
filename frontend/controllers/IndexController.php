<?php
namespace  frontend\controllers;

use Yii;
use yii\web\Controller;

class IndexController extends Controller{


    public function actionIndex(){
       $requestUrl = 'http://www.baidu.com';
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
        curl_setopt($ch, CURLOPT_PROXY, "120.195.203.108"); //代理服务器地址
        curl_setopt($ch, CURLOPT_PROXYPORT, 80); //代理服务器端口
        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, ":"); //http代理认证帐号，username:password的格式
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        $file_contents = curl_exec($ch);
        curl_close($ch);
        echo $file_contents;

    }
}