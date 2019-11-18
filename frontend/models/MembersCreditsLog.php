<?php 
 
namespace frontend\models; 
 
use Yii;
use yii\db\Query;
use yii\base\Model;
use frontend\models\Corpteam; 
 
/** 
* This is the model class for table "{{%members_credits_log}}". 
* 
* @property string $id 
* @property string $uid 积分变动日志 
* @property string $type 积分类型 
* @property string $amount 变动值 
* @property string $remarks 积分变动日志 
* @property string $createtime 变动时间 
* @property string $carsn 车牌号 
*/ 
class MembersCreditsLog extends \yii\db\ActiveRecord 
{ 
   /** 
    * {@inheritdoc} 
    */ 
   public static function tableName() 
   { 
       return '{{%members_credits_log}}'; 
   }

   /*
    * 获取副卡用户分配记录
   */
   public function getCreditsLog($uid,$driverid){
      $sql =  ['between','createtime',strtotime(date("Y-m-d H:i:s", strtotime("-3 month"))), time()];
      return (new Query())->select('*')
                        ->from(MembersCreditsLog::tableName())
                        ->where(['uid' => $uid,'driverid' => $driverid])
                        ->andwhere($sql)
                        ->orderBy('createtime DESC')
                        ->all();
   }

   /*
    * 副卡用户分配金额求和
   */
   public function sumCreditsAmount($uid,$driverid){
      return (new Query())->select('uid,driverid,createtime,sum(`amount`) as amount')
                        ->from(MembersCreditsLog::tableName())
                        ->where(['uid' => $uid,'driverid' => $driverid])
                        ->one();
   }

   /*
    * 获取副卡用户最近一次分配金额的时间
   */
   public function getCreateTime($uid,$driverid){
      return (new Query())->select('uid,driverid,createtime')
                        ->from(MembersCreditsLog::tableName())
                        ->where(['uid' => $uid,'driverid' => $driverid])
                        ->orderBy('createtime DESC')
                        ->limit(1)
                        ->one();
   }

   /** 
    * {@inheritdoc} 
    */ 
   public function rules() 
   { 
       return [ 
           [['uid', 'type', 'amount'], 'required'], 
           [['uid','type'], 'integer'], 
           [['amount'], 'number'], 
           [['remarks'], 'string'], 
           [['createtime','driverid'], 'integer'], 
       ]; 
   } 
 
   /** 
    * {@inheritdoc} 
    */ 
   public function attributeLabels() 
   { 
       return [ 
           'id' => 'ID', 
           'uid' => 'Uid', 
           'type' => 'Type', 
           'amount' => 'Amount', 
           'remarks' => 'Remarks', 
           'createtime' => 'Createtime', 
           'driverid' => 'Driverid', 
       ]; 
   } 
} 
 