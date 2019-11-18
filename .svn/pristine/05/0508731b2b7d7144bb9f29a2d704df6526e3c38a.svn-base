<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

/**
 * This is the model class for table "{{%shopping_cart}}".
 *
 * @property int $cartid
 * @property int $uid
 * @property int $goodsid
 * @property int $goodstype
 * @property int $selected 0未选 1已选
 * @property int $total 购买数量
 * @property int $optionid
 * @property string $marketprice 单品售价
 * @property int $cabinetid
 * @property int $pathwayid
 * @property int $createtime
 */
class ShoppingCart extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%shopping_cart}}';
    }

    public static function goods() {
        return '{{%shopping_goods}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['uid', 'goodsid', 'total', 'cabinetid', 'pathwayid', 'createtime'], 'required'],
            [['uid', 'goodsid', 'goodstype', 'selected', 'total', 'optionid', 'cabinetid', 'pathwayid', 'createtime'], 'integer'],
            [['marketprice'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'cartid'      => 'Cartid',
            'uid'         => 'Uid',
            'goodsid'     => 'Goodsid',
            'goodstype'   => 'Goodstype',
            'selected'    => 'Selected',
            'total'       => 'Total',
            'optionid'    => 'Optionid',
            'marketprice' => 'Marketprice',
            'cabinetid'   => 'Cabinetid',
            'pathwayid'   => 'Pathwayid',
            'createtime'  => 'Createtime',
        ];
    }

    /*
    *查询user购物车
    */
    public function getCartGoodsByUid($uid, $selected=null) {
        if ($selected === null) {
            $condition = ['c.uid'=>$uid, 'cap.status'=>0];
        } else {
            $condition = ['c.uid'=>$uid, 'c.selected'=>$selected, 'cap.status'=>1];
        }

        return (new Query())
            ->select('c.cartid,cap.goodsid,cap.stock,cap.price,g.originalprice,g.title,g.maxbuy,g.brand,g.pcate,g.description,c.total,c.selected, g.sid, g.maxbuy,g.thumb,g.thumb1,cap.status,cap.cabinetid,ca.name,ca.addr_city,ca.addr_dist')
            ->from("{$this->tableName()} as c")
            ->leftJoin("{$this->goods()} as g", 'c.goodsid = g.id')
            ->leftJoin("{{%cabinet_pathway}} AS cap", 'cap.pathwayid = c.pathwayid')
            ->leftJoin("{{%cabinet}} AS ca", 'ca.cabinetid = cap.cabinetid')
            ->where($condition)
            ->orderBy('cap.cabinetid ASC')
            ->All();
    }

    /**
     * 查询一个产品，是否在用户的购物车里。
     */
    public function isIn($uid, $goodsid) {
        return $this::find()
            ->where(['uid' => $uid, 'goodsid' => $goodsid])
            ->one();
    }

    public function deleteGoodsById($uid, $goodsid)
    {
        $query = ShoppingCart::find()
            ->where(['uid' => $uid, 'goodsid' => $goodsid])
            ->one();

        if ($query !== false) {
            $query->delete();
        }
    }

    /*-
    * 修改user购物车数量数据
    */
    public function UpdateCart($c_id, $total)
    {
		$conditions = array('total' => $total);
        $state = $this::updateAll($conditions, 'id = ' . $c_id);
        if ($state) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 直接购买修改购物车数据数量
     */

    public function ImmediatelyUpdateCart($goodsid, $uid, $selected, $total)
    {
        $conditions = array('selected' => $selected, 'total' => $total);

        $state = $this::updateAll($conditions, ' goodsid = ' . $goodsid . ' and uid = ' . $uid);
        if ($state) {
            return true;
        } else {
            return false;
        }
    }


    /*
    * 删除购物车
    */
    public function deleteCartData($goodsid)
    {
        $state = $count = $this->deleteAll("goodsid in('$goodsid')"); //删除id为这些的数据
        if ($state) {
            return true;
        } else {
            return false;
        }
    }


    /*
	* 购物车总计
	*/
    public function SumCartTotal($CartData)
    {
        $sum = 0;
        $num = 0;
        if ($CartData) {
            foreach ($CartData as $SumCartTotal) {
                $sum += $SumCartTotal['marketprice'] * $SumCartTotal['total'];  //购物车全部商品总价
                $num++;
            }

            return array('sum' => $sum, 'num' => $num);
        }
    }

    /*
    * 初始购物车数据库字段selected
    */
    public function isdefault($uid, $id)
    {
        return $this::updateAll(array('selected' => 0), "uid='$uid' AND id!=" . $id);
    }

    /**
     *修改购物车价格
     *
    */

    public function EditPrice($price,$id){
         $this::updateAll(array('marketprice'=>$price), "id = '$id'");
    }


}
