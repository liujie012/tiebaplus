<?php
namespace frontend\models;
use Yii;
use yii\db\ActiveRecord;

class Tieba extends ActiveRecord{
    
    
    public static function getTiebaFid($name){
        $ch = new wcurl('http://tieba.baidu.com/mo/m?kw='.urlencode($kw), array('User-Agent: fuck phone','Referer: http://wapp.baidu.com/','Content-Type: application/x-www-form-urlencoded'));
        $s  = $ch->exec();
        //self::mSetFid($kw,$fid[1]);
        $x  = easy_match('<input type="hidden" name="fid" value="*"/>',$s);
        if (isset($x[1])) {
            return $x[1];
        } else {
            return false;
        }
    }
}