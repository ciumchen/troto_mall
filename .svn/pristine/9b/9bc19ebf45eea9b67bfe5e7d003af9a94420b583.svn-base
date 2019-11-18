<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "ims_shopping_adv".
 *
 * @property integer $id
 * @property integer $weid
 * @property string $advname
 * @property string $link
 * @property string $thumb
 * @property integer $displayorder
 * @property integer $enabled
 */
class ImsShoppingAdv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shopping_adv}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weid', 'displayorder', 'enabled'], 'integer'],
            [['advname'], 'string', 'max' => 50],
            [['link', 'thumb'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'weid' => 'Weid',
            'advname' => 'Advname',
            'link' => 'Link',
            'thumb' => 'Thumb',
            'displayorder' => 'Displayorder',
            'enabled' => 'Enabled',
        ];
    }

    /**
     * @adv 
     * @parameter type 广告类型
     * @return array
     */
    public function adv($type=1){
        return $this::find()
            ->where(['enabled' => 1,'type'=>$type])
            // ->andFilterWhere(['<', 'starttime', time()])
            // ->andFilterWhere(['>', 'endtime', time()])
            ->asArray()
            ->orderBy('displayorder desc')
            ->all();
    }
    
}