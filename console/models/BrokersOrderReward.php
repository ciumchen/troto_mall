<?php
namespace console\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;

class BrokersOrderReward extends ActiveRecord {
    private $connection;

    public static function tableName()
    {
        return '{{%brokers_order_reward}}';
    }
}