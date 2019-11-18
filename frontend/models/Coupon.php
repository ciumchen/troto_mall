<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class Coupon extends ActiveRecord
{

    private $connection;

    public function init() {
        $this->connection = Yii::$app->db;
    }

    public static function tableName()
    {
        return '{{%coupon}}';
    }
    public static function tableCouponType()
    {
        return '{{%coupon_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [

        ];
    }

    /**
     * 生成优惠券码
     * @prama intval $bits 券码位数
     * @return string
     */
    public static function generateSN($bits=12) {
        $baseChars = '12356789QWERTYUIOPASDFGHJKLZXCVBNM';
        $sn = '';
        for ($no=0; $no<$bits; $no++) { 
            $sn .= substr($baseChars, rand(0,33), 1);
        }
        return $sn;
    }

    /**
     * 根据优惠券ID获取优惠券详情
     * @param  [type] $couponid [description]
     * @return [type]         [description]
     */
    public function getCouponDetailById($couponid) {
        $sql = "select c.*, t.name, t.value, t.threshold, t.goodsid from ".self::tableName()." c 
                left join ".self::tableCouponType()." t on (c.type_id = t.id) 
                where c.id={$couponid}";
        return $this->connection->createCommand($sql)->queryAll();
    }

    public function getCouponByUid($uid, $status = 2)
    {
        if ($status === 2) {
            $condition = 'c.order_id is null';
        } else {
            $condition = "c.status = $status";
        }

        $sql = "select c.*, t.name, t.value, t.threshold, t.goodsid from ".self::tableName()." c 
                left join ".self::tableCouponType()." t on (c.type_id = t.id) 
                where c.uid = $uid and $condition order by c.type_id ASC";
        $command = $this->connection->createCommand($sql);
        $rows = $command->queryAll();

        return $rows;
    }

    public function getListByStatus($uid)
    {
        $coupons2 = $this->getCouponByUid($uid, 2);
        $coupons3 = $this->getCouponByUid($uid, 3);
        $coupons4 = $this->getCouponByUid($uid, 4);
        $coupons = [$coupons2, $coupons3, $coupons4];

        $coupons_status = [];
        foreach ($coupons as $cps) {
            $types = [];
            foreach ($cps as $cp) {
                $type_id = $cp['type_id'];

                if (!isset($types[$type_id])) {
                    $types[$type_id] = $cp;
                    $types[$type_id]['total'] = 1;
                } else {
                    $types[$type_id]['total']++;
                }
            }

            //复位键值
            $tmp = [];
            foreach ($types as $t) {
                $tmp[] = $t;
            }

            $coupons_status[] = $tmp;
        }

        return $coupons_status;
    }

    /**
     * 根据用户id获取可用优惠券
     * @param  intval $uid 用户id
     * @return array
     */
    public function getAvailableCouponListByUid($uid) {
        $condition = '';
        if ($status === 2) {
            $condition = 'c.order_id is null';
        } else {
            $condition = "c.status  between 1 and 2 ";
        }

        $sql = "select c.*, t.name, t.value, t.threshold, t.goodsid from ".self::tableName()." c 
                left join ".self::tableCouponType()." t on (c.type_id = t.id) 
                where c.uid = $uid and $condition order by c.type_id ASC";
        return $this->connection->createCommand($sql)->queryAll();
    }

    public function getCouponByOrderId($order_id)
    {
        $sql = "select c.*, t.name, t.value, t.threshold, t.goodsid from ".self::tableName()." c 
                left join ".self::tableCouponType()." t on (c.type_id = t.id) 
                where c.order_id = $order_id";
        $command = $this->connection->createCommand($sql);
        $row = $command->queryOne();

        return $row;
    }

    public function getOneByType($type_id)
    {
        $sql = "select c.*, t.name, t.value, t.threshold, t.goodsid from ".self::tableName()." c 
                left join ".self::tableCouponType()." t on (c.type_id = t.id) 
                where c.type_id = $type_id and c.status=2 and c.uid is null";
        $command = $this->connection->createCommand($sql);
        $row = $command->queryOne();

        return $row;
    }

    public function deliverCouponForReg($uid, $where) {
        $couponId = $this::find()->where($where)->asArray()->one();
        if ($couponId['id']) {
            $sql = "UPDATE ".self::tableName()." SET uid={$uid} WHERE id=".$couponId['id'];
            return $this->connection->createCommand($sql)->execute();
        } else {
            return false;
        }
    }
}
//end file