<?php
namespace frontend\models;

use Yii;
use yii\base\Model;

class ShoppingAddress extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%shopping_address}}';
    }

    /**
     * 查询user全部地址信息
     */
    public function getGoodsAddressByGoodsUid($uid, $isdefault)
    {
        if ($isdefault == '') {
            $where = ['uid' => $uid];
        } else {
            $where = ['uid' => $uid, 'isdefault' => 1];
        }
        return $this::find()->where($where)->orderBy(' isdefault desc')->all();
    }

    /**
     * 修改user地址信息
     */
    public function UpdateAddress($realname, $idno, $mobile, $s_province, $s_city, $s_county, $address, $isdefault, $id)
    {
        return $this::updateAll(
            array(
                'realname' => $realname,
                'mobile'   => $mobile,
                'idno'     => $idno,
                'province' => $s_province,
                'city'     => $s_city,
                'area'     => $s_county,
                'address'  => $address,
                'isdefault'=> ($isdefault == 'on') ? 1 : 0
            ), 'id='.$id
        );
    }

    /**
     * 修改默认地址
     */
    public function isdefault($uid, $id)
    {
        return $this::updateAll(array('isdefault' => 0), "uid='$uid' AND id!=" . $id);
    }

    /**
     * 删除user收货地址
     */
    public function deleteAddress($id)
    {
        return $this->deleteAll("id=" . $id);
    }

    /**
     * 查询user全部地址信息(Array)
     */
    public function getAddressByUid($uid, $isdefault)
    {
        if ($isdefault == '') {
            $where = ['uid' => $uid];
        } else {
            $where = ['uid' => $uid, 'isdefault' => 1];
        }
        return $this::find()->where($where)->orderBy(' isdefault desc')->asArray()->all();
    }

    /**
     * 创建订单时获取地址, 没有选择id, 就获取默认地址
     */
    public function getAddressToShopping($uid, $address_id = null){
        if($address_id != null){
            $where = ['uid' => $uid, 'id' => $address_id];
            $res = $this::find()->where($where)->asArray()->one();// 获取指定的地址
        }
        if(empty($res)){
            $where = ['uid' => $uid, 'isdefault' => 1];
            $res = $this::find()->where($where)->asArray()->one();
        }
        return $res;
    }
}

//end file
