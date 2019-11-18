<?php
    namespace console\models;

    use Yii;
    use yii\base\Model;
    use yii\db\ActiveRecord;
    use yii\db\Query;

    class Orders extends ActiveRecord
    {
        private $connection;

        // 订单号
        public $ordersn;
        public function init() {
            $this->connection = Yii::$app->db;
        }

        public static function tableName() {
            return '{{%shopping_order}}';
        }

        /*
         * 检查未支付订单
         * @param ordersn 订单号
         * @param ckecktime 检查时间
         * */
        public function checkOrder($orderid, $checktime = 1800) {
            $upodatedt = time() - $checktime;

            $query = (new Query())->select('*')->from(self::tableName())->where(['status' => -1, 'orderid' => $orderid])->andwhere(['createtime<=' => $upodatedt])->all();
            return $query;
        }

        /*
         * 更新未支付订单状态
         *
         * */
        public function updateOrderById($data, $orderid){
            return $this->connection->createCommand()
                ->update(self::tableName(), $data, 'orderid=:orderid OR parent_orderid=:orderid', [':orderid'=>$orderid])
                ->execute();
        }
    }
