<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

use frontend\models\ShoppingGoods;

/**
* This is the model class for table "{{%cabinet_pathway}}".
 *
 * @property int $pathwayid
 * @property int $cabinetid
 * @property int $pathway 轨道编号
 * @property int $status 轨道状态，-1维护中，0正常，1补货
 * @property int $goodsid 关联商品
 * @property string $price 机柜实际售价
 * @property int $stock 库存
 * @property int $deleted 是否废弃，0否 1是
 * @property int $createdt 创建时间
 * @property int $updatedt 更新时间
 */
class CabinetPathway extends ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%cabinet_pathway}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cabinetid', 'pathway', 'goodsid', 'createdt', 'updatedt'], 'required'],
            [['cabinetid', 'pathway', 'status', 'goodsid', 'stock', 'deleted', 'createdt', 'updatedt'], 'integer'],
            [['price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pathwayid' => 'Pathwayid',
            'cabinetid' => 'Cabinetid',
            'pathway' => 'Pathway',
            'status' => 'Status',
            'goodsid' => 'Goodsid',
            'price' => 'Price',
            'stock' => 'Stock',
            'deleted' => 'Deleted',
            'createdt' => 'Createdt',
            'updatedt' => 'Updatedt',
        ];
    }

    /**
     * 根据柜机编号获取柜机内商品
     *@param $cabinetId 柜机编号
     *@return array
     */
    public function getCabinetGoods($cabinetId) {
        return (new Query())
            ->select('cp.* ,g.title,g.thumb,g.thumb1,g.brand,g.description,g.fdesc')
            ->from("{$this->tableName()} cp")
            ->leftJoin(ShoppingGoods::tableName()." g", 'cp.goodsid=g.id')
            ->where(['cp.cabinetid'=>$cabinetId])
            ->all();
    }

}
