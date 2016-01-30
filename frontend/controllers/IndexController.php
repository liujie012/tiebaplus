<?php
namespace  frontend\controllers;
use Yii;
use yii\web\Controller;
use frontend\models\User;
use frontend\models\Tieba;
use frontend\models\BaiduUser;

class IndexController extends Controller
{

    public function actionIndex()
    {
         
       /* $requestUrl = 'http://www.lianyingdai.com/user/SinaUser/client_ip';
         */
          
       //$fid = Tieba::getTiebaFid('北京');
       //Tieba::getTiebaList(1);
       Tieba::signInAll(1);
       //print_r($fid);
       //$bduss = "BDUSS=EJWUlZsVzlCRXZ-M2lZNzY1MEF1dWcwfn5TeWdjYUl2bGNCNHhjSXk4czUtcHhXQVFBQUFBJCQAAAAAAAAAAAEAAACDewBQSkrP8ba50b8AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADltdVY5bXVWd;";
       //echo Tieba::getTbs($bduss);
       
    }
    
    public function actionPost(){
        Tieba::post(1,1,'测试','测试');
    }
    
    public function actionLogin(){
        $bduus = BaiduUser::getBduss(1);
        echo Tieba::getTbs($bduus);
    }
}