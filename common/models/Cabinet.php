<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%cabinet}}".
 *
 * @property int $cabinetid
 * @property string $name 货柜名称
 * @property string $sn 货柜编号
 * @property string $thumb 货柜照片
 * @property string $fee 安装服务价
 * @property int $status 货柜状态:-1维护,0未启用,1启用
 * @property int $stock 货柜库存
 * @property double $lat 纬度
 * @property double $lng 经度
 * @property string $addr_prov 省份
 * @property string $addr_city 市
 * @property string $addr_dist 区县
 * @property string $addr_mark 详细描述
 * @property string $address 详细地址
 * @property string $geohash
 * @property string $secret
 * @property int $createdt 创建时间
 * @property int $updatedt 更新时间
 */
class Cabinet extends ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%cabinet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name', 'sn', 'thumb', 'lat', 'lng', 'addr_prov', 'addr_city', 'addr_dist', 'addr_mark', 'geohash', 'createdt', 'updatedt'], 'required'],
            [['status', 'stock', 'createdt', 'updatedt'], 'integer'],
            [['fee', 'lat', 'lng'], 'number'],
            [['name', 'address'], 'string', 'max' => 300],
            [['sn'], 'string', 'max' => 5],
            [['addr_prov', 'addr_city', 'addr_dist', 'addr_mark'], 'string', 'max' => 50],
            [['geohash'], 'string', 'max' => 12],
            [['secret'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'cabinetid' => 'Cabinetid',
            'name'      => 'Name',
            'sn'        => 'sn',
            'thumb'     => 'thumb',
            'status'    => 'Status',
            'stock'     => 'Stock',
            'lat'       => 'Lat',
            'lng'       => 'Lng',
            'addr_prov' => 'Addr Prov',
            'addr_city' => 'Addr City',
            'addr_dist' => 'Addr Dist',
            'addr_mark' => 'Addr Mark',
            'address'   => 'Address',
            'geohash'   => 'Geohash',
            'secret'    => 'Secret',
            'createdt'  => 'Createdt',
            'updatedt'  => 'Updatedt',
        ];
    }

}
