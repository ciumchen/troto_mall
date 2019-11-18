<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;
use yii\db\ActiveRecord;

class ShoppingOrderLogistic extends ActiveRecord {
    private $connection;

    public function init(){
        $this->connection = Yii::$app->db;
    }

    public static function tableName() {
        return '{{%shopping_order_logistic}}';
    }

}