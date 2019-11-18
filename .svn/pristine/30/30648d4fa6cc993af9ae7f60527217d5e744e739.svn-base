<?php
namespace frontend\models;

use Yii;
use yii\base\Model;


/**
 * This is the model class for table "ims_shopping_category".
 *
 * @property integer $id
 * @property integer $weid
 * @property string $name
 * @property string $thumb
 * @property integer $parentid
 * @property integer $isrecommand
 * @property string $description
 * @property integer $displayorder
 * @property integer $enabled
 * @property integer $community
 */
class ShoppingCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shopping_category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weid', 'parentid', 'isrecommand', 'displayorder', 'enabled', 'community'], 'integer'],
            [['name', 'thumb', 'description'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['thumb'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 500],
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
            'name' => 'Name',
            'thumb' => 'Thumb',
            'parentid' => 'Parentid',
            'isrecommand' => 'Isrecommand',
            'description' => 'Description',
            'displayorder' => 'Displayorder',
            'enabled' => 'Enabled',
            'community' => 'Community',
        ];
    }

    /* 
     * 首页分类
     */
    public function category($condition=[]) {
        return $this::find()
            ->where(['enabled'=>1])
            ->orderBy('displayorder DESC')
            ->asArray()
            ->all();
    }

    /*
    @根据产品pcate获取产品分类
    */
    public function getCategory($id)
    {
        $CategoryData = $this::find()->where(['id' => $id])->one();
        return $CategoryData;
    }

}