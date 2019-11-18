<?php
namespace frontline\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Session;
use yii\helpers\BaseArrayHelper;
use frontline\models\UserFeedback;
class HomeController extends Controller{
    // public $enableCsrfValidation = false;
    /**
     * 主页
     */
    public function actionIndex() {
         $this->layout='blank';
        $cache = Yii::$app->cache;
        return $this->render('index', []);
    }

    /**
     * 品牌故事
     */
    public function actionIntro(){
        return $this->render('brand',[]);
    }

    /**
     * 招商加盟
     */
    public function actionJoin(){
        return $this->render('join',[]);
    }

    /**
     * 关于我们
     */
    public function actionAbout(){
        return $this->render('about',[]);
    }

    /**
     * 联系我们
     */
    public function actionRelation(){
        return $this->render('relation',[]);
    }

    /**
     * 意见反馈
     */
    public function actionOpinion(){
        return $this->render('opinion');
    }

    /**
     * 提交意见
     */
    public function actionSubmitOpinion(){
        $request = Yii::$app->request;
        $title = $request->post('title');
        $address = $request->post('address');
        $content = $request->post('content');
        $time = $request->post('time');
        $feedback = new UserFeedback();
        $feedback->title = $title;
        $feedback->content = $content;
        $feedback->address = $address;
        $feedback->ctime = $time;
        $feedback->save();
        return json_encode(array('content'=>'提交成功！'));
    }

}