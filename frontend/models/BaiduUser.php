<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class BaiduUser extends ActiveRecord{
    
    public static function model($className = 'baidu_user'){
         return new $className;
    }
}