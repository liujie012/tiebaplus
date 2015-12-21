<?php
namespace frontend\models;
use yii\db\ActiveRecord;
use linslin\yii2\curl;
class Tieba extends ActiveRecord{
    
    
    /**
     * 获取贴吧fid
     * 
     */
    public static function getTiebaFid($name){
        
        $tieba = self::find()->where(['name'=>$name])->asArray()->one();
        if($tieba) return $tieba['id'];
        
        $curl = new curl\Curl();        
        $response = $curl->setOption(CURLOPT_USERAGENT,'User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded')->post('http://tieba.baidu.com/mo/m?kw='.urlencode($name));
        
        if($curl->responseCode == 200){            
             $x  = preg_match('<input type="hidden" name="fid" value="(\d+)"/>',$response,$matches);
             if(isset($matches[1]) && $matches[1]>0){
                 $tieba = new self();
                 $tieba->fid = intval($matches[1]);
                 $tieba->name = $name;
                 $tieba->save();
                 return $tieba->id;
             }else{
                 return false;
             }
        }else{
            return false;
        }       
    }
    
    
    /**
     * 得到TBS
     */    
    public static function getTbs($bduss){
        $curl = new curl\Curl();
        $cookie = "BDUSS=".$bduss;
        $curl->setOption(CURLOPT_COOKIE, $bduss);
        $http_header = array('X-FORWARDED-FOR:8.8.8.8,125.8.8.7,125.8.8.6,125.8.8.5', 'CLIENT-IP:103.235.46.'.mt_rand(1,255),'X_REAL_IP:103.235.46.'.mt_rand(1,255),'X_FORWARDED_FOR:103.235.46.'.mt_rand(1,255),'REMOTE_ADDR:103.235.46.'.mt_rand(1,255));
        $curl->setOption(CURLOPT_HTTPHEADER,$http_header);        
        $response = $curl->post('http://tieba.baidu.com/dc/common/tbs');
        return json_decode($response,true)['tbs'];             
    }
    
    /**
     * 得到用户关注贴吧列表
     */
    public static function getTiebaList($id){
        $curl = new curl\Curl();
        $baidu_user = BaiduUser::findOne($id)->toArray();
        $bduss = $baidu_user['bduss'];
        $curl->setOption(CURLOPT_COOKIE, 'BDUSS='.$bduss);
        $page     = 1;
        
        while(true) {
            $list   = array();
            $url = 'http://tieba.baidu.com/f/like/mylike?&pn='.$page;
            $page++;
            $addnum = 0;
            $response     = $curl->get($url);
            preg_match_all('/\<td\>(.*?)\<a href=\"\/f\?kw=(.*?)\" title=\"(.*?)\">(.*?)\<\/a\>\<\/td\>/', $response, $list);
            foreach ($list[3] as $v) {
                $tieba_name = addslashes(htmlspecialchars(mb_convert_encoding($v, "UTF-8", "GBK")));
                $tieba_id = self::getTiebaFid($tieba_name);
                if($tieba_id){
                    $tieba_liked = TiebaLiked::find()->where(['tieba_id'=>$tieba_id,'buid'=>$id])->asArray()->one();
                    if(!$tieba_liked){
                        $tieba_liked = new TiebaLiked();
                        $tieba_liked->buid = $id;
                        $tieba_liked->tieba_id = $tieba_id;
                        $tieba_liked->save();
                    }
                }
               //插库
            }
            if($page > 100){
                break; //100页后重复
            }
            if (!isset($list[3][0])) {
                break; //完成
            }
        }
    }
    
    
    
    public static function signIn(){
        
    }
    
    
    
    
    
    
}