<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%replenish}}".
 *
 * @property int $id
 * @property string $replenishsn 补货单号
 * @property string $operatorid 补货员id
 * @property int $cabinetid
 * @property int $pathway 轨道编号
 * @property string $productsn 轮胎条码
 * @property int $total 需补数量
 * @property int $num 已补数量
 * @property int $status 状态:0-未补 1-未完 2-补完
 * @property int $createdt
 * @property int $updatedt
 */
class Replenish extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%replenish}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['replenishsn', 'operatorid', 'cabinetid', 'pathway', 'productsn', 'createdt'], 'required'],
            [[ 'cabinetid', 'pathway', 'total', 'num', 'status', 'createdt', 'updatedt'], 'integer'],
            [['replenishsn'], 'string', 'max' => 30],
            [['productsn'], 'string', 'max' => 18],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'replenishsn' => 'Replenishsn',
            'operatorid' => 'Operatorid',
            'cabinetid' => 'Cabinetid',
            'pathway' => 'Pathway',
            'productsn' => 'Productsn',
            'total'       => 'Total',
            'num'         => 'Num',
            'status'      => 'Status',
            'createdt'    => 'Createdt',
            'updatedt'    => 'Updatedt',
        ];
    }
}