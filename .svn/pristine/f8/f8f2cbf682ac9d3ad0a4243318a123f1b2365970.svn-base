<?php
namespace console\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;

class ShoppingOrder extends ActiveRecord {
    private $connection;

    public function init(){
        $this->connection = Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%shopping_order}}';
    }


    /**
     * 更新订单信息
     * @param  array $data    更新字段
     * @param  array $orderid 主键ID
     * @return array
     */
    public function updateOrderById($data, $orderid) {
        return $this->connection->createCommand()
                    ->update(self::tableName(), $data, 'id=:orderid', [':orderid'=>$orderid])
                    ->execute();
    }

}