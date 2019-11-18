<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class ShoppingTopic extends \yii\db\ActiveRecord {
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%shopping_topic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
        ];
    }

    /**
     * 获取专题详情
     * @param  int $topicid 专题ID
     * @return array
     */
    public function getOneDetail($topicid) {
        return $this::find()->where(['enabled'=>1, 'topicid'=>$topicid, 'isjump'=>0])
                            ->asArray()
                            ->one();
    }

    /**
     * 获取当前可用的专题列表
     * @return array
     */
    public function getActiveTopicList() {
        return $this::find()->where(['enabled'=>1,'isfocus'=>0])
                            ->orderBy('displayorder DESC')
                            ->asArray()
                            ->all();
    }

}