<?php
namespace frontend\models;

use Yii;
use yii\db\Query;
use yii\base\Model;
use frontend\models\Corpteam;

/**
 * This is the model class for table "{{%corpteam_driver}}".
 *
 * @property string $driverid
 * @property string $corpteamid
 * @property string $uid
 * @property string $carsn
 * @property string $phone
 * @property int $status 卡车状态:0 未启用,1 启用
 * @property string $expire_begin 有效期开始
 * @property string $expire_end 有效期结束
 * @property string $createdt 创建时间
 * @property string $updatedt 更新时间
 */
class CorpteamDriver extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%corpteam_driver}}';
    }

    /*
    * 副卡用户查出自己的所属车队信息
    */
    public function getUserDriver($driverid){
        return (new Query())->select('*')
                            ->from(CorpteamDriver::tableName() . 'as cd')
                            ->leftjoin(Corpteam::tableName() . 'as c','cd.corpteamid=c.corpteamid')
                            ->where(['driverid' => $driverid])
                            ->one();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['corpteamid', 'uid', 'carsn', 'phone', 'createdt', 'updatedt'], 'required'],
            [['corpteamid', 'uid', 'status', 'expire_begin', 'expire_end', 'createdt', 'updatedt'], 'integer'],
            [['carsn'], 'string', 'max' => 12],
            [['phone'], 'string', 'max' => 11],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'driverid' => 'Driverid',
            'corpteamid' => 'Corpteamid',
            'uid' => 'Uid',
            'carsn' => 'Carsn',
            'phone' => 'Phone',
            'status' => 'Status',
            'expire_begin' => 'Expire Begin',
            'expire_end' => 'Expire End',
            'createdt' => 'Createdt',
            'updatedt' => 'Updatedt',
        ];
    }
}
