<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class ShoppingCollect extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%members_collect}}';
    }

    public function shopping_goods()
    {
        return '{{%shopping_goods}}';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     *根据用户Id和商品id获取收藏记录详情
     */
    public function getCollectDataOne($uid, $goodsid) {
        return $this::find()->where(['uid'=>$uid, 'goodsid'=>$goodsid])->one();
    }

    /*
     * 查询用户的收藏列表
     */
    public function getCollectListByUid($uid) {
        return (new Query())
                ->select('*')
                ->from("{$this->tableName()} as c")
                ->leftJoin("{$this->shopping_goods()} as g", 'c.goodsid=g.id')
                ->where(['c.uid' => $uid,'c.status' => 1, 'g.status'=>1])
                ->orderBy('c.createdt DESC')
                ->All();
    }

}