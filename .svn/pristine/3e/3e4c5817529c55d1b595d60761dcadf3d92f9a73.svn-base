<?php
namespace frontend\models;

use Yii;
use yii\db\Query;
use yii\db\ActiveRecord;
use yii\base\Model;

use frontend\models\Users;
use frontend\models\CorpteamDriver;
use frontend\models\MembersCreditsLog;

/**
 * This is the model class for table "{{%corpteam}}".
 *
 * @property string $corpteamid
 * @property string $uid
 * @property string $name 车队名称
 * @property int $status 车队状态:0 未启用,1 启用
 * @property string $createdt 创建时间
 * @property string $updatedt 更新时间
 */
class Corpteam extends \yii\db\ActiveRecord {
    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%corpteam}}';
    }

    /*
	* 主卡用户查出所有车队及副卡用户
    */
    public function getCorpteam($uid, $expire=''){
    	$whereCondition = ['c.uid' => $uid];
    	// 近3天为 3天前到现在时间为止的用户创建时间
    	$sqld =  ['between','cd.createdt',strtotime(date("Y-m-d H:i:s", strtotime("-3 day"))), time()];
    	$sqlm =  ['between','cd.createdt',strtotime(date("Y-m-d H:i:s", strtotime("-1 month"))), time()];
    	if ($expire!=='') {
    		if ($expire == -3) {
    			$sql = $sqld;
    		} else{
    			$sql = $sqlm;
    		}
    		return (new Query())->select('c.*,cd.*,m.uid,m.mobile,m.realname,m.avatar,m.deposit')
    						->from(Corpteam::tableName() . 'as c')
    						->leftjoin(CorpteamDriver::tableName() .' as cd', 'c.corpteamid=cd.corpteamid')
    						->leftjoin(Users::tableName() . ' as m', 'cd.uid=m.uid')
    						->where($whereCondition)
    						->andwhere(['c.status' => 1,'cd.status' => 1])
    						->andFilterWhere($sql)
    						->orderBy('cd.createdt DESC')
    						->all();
        } else {
        	return (new Query())->select('c.*,cd.*,m.uid,m.mobile,m.realname,m.avatar,m.deposit')
    						->from(Corpteam::tableName() . 'as c')
    						->leftjoin(CorpteamDriver::tableName() .' as cd', 'c.corpteamid=cd.corpteamid')
    						->leftjoin(Users::tableName() . ' as m', 'cd.uid=m.uid')
    						->where($whereCondition)
    						->andwhere(['c.status' => 1,'cd.status' => 1])
    						->orderBy('cd.createdt DESC')
    						->all();
        }
    }


    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['uid', 'name', 'createdt', 'updatedt'], 'required'],
            [['uid', 'status', 'createdt', 'updatedt'], 'integer'],
            [['name'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'corpteamid' => 'Corpteamid',
            'uid' => 'Uid',
            'name' => 'Name',
            'status' => 'Status',
            'createdt' => 'Createdt',
            'updatedt' => 'Updatedt',
        ];
    }

}
