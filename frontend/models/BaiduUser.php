<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class BaiduUser extends ActiveRecord{
    
    public static function tableName(){
        return 'baidu_user';
    }
    
    
    public static function getBduss($id){
        $baidu_user = self::findOne($id);
        return $baidu_user['bduss'];
    }
}