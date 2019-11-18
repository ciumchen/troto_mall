<?php
namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%shopping_goods_param}}".
 *
 * @property int $id
 * @property int $goodsid
 * @property string $title
 * @property string $value
 * @property int $displayorder
 */
class ShoppingGoodsParam extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%shopping_goods_param}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['goodsid', 'displayorder'], 'integer'],
            [['value'], 'string'],
            [['title'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'goodsid' => 'Goodsid',
            'title' => 'Title',
            'value' => 'Value',
            'displayorder' => 'Displayorder',
        ];
    }
}