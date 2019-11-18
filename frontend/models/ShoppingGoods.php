<?php
namespace frontend\models;

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
     * @inheritdoc
     */
    public function rules() {
        return [
        ];
    }

    /**
     * 根据分类ID获取分类下面产品
     *@parameter field字段名
     *@parameter 条件
     *@parameter 限制
     *@return array
     */
    public function getCategoryGoodsByCateId($field, $cateId, $offset=0)
    {
        //判断字段是否成立
        if ($field=='pcate' || $field=='ccate') {
            if ($cateId) {
                $condition = $field."= '{$cateId}' and g.status=1 and g.deleted=0 and g.isflash=0";
            } else {
                $condition = "g.status=1 and g.deleted=0 and g.isflash=0";
            }
        }
        //热门产品
        if ($field == 'ishot') {
            $condition = "ishot = '{$cateId}' and g.status=1 and g.deleted=0 and g.isflash=0";
        }
        //单个推荐产品
        if ($field == 'isrecommand') {
            $condition = "isrecommand = '{$cateId}' and g.status=1 and g.deleted=0 and g.isflash=0";
        }

        // 根据产品id号查询推荐产品
        if ($field == 'relatedgoods') {
            $condition = "relatedgoods = '{$cateId}' and g.status=1 and g.deleted=0 and g.isflash=0";
        }

        $query = (new Query())
            ->select('g.*,c.name countries,c.symbol_code')
            ->from("{$this->tableName()} g")
            ->leftJoin("{$this->shopping_country()} c",'g.country = c.symbol_code')
            ->where($condition)
            ->offset($offset)
            ->limit(8)
            ->orderBy('displayorder desc')
            ->all();
       return $query;
    }

    /**
     * 获取首页商品
     * @return array
     */
    public function getTrotoIndexGoodsList() {
        return [
            'new' => $this::find()->where([
                          'isrecommand' => 1, 'isnew' => 1, 'status'=>1, 'deleted'=>0
                     ])->orderBy('displayorder DESC')->limit(3)->all(),
            'hot' => $this::find()->where([
                          'isrecommand' => 1, 'ishot' => 1, 'status'=>1, 'deleted'=>0
                     ])->orderBy('displayorder DESC')->limit(3)->all(),
        ];
    }

    /**
     * 获取首页推荐商品
     * @param intval $param 条件
     * @return array
     */
    public function getIndexRecommandGoodsList($limit=20) {
        $condition = "g.isrecommand=1 and g.status=1 and g.deleted=0 and g.isflash=0";
        $query = (new Query())
            ->select('g.*,c.name countries,c.symbol_code')
            ->from("{$this->tableName()} g")
            ->leftJoin("{$this->shopping_country()} c",'g.country = c.symbol_code')
            ->where($condition)
            ->limit($limit)
            ->orderBy('displayorder DESC')
            ->all();
       return $query;
    }

    /**
     * 统计产品总数
     * @param  array $where 查询条件
     * @return intval
     */
	public function countGoodsTotal($where) {
        $query = (new Query())
            ->select('count(id) as total')
            ->from("{$this->tableName()}")
            ->where($where)
            ->all();
        return intval($query[0]['total']);
    }
	
	/**
     * 推荐产品总数
	 *@return array
     */
	public function goodsCountIsrecommand()
    {
        $query = (new Query())
            ->select('count(*) as count')
            ->from("{$this->tableName()}")
            ->where(['status'=>1,'deleted'=>0,'isrecommand' => 1])
            ->one();
        return $query;
    }

    /*
    * 更据产品id查询产品信息
    */
    public function getGoodsDetailByGoodsId($id)
    {

        $query = (new Query())
            ->select('g.*,w.name wname, c.*,b.spellname,b.fullname,b.brandimg,b.brandurl,b.content as b_content')
            ->from("{$this->tableName()} g")
            ->leftJoin("{$this->shopping_country()} c", 'g.country = c.symbol_code')
            ->leftJoin("{{%shopping_warehouse}} w", 'g.wid = w.id')
			      ->leftJoin("{{%shopping_brand}} b", 'b.brand = g.brand')
            ->where(['g.id' => $id,'g.status'=>1,'g.deleted'=>0])
            ->one();
        return $query;

    }

    /*
    * 获取全部产品的信息
    */
    public function getGoodsAllDetailByGoodsId($id)
    {
        $query = (new Query())
            ->select('*, c.*')
            ->from("{$this->tableName()} g")
            ->leftJoin("{{%shopping_warehouse}} w", 'g.wid = w.id')
            ->leftJoin("{{%shopping_country}} c", 'c.symbol_code = g.country')
            ->where(['g.id' => $id,'g.status'=>1, 'g.deleted'=>0])
            ->one();
        return $query;
    }

    /**
     * 获取产品参数数据
     */
    public function goodsParam($goodsid)
    {
        $query = (new Query())
            ->select('*')
            ->from("{$this->shopping_goods_param()}")
            ->where(['goodsid' => $goodsid])
            ->orderBy('displayorder DESC')
            ->all();
        return $query;
    }

    /**
     * 查询产品规格
     */
    public function goodsOption($goodsid)
    {
        $query = (new Query())
            ->select('*')
            ->from("{{%shopping_goods_option}}")
            ->where(['goodsid' => $goodsid])
            ->all();
        return $query;
    }

    /**
     * 规格id查询价格
     */
    public function optionId($id){
        $query = (new Query())
            ->select('*')
            ->from("{{%shopping_goods_option}}")
            ->where(['id' => $id])
            ->one();
        return $query;
    }

    public function getById($id)
    {
        return $this::find()->where(['id' => $id,'status'=>1, 'deleted'=>0])->one();
    }
	
	/**
     * @搜索商品
	 * @parameter $searchKeywords 产品标题
     */
	 public function search($searchKeywords,$limits=''){
		 $query = (new Query())
            ->select('*,c.countryimg')
            ->from("{$this->tableName()} as g")
			->leftJoin("{$this->shopping_country()} c",'g.country = c.symbol_code')
            ->where(['g.isflash'=>0, 'g.status'=>1, 'g.deleted'=>0])
            ->andwhere(['OR', "title LIKE '%{$searchKeywords}%'", "fdesc LIKE '%{$searchKeywords}%'", "tags LIKE '%{$searchKeywords}%'"])
			->offset($limits)
            ->limit(8)
            ->all();
        return $query;
	 }

     /**
     * @搜索商品数量
     * @parameter title 产品标题
     */
    public  function countSearch($title){
        $query = (new Query())
            ->select('count(*) as count')
            ->from("{$this->tableName()}")
            ->where(['isflash'=>0, 'status'=>1, 'deleted'=>0])
            ->andwhere(['like', 'title', '%'.$title.'%', false])
            ->one();
        return $query;
    }

    /**
     * @产品标签
     */

    public function label(){
             $query = (new Query())
            ->select('*')
            ->from("{{%shopping_tags}}")
            ->where(['enabled'=>1])
            ->orderBy('displayorder desc')
            ->all();
        return $query;
    }

    /**
     * 获取全部品列表
     * @return array
     */
    public function getNewGoodsList() {
        return $this::find()->where([
                'isnew' => 1, 'status'=>1, 'deleted'=>0
            ])
              ->orderBy('displayorder DESC')
              ->all();
    }

    /**
     * 获取全部热销推荐列表
     * @return array
     */
    public function getHotGoodsList() {
        return $this::find()->where([
                'ishot' => 1, 'status'=>1, 'deleted'=>0
            ])
              ->orderBy('displayorder DESC')
              ->all();
    }

    /**
     * 获取全部必买推荐列表
     * @return array
     */
    public function getMustBuyGoodsList() {
        return $this::find()->where([
                'isdiscount' => 1, 'status'=>1, 'deleted'=>0
            ])
              ->orderBy('displayorder DESC')
              ->all();
    }

    /**
     * 获取全部天天特价列表
     * @return array
     */
    public function getBargainGoodsList() {
        return $this::find()->where([
                'isbrush' => 1, 'status'=>1, 'deleted'=>0
            ])->andWhere(['>=', 'timeend', time()])
              ->andWhere(['<=', 'timestart', time()])
              ->orderBy('displayorder DESC')
              ->all();
    }
	
}