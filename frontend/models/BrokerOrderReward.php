<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;

class BrokerOrderReward extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%brokers_order_reward}}';
    }
}

//end file