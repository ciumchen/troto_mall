<?php
    namespace comapi\models;
    use Yii;
    use yii\db;
    use yii\base\Model;
    use yii\db\ActiveRecord;
    use common\rpc\Cabint;

    class CabintModel
    {
        public function TableName()
        {
            return 'ims_ims_cabinet';
        }
        public function updateStatus()
        {
            $res = "UPDATE `{$this->TableName()}` SET `status = `" . $status . " WHERE `deviceid` = " . $deviceid;
            return $res;
        }

        public function updateBreakdown()
        {
            $res = "UPDATE `{$this->TableName()}` SET `status = `" . $status . " WHERE `deviceid` = " . $deviceid;
            return $res;
        }
    }