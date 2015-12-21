<?php
namespace frontend\models;
use yii\db\ActiveRecord;

class TiebaLiked extends ActiveRecord{
    
    public static function model($className = 'tieba_liked'){
        return new $className;
    }
}