<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

class BrokerOrder extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%brokers_order}}';
    }
}

//end file