<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class TiebaLiked extends ActiveRecord
{
    
    public static function tableName()
    {
        return 'tieba_liked';
    }
}