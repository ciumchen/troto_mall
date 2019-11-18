<?php
namespace frontline\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class ShoppingGoods extends \yii\db\ActiveRecord
{
	private $connection;

    public function init(){
        $this->connection = Yii::$app->db;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shopping_goods}}';
    }

    public static function shopping_country()
    {
        return '{{%shopping_country}}';
    }

    public static function shopping_goods_param()
    {
        return '{{%shopping_goods_param}}';
    }

    /**
     * [getCategoryGoodsByCateId 获取分类商品]
     * @param  [int] $typeid [分类id]
     * @return [array]         
     */
    public function getCategoryGoodsByCateId($typeid){
        $query=(new Query())
            ->select("thumb,title,marketprice,id")
            ->from("{$this->tableName()}")
            ->where(['pcate'=>$typeid,'status'=>1,'deleted'=>0])
            ->orderBy(['displayorder'=>'SORT_DESC'])
            ->limit(18)
            ->all();
        return $query;
    }

    /**
     * [getGoodsInfo 查询单个商品详细]
     * @param  [type] $goodsid [商品id]
     * @return [array]         
     */
    public function getGoodsInfo($goodsid){
        $query = (new Query())
            ->select("a.*,b.name")
            ->from("{$this->tableName()} as a")
            ->leftJoin("{$this->shopping_country()} as b",'a.country=b.symbol_code')
            ->andwhere(['a.id'=>$goodsid])
            ->one();
        return $query;     
    }
    
}
