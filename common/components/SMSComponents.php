<?php
namespace common\components;

use common\extensions\ZtSMS;

/**
 * 短信发送组件
 * 备注：可通过增加extension中类似Ueswt.php来扩展多家短息通道
 */
class SMSComponents extends \yii\base\Object{

    /**
     * 发送短信
     * @param int $mobileNo 手机号
     * @param string $content 需要发送短信内容
     * @return bool
     */
    public static function send($mobileNo, $content){
        return ZtSMS::sendSMS($mobileNo, $content);
        // return Ueswt::sendSMS($mobileNo, $content);
    }

}