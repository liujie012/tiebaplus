<?php
namespace frontend\models;

use yii\db\ActiveRecord;
use linslin\yii2\curl;

class Tieba extends ActiveRecord{
    
    
    /**
     * 获取贴吧fid
     * 
     */
    public static function getTiebaFid($name)
    {
        
        $tieba = self::find()->where(['name'=>$name])->asArray()->one();
        if($tieba) return $tieba['id'];
        
        $curl = new curl\Curl();        
        $response = $curl->setOption(CURLOPT_USERAGENT,'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded')->post('http://tieba.baidu.com/mo/m?kw='.urlencode($name));
        
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
    public static function getTbs($bduss)
    {
        $curl = new curl\Curl();
        $cookie = "BDUSS=".$bduss;
        $curl->setOption(CURLOPT_COOKIE, $cookie);
        $http_header = array('X-FORWARDED-FOR:8.8.8.8,125.8.8.7,125.8.8.6,125.8.8.5', 'CLIENT-IP:103.235.46.'.mt_rand(1,255),'X_REAL_IP:103.235.46.'.mt_rand(1,255),'X_FORWARDED_FOR:103.235.46.'.mt_rand(1,255),'REMOTE_ADDR:103.235.46.'.mt_rand(1,255));
        $curl->setOption(CURLOPT_HTTPHEADER,$http_header);        
        $response = $curl->post('http://tieba.baidu.com/dc/common/tbs');
        return json_decode($response,true)['tbs'];             
    }
    
    /**
     * 得到用户关注贴吧列表
     */
    public static function getTiebaList($id)
    {
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
                    $tieba_liked = TiebaLiked::find()->where(['tieba_id'=>$tieba_id,'buid'=>$id])->one();
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
    
    
    /**
     * 单个贴吧签到
     * @param int $buid
     * @param int $tieba_id
     */
    public static function signIn($buid, $tieba_id)
    {
        $curl = new curl\Curl();
        $bduus = BaiduUser::getBduss($buid);
        $curl->setOption(CURLOPT_COOKIE, "BDUSS=".$bduus);
        $curl->setOption(CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4');
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','sid: fa47438c22e378d6'));
        $url = 'http://c.tieba.baidu.com/c/c/forum/sign';
        
        //$tieba_liked = TiebaLiked::findOne($tieba_id)->toArray();
        $tieba = Tieba::findOne($tieba_id)->toArray();
        
        $post = array(
            'BDUSS' => trim($bduus),
            '_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
            '_client_type' => '4',
            '_client_version' => '1.2.1.17',
            '_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
            'fid' => $tieba['fid'],
            'kw' => $tieba['name'],
            'net_type' => '3',
            'tbs' => self::getTbs($bduus)
        );
        $str = '';
        foreach($post as $k=>$v) {
            $str .= $k.'='.$v;
        }
        $post['sign'] = strtoupper(md5($str.'tiebaclient!!!'));
        $curl->setOption(CURLOPT_POSTFIELDS, http_build_query($post));
        //print_r($curl->getOptions());exit;
        $response = $curl->post($url);
        $response = json_decode($response, true);
        if($response['error_code'] == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 所有贴吧签到
     * @param int $buid
     */
    public static function signInAll($buid)
    {
        $tieba_liked = TiebaLiked::find()->where(['buid'=>$buid])->asArray()->all();
        foreach ($tieba_liked as $value) {
            if(self::signIn($buid, $value['tieba_id'])) {
                echo 'ok';
            }else{
                echo 'wrong';
            }
        }
    }
    
    
    public static function post($buid, $tieba_id, $title, $content){
        $curl = new curl\Curl();
        $bduus = BaiduUser::getBduss($buid);
        $curl->setOption(CURLOPT_COOKIE, "BDUSS=".$bduus);
        $curl->setOption(CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_0 like Mac OS X) AppleWebKit/600.1.3 (KHTML, like Gecko) Version/8.0 Mobile/12A4345d Safari/600.1.4');
        $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        $url = 'http://c.tieba.baidu.com/c/c/thread/add';
        
        //$tieba_liked = TiebaLiked::findOne($tieba_id)->toArray();
        $tieba = Tieba::findOne($tieba_id)->toArray();
        
        $post = array(
            /* 'BDUSS' => trim($bduus),
            'title' => urlencode($title),
            'content' => urlencode($content),
            'subapp_type' => "tieba",
            '_client_id' => '03-00-DA-59-05-00-72-96-06-00-01-00-04-00-4C-43-01-00-34-F4-02-00-BC-25-09-00-4E-36',
            '_client_type' => '1',
            '_client_version' => '1.2.1.17',
            '_phone_imei' => '540b43b59d21b7a4824e1fd31b08e9a6',
            'fid' => $tieba['fid'],
            'kw' => urlencode($tieba['name']),
            'net_type' => '1', */
            "anonymous" => "0",
            "title" => "%E6%B5%8B%E8%AF%95",
            "m_cost" => "219.920993",
            "m_logid" => "1664654236",
            "subapp_type" => "tieba",
            "m_size_d" => "332",
            "_timestamp" => "1453818477597",
            "brand" => "iPhone",
            "_os_version" => "8.2",
            "_phone_newimei" => "26E17728D81B6B50E207E17D04F1FF4A",
            "_client_version" => "7.1.0",
            "BDUSS" => "lnbWJhVTlaWVJicENNaGpsYnlsU0JScW01aGpKWlZnWDE5T3RxUlRGQ3dBMnhXQVFBQUFBJCQAAAAAAAAAAAEAAAAvgXkwt8rQoc3IAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAALB2RFawdkRWZF",
            "fid" => "1363364",
            "is_location" => "1",
            "voice_md5" => "",
            "during_time" => "",
            "tbs" => "f367d4f402d486811447769827",
            "net_type" => "1",
            "cuid" => "26E17728D81B6B50E207E17D04F1FF4A",
            "kw" => "%E5%B9%BF%E5%B7%9Efc",
            "sign" => "EF5105CD2C62DB361DBDA3CE758A1565",
            "_client_type" => "1",
            "new_vcode" => "1",
            "is_ntitle" => "0",
            "from" => "appstore",
            "_client_id" => "wappc_1452144434786_865",
            "m_size_u" => "1199",
            "brand_type" => "iPhone%206%20Plus",
            "_phone_imei" => "26E17728D81B6B50E207E17D04F1FF4A",
            "vcode_tag" => "11",
            "m_api" => "c%2Fs%2Finpv",
            "content" => "%E6%B5%8B%E8%AF%95%E6%B5%8B%E8%AF%95"
            
        );
        $str = '';
        foreach($post as $k=>$v) {
            $str .= $k.'='.$v;
        }
        $post['sign'] = strtoupper(md5($str.'tiebaclient!!!'));
        $curl->setOption(CURLOPT_POSTFIELDS, http_build_query($post));
        //print_r($curl->getOptions());exit;
        $response = $curl->post($url);
        $response = json_decode($response, true);
        print_r($response);
        if($response['error_code'] == 0) {
            return true;
        } else {
            return false;
        }
    }
    
    
    
    
}