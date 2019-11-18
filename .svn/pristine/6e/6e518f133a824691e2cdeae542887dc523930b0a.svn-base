<?php
namespace frontline\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class CompanyNews extends \yii\db\ActiveRecord
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
        return '{{%company_news}}';
    }

    /**
     * [getNewsById 获取分类新闻]
     * @param  [type] $cateid [分类id]
     * @return [array]         
     */
    public function getNewsById($cateid){
        return self::find()->select("*")->where(['cateid'=>$cateid])->asArray()->all();
    }
    
}