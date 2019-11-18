<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

use frontend\models\Users;
use frontend\models\ShoppingOrderGoods;
use frontend\models\ShoppingGoods;
use frontend\models\Coupon;
use frontend\models\BrokerOrder;
use frontend\models\BrokerOrderReward;


class ShoppingOrder extends \yii\db\ActiveRecord
{
    private $connection;

    public static $orderStatus = [
        '-1' => '取消订单',
        '-2' => '系统取消',
        '0' => '未支付',
        '1' => '已付款',
        '2' => '已发货',
        '3' => '已收货',
        '4' => '已评价',
        '5' => '结束'
    ];

    public function init(){
        $this->connection = Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%shopping_order}}';
    }

    public function order_goodstable()
    {
        return '{{%shopping_order_goods}}';
    }

    public function goodstable()
    {
        return '{{%shopping_goods}}';
    }

    public static function generateOrderSn() {
        return date('ymdHi').rand(100000,999999);
    }

    public static function getStatusName($status) {
        return ShoppingOrder::$orderStatus[$status];
    }

    /**
     * 更新订单状态
     * @param  array $data    更新字段
     * @param  array $ordersn 更新条件
     * @return array
     */
    public function updateOrders($data, $ordersn) {
        return $this->connection->createCommand()
                    ->update(self::tableName(), $data, 'ordersn=:ordersn OR parent_ordersn=:ordersn', [':ordersn'=>$ordersn])
                    ->execute();
    }

    /**
     * 根据用户id获取订单
     */
    public function getOrderGoodsByUid($uid, $status='') {
        $whereCondition = ['o.uid' => $uid];
        if ($status!=='') {
            $whereCondition['o.status'] = $status;
        }

        return (new Query())
            ->select('g.title,g.marketprice,g.deduct,g.originalprice,g.id AS goodsid,g.thumb, og.*, o.source')
            ->from("{$this->order_goodstable()} as og")
            ->leftJoin("{$this->tableName()} as o", 'o.id=og.orderid')
            ->leftJoin("{$this->goodstable()} as g", 'g.id=og.goodsid')
            ->where($whereCondition)
            ->orderBy('o.createtime DESC')
            ->All();
    }

    public function getGoodsByOrder($orderid) {
        return (new Query())
            ->select('g.*,s.title,s.thumb,s.thumb1,s.marketprice,s.price,s.deduct, s.brand, s.pcate')
            ->from("{$this->order_goodstable()} as g")
            ->leftJoin("{$this->goodstable()} as s", 'g.goodsid = s.id')
            ->where(['g.orderid' => $orderid])
            ->All();
    }

    public function getOrderByUid($uid, $status = '')
    {
        $andWhereStatus = ($status == '' ? [] : ['status' => $status]);

        $orders = (new Query())
                    ->select('*')
                    ->from($this->tableName())
                    ->where(['uid'=>$uid, 'deleted'=>0, 'hassub_order'=>0]);
        if ($status!='') {
            $orders = $orders->andWhere($andWhereStatus);
        }
        $orders = $orders->orderBy('createtime DESC')->All();

        foreach ($orders as &$order) {
            $order['goods'] = $this->getGoodsByOrder($order['id']);
        }

        return $orders;
    }


    //用户是否有首个已支付的订单
    public function isFirstOrder($uid)
    {
        return false;
        $order = (new Query())
            ->select('id')
            ->from($this->tableName())
            ->where(['uid' => $uid])
            ->andWhere(['>', 'status', 0])
            ->one();

        if ($order === false) {
            return true;
        } else {
            return false;
        }
    }


    public function paid($order_id) {
        /*********************************************
        //减库存
        $orderGoodsModel = new ShoppingOrderGoods();
        $products = $orderGoodsModel->getByOrderId($order_id);

        foreach ($products as $p) {
            $goods = ShoppingGoods::find()->where(['id' => $p['goodsid']])->one();
            $goods->total -= $p['total'];
            $goods->save();
        }
        *************************************************/
        //更新订单状态
        $order = ShoppingOrder::find()->where(['id' => $order_id])->one();
        if (isset($order->status) && $order->status==0) {
            $ShoppingOrderModel = new ShoppingOrder();
            $ShoppingOrderModel->updateOrders(['status'=>1], $order->ordersn);

            //订单如使用优惠券，更新优惠券状态
            $coupon = Coupon::find()->where(['order_id' => $order_id])->one();
            if ($coupon !== null) {
                $coupon->status = 3;
                $coupon->save();
            }

            //更新客户总消费金额
            $Users = new Users();
            $Users->addCredits2($order->uid, $order->price);

            //生成分销提成
            $this->orderBrokerage($order);
        }
    }

    private function orderBrokerage($order) {
        //计算商品总提成
        $orderGoodsModel = new ShoppingOrderGoods();
        $products = $orderGoodsModel->getByOrderId($order->id);
        $reward_total = 0;

        foreach ($products as $p) {
            $reward_total += ($p['total'] * $p['comm1']);
        }

        //生成分成订单
        $broker_order = new BrokerOrder();
        $broker_order->order_id = $order->id;
        $broker_order->status = 0;
        $broker_order->reward_total = $reward_total;
        $broker_order->save();

        //计算分销分成
        $Broker = new Broker();
        $relation_list = $Broker->getRelationList($order->uid);
        $bill = $Broker->calReward($relation_list, $reward_total);

        foreach ($bill as $b) {
            $new_bill = new BrokerOrderReward();
            $new_bill->orderid = $order->id;
            $new_bill->reward_amount = $b['reward'];
            $new_bill->brokerid = $b['uid'];
            $new_bill->status = 0;
            $new_bill->save();
        }

        //更新所有下线的总消费额和总分成额
        foreach ($bill as $b) {
            $user = Users::find()->where(['uid' => $b['uid']])->one();
            //下单帐户自己不计算
            if ($b['uid'] != $order->uid) {
                $user->credits7 += $order->price;  //下线总消费额
            }

            //有消费则分派提成
            if ($b['reward'] > 0) {
                $user->credits5 += $b['reward'];   //总分成额
            } else {
                //代理商，不用激活，始终分派提成。
                if ($user->brokerid === null) {
                    $user->credits5 += $b['reward'];   //总分成额
                }
            }

            $user->save();
        }
    }

    /**
     * 减库存操作
     * @param intval $orderid 订单ID，如果是子订单将不处理其他订单
     * @param string $type    减类型，ORDER-下单减，PAYOFF-支付减
     * @return bool
     */
    public function reduceGoodsTotal($orderid, $type='PAYOFF') {
        //异常参数直接返回错误
        if ($type!='PAYOFF' && $type!='ORDER') {
            return false;
        }
        //查询订单内商品
        $orderGoods = (new Query())
                            ->select('goodsid,total')
                            ->from($this->order_goodstable())
                            ->where(['orderid'=>$orderid])
                            ->all();
        $orderGoodsList = [];
        foreach ($orderGoods as $orderGoodsOne) {
            $orderGoodsList[$orderGoodsOne['goodsid']] = $orderGoodsOne;
            $orderGoodsidList[] = $orderGoodsOne['goodsid'];
        }
        $orderGoodsidStr = implode(',', array_values($orderGoodsidList));

        //根据totalcnf字段判断是直接减库存还是支付减库存
        $goodsList = (new Query())
                            ->select('id, totalcnf, total')
                            ->from($this->goodstable())
                            ->where(['in', 'id', array_values($orderGoodsidList)])
                            ->all();
        $goodsConfList = [];
        foreach ($goodsList as $goodsOne) {
            $goodsConfList[$goodsOne['id']] = $goodsOne;
        }

        //根据减库存依据的类型处理
        if($type=='ORDER') {
            foreach ($orderGoodsList as $orderGoodsKey=>$orderGoodsValue) {
                if ($goodsConfList[$orderGoodsValue['goodsid']]['totalcnf']=='0') {
                    $sql = 'update '.$this->goodstable().' set total=total-'.$orderGoodsValue['total'].' where id='.$orderGoodsValue['goodsid'];
                    $this->connection->createCommand($sql)->execute();
                }
            }
        } else if ($type=='PAYOFF') {
            foreach ($orderGoodsList as $orderGoodsKey=>$orderGoodsValue) {
                if ($goodsConfList[$orderGoodsValue['goodsid']]['totalcnf']=='1') {
                    $sql = 'update '.$this->goodstable().' set total=total-'.$orderGoodsValue['total'].' where id='.$orderGoodsValue['goodsid'];
                    $this->connection->createCommand($sql)->execute();
                }
            }
        }
        return true;
    }

}