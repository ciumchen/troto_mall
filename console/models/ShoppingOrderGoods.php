<?php
    namespace console\models;

    use Yii;
    use yii\base\Model;
    use yii\db\ActiveRecord;
    use yii\db\Query;

    class ShoppingOrderGoods extends ActiveRecord {
        private $connection;

        public function init(){
            $this->connection = Yii::$app->db;
        }

        public static function tableName()
        {
            return '{{%shopping_order_goods}}';
        }

        public static function ShoppingOrder()
        {
            return '{{%shopping_order}}';
        }

        /**
         * 查询订单商品信息
         * @param  array $data    更新字段
         * @param  array $orderid 主键ID
         * @return array
         */
        public function ckeckOrderGoodsById($orderid, $checktime = 1800) {
            $margintime = time() - $checktime;
            $query = (new Query())->select('og.orderid,og.goodsid,og.total')->from('shopping_order_goods as og')->leftJoin('shopping_order as o', 'og.orderid=o.id')->where(['o.createtime<=' => $margintime,'og.orderid' => $orderid])->all();
            return $query;
        }
    }