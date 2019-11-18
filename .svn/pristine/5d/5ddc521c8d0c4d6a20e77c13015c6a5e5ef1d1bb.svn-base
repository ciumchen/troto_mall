<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%members_pos}}".
 *
 * @property int $posid
 * @property int $uid
 * @property double $lat 纬度
 * @property double $lng 经度
 * @property string $addr_prov 省份
 * @property string $addr_city 市
 * @property string $addr_dist 区县
 * @property string $addr_mark 准确地址
 * @property string $geohash
 * @property int $createdt 创建时间
 */
class MembersPos extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%members_pos}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['lat', 'lng', 'addr_prov', 'addr_city', 'addr_dist', 'addr_mark', 'geohash', 'createdt'], 'required'],
            [['lat', 'lng'], 'number'],
            [['createdt'], 'integer'],
            [['addr_prov', 'addr_city', 'addr_dist', 'addr_mark'], 'string', 'max' => 50],
            [['geohash'], 'string', 'max' => 12],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'posid'     => 'posid',
            'uid'       => 'Uid',
            'lat'       => 'Lat',
            'lng'       => 'Lng',
            'addr_prov' => 'Addr Prov',
            'addr_city' => 'Addr City',
            'addr_dist' => 'Addr Dist',
            'addr_mark' => 'Addr Mark',
            'geohash'   => 'Geohash',
            'createdt'  => 'Createdt',
        ];
    }
}
