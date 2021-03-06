<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class ShoppingOrderGoods extends \yii\db\ActiveRecord {

    public static function tableName() {
        return '{{%shopping_order_goods}}';
    }

    public function getByOrderId($order_id) {
        $connection = Yii::$app->db;

        //查询子订单
        $sub_order_sql = "select id from ims_shopping_order where parent_ordersn = (
                              select ordersn from ims_shopping_order where id = {$order_id} 
                          )";

        $command = $connection->createCommand($sub_order_sql);
        $sub_order = $command->queryAll();

        //有无子订单，货品的查询方式会不同。
        $sql = "select g.title,g.brand,g.thumb, g.marketprice, w.name, g.comm1, og.*  from ims_shopping_order_goods as og 
                LEFT JOIN ims_shopping_goods as g on (og.goodsid= g.id) 
                LEFT JOIN ims_shopping_warehouse as w on (w.id = g.wid)
                where og.orderid in ( " . (empty($sub_order) ? $order_id : $sub_order_sql) . " )";

        $command = $connection->createCommand($sql);
        $result = $command->queryAll();

        return $result;
    }
}
