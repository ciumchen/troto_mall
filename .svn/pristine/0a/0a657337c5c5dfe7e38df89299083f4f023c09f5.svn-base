<?php
namespace console\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;

class Members extends ActiveRecord {
    private $connection;

    public $openId;

    public function init(){
        $this->connection = Yii::$app->db;
    }

    public static function tableName() {
        return '{{%members}}';
    }

    /**
     * 检查过期未登录用户信息
     * @param  string  $openId 微信openid
     * @param  integer $period 检查的时间
     * @return array
     */
    public function checkUserInfo($openId, $period=36000) {
        $updatedt = time()-$period;
        // $query = $this->find()->where(['openid'=>$openId]);
        // $query = $query->andWhere(['<', 'updatedt', $updatedt]);
        $sql = 'SELECT * FROM '.self::tableName().' WHERE '."openid='{$openId}'";
        $query = $this->findBySql($sql);
        return $query->all();
    }

    /**
     * 保存用户信息
     * @param  array $data   用户信息
     * @param  string $openId openid
     * @return mixed
     */
    public function saveUser($data, $openId='') {
        if ($openId=='') {
            // echo $this->connection->createCommand()->insert($this->tableName(), $data)->getRawSql();
            $res = $this->connection->createCommand()
                ->insert($this->tableName(), $data)
                ->execute();
            if($res){
                return $this->connection->getLastInsertID();
            }
        } else {
            // echo $this->connection->createCommand()->update($this->tableName(), $data, "openid=:openid",[':openid'=>$openId])->getRawSql();
            return $this->connection->createCommand()
                                    ->update($this->tableName(), $data, "openid=:openid",[':openid'=>$openId])
                                    ->execute();
        }
    }

}