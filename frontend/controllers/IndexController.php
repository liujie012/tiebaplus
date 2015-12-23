<?php
namespace  frontend\controllers;
use Yii;
use yii\web\Controller;
use frontend\models\User;
use frontend\models\Tieba;

class IndexController extends Controller
{

    public function actionIndex()
    {
         
       /* $requestUrl = 'http://www.lianyingdai.com/user/SinaUser/client_ip';
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_BASIC); //代理认证模式
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:8.8.8.8,125.8.8.7,125.8.8.6,125.8.8.5', 'CLIENT-IP:125.8.8.8','X_REAL_IP:125.8.8.9','X_FORWARDED_FOR:8.8.8.3','REMOTE_ADDR:8.8.8.2'));
        curl_setopt($ch, CURLOPT_PROXY, "182.92.66.41"); //代理服务器地址
        curl_setopt($ch, CURLOPT_PROXYPORT, 8080); //代理服务器端口
        //curl_setopt($ch, CURLOPT_PROXYUSERPWD, ":"); //http代理认证帐号，username:password的格式
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        $file_contents = curl_exec($ch);
        curl_close($ch);
        echo $file_contents; */
          
       //$fid = Tieba::getTiebaFid('北京');
       //Tieba::getTiebaList(1);
       Tieba::signInAll(1);
       //print_r($fid);
       //$bduss = "BDUSS=EJWUlZsVzlCRXZ-M2lZNzY1MEF1dWcwfn5TeWdjYUl2bGNCNHhjSXk4czUtcHhXQVFBQUFBJCQAAAAAAAAAAAEAAACDewBQSkrP8ba50b8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADltdVY5bXVWd;";
       //echo Tieba::getTbs($bduss);
       
    }
    
}