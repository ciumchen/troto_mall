<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

use common\components\SMSComponents;

/**
 * This is the model class for table "{{%sms_log}}".
 *
 * @property int $smsid
 * @property string $mobile
 * @property int $type
 * @property string $content
 * @property int $result 0-失败 1-成功 2-错误
 * @property int $createdt
 */
class SmsLog extends ActiveRecord {

    //验证码类型： 绑定，用户取胎码，服务车取胎码，消费，结果通知
    const TYPE_BIND     = 1;
    const TYPE_PICk     = 2;
    const TYPE_SPICK    = 3;
    const TYPE_BALANCE  = 5;
    const TYPE_PICk_RST = 6;

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%sms_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['mobile', 'content', 'createdt'], 'required'],
            [['type', 'result', 'createdt'], 'integer'],
            [['mobile'], 'string', 'max' => 11],
            [['content'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'smsid' => 'Smsid',
            'mobile' => 'Mobile',
            'type' => 'Type',
            'content' => 'Content',
            'result' => 'Result',
            'createdt' => 'Createdt',
        ];
    }

    /**
     * [send description]
     * @param  string  $mobile 手机号
     * @param  string  $text   短信正文(不包含签名)
     * @param  integer $type   短信类型
     * @return null
     */
    public static function send($mobile, $text, $type){
        $text = '【多轮多】'.$text;
        $rs = SMSComponents::send($mobile, $text);
        $model = new self();
        $model->mobile   = $mobile;
        $model->type     = $type;
        $model->content  = $text;
        $model->result   = intval($rs);
        $model->createdt = time();
        $model->save();
        return $rs;
    }

    public static function generateCode($bits=6) {
        $baseNum = '012356789';
        $baseNums = strlen($baseNum)-1;
        $code = '';
        for ($i=0; $i < $bits; $i++) {
            $code .= $i ? substr($baseNum, rand(0, $baseNums), 1) : substr($baseNum, rand(1, $baseNums), 1);
        }
        return $code;
    }

}
