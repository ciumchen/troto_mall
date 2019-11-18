<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%regions}}".
 *
 * @property int $regionid
 * @property int $parentid 父ID
 * @property int $level 级别
 * @property string $name 名称
 * @property int $country 国家
 * @property string $prov 所属省份
 * @property string $city 地区名称
 * @property string $dist 地区名称
 * @property string $spell 简拼
 * @property string $fspell 全拼
 * @property int $status 服务支持，1支持，0未开通
 * @property double $fee 服务费率
 */
class Regions extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%regions}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['regionid', 'parentid', 'name', 'country'], 'required'],
            [['regionid', 'parentid', 'level', 'country', 'status'], 'integer'],
            [['fee'], 'number'],
            [['name'], 'string', 'max' => 100],
            [['prov', 'city', 'dist'], 'string', 'max' => 60],
            [['spell'], 'string', 'max' => 10],
            [['fspell'], 'string', 'max' => 80],
            [['regionid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'regionid' => 'Regionid',
            'parentid' => 'Parentid',
            'level'    => 'Level',
            'name'     => 'Name',
            'country'  => 'Country',
            'prov'     => 'Prov',
            'city'     => 'City',
            'dist'     => 'Dist',
            'spell'    => 'Spell',
            'fspell'   => 'Fspell',
            'status'   => 'Status',
            'fee'      => 'Fee',
        ];
    }

    /**
     * 根据上级编号查询开放服务的区域
     * @param $parentid int 父级区域编码
     * @return array
     */
    public function getOpenDist($parentid=0) {
        return self::find()->where([
                'parentid' => $parentid,
                'status'   => 1,
            ])->asArray()->all();
    }

}
