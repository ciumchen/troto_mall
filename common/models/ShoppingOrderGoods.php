<?php
namespace common\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

use common\models\ShoppingOrder;
use common\models\CabinetPathway;
use frontend\models\ShoppingGoods;

/**
 * This is the model class for table "{{%shopping_order_goods}}".
 *
 * @property int $id
 * @property int $orderid 订单ID
 * @property int $goodsid 商品ID
 * @property int $pathway 机柜中商品轨道编号
 * @property string $price 商品或者规格的单价
 * @property int $total 数量
 * @property int $picknum 完成数量
 * @property int $createtime 创建时间
 * @property int $picktime
 * @property int $optionid 规格ID
 * @property string $optionname 规格名
 * @property int $cancelgoods 0正常1换货2退货
 * @property int $status -1申请失败0正常1申请售后2通过3换货中4完成
 * @property int $deleted 1删除商品（减总价）
 * @property int $sid 供应商
 */
class ShoppingOrderGoods extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%shopping_order_goods}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['orderid', 'goodsid', 'createtime'], 'required'],
            [['orderid', 'goodsid', 'pathway', 'total', 'createtime', 'optionid', 'cancelgoods', 'status', 'deleted', 'sid', 'picknum', 'picktime'], 'integer'],
            [['price'], 'number'],
            [['optionname'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id'          => 'ID',
            'orderid'     => 'Orderid',
            'goodsid'     => 'Goodsid',
            'pathway'     => 'pathway',
            'price'       => 'Price',
            'total'       => 'Total',
            'picknum'     => 'Picknum',
            'createtime'  => 'Createtime',
            'picktime'    => 'Picktime',
            'optionid'    => 'Optionid',
            'optionname'  => 'Optionname',
            'cancelgoods' => 'Cancelgoods',
            'status'      => 'Status',
            'deleted'     => 'Deleted',
            'sid'         => 'Sid',
        ];
    }

    /**
     * 根据订单号获取订单内商品详细
     * @param int $orderid 订单编号
     * @return array
     */
    public function getOrderGoodsList($orderid) {
        $fields = 'o.createtime, o.goodsprice AS price, o.price AS payprice, o.taxtotal, 
        g.id,g.title,g.brand,g.fdesc,g.sid,g.pcate,g.ccate,g.status,g.thumb,g.unit,g.description,g.content,g.goodssn,cp.productsn,
        og.picktime,og.price AS saleprice,og.total, og.pathway, og.picknum';
        return (new Query())->select($fields)
                            ->from(self::tableName()." as og")
                            ->leftJoin(ShoppingOrder::tableName()." as o", 'og.orderid=o.id')
                            ->leftJoin(ShoppingGoods::tableName()." as g", 'og.goodsid=g.id')
                            ->leftJoin(CabinetPathway::tableName()." as cp", 'o.cabinetid=cp.cabinetid AND og.pathway=cp.pathway')
                            ->where(['og.orderid'=>$orderid])
                            ->All();
    }

}
