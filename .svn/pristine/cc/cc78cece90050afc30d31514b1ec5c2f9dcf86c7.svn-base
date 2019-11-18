<?php
namespace frontline\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class UserFeedback extends \yii\db\ActiveRecord
{
	private $connection;

    public function init(){
        $this->connection = Yii::$app->db;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_feedback}}';
    }
    
}