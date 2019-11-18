<?php
namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class BrokerRewardWithdraw extends ActiveRecord
{

    public static $rewardWithdrawStatus = [
        '0' => '提交申请成功',
        '1' => '审核可发',
        '2' => '已发放',
        '3' => '审核未通过',
        '4' => '已取消',
    ];

    public static function tableName()
    {
        return '{{%brokers_reward_withdraw}}';
    }
}

//end file