<?php
namespace frontend\controllers;

use common\models\User;
use frontend\models\Users;
use Yii;
use yii\web\Controller;
use frontend\models\MembersInterest;
use frontend\models\ShoppingCategory;
use frontend\models\UsersProfile;

class RegisterController extends Controller
{
    public $layout = 'blank';

    public function actionNewAccountTest()
    {
        $Users = new Users();
        $user = $Users->newAccount([
            'openid' => '',
            'broke_id' => 0
        ]);

        var_dump($user);
    }

    public function actionSex()
    {

        return $this->render('sex', [
        ]);
    }

    public function actionInterest()
    {
        $uid = Yii::$app->session['userinfo']['uid'];

        //保存性别
        $sex = Yii::$app->request->get('sex');
        $userProfile = new UsersProfile();
        $userProfile->uid = $uid;
        $userProfile->gender = $sex;
        $userProfile->createtime = time();
        $userProfile->realname = '';
        $userProfile->nickname = '';
        $userProfile->avatar = '';
        $userProfile->qq = '';
        $userProfile->mobile = '';
        $userProfile->fakeid = '';
        $userProfile->vip = 0;
        $userProfile->birthyear = 0;
        $userProfile->birthmonth = 0;
        $userProfile->birthday = 0;
        $userProfile->constellation = '';
        $userProfile->zodiac = '';
        $userProfile->telephone = '';
        $userProfile->idcard = '';
        $userProfile->studentid = '';
        $userProfile->grade = '';
        $userProfile->address = '';
        $userProfile->zipcode = '';
        $userProfile->nationality = '';
        $userProfile->resideprovince = '';
        $userProfile->residecity = '';
        $userProfile->residedist = '';
        $userProfile->graduateschool = '';
        $userProfile->company = '';
        $userProfile->education = '';
        $userProfile->occupation = '';
        $userProfile->position = '';
        $userProfile->revenue = '';
        $userProfile->affectivestatus = '';
        $userProfile->lookingfor = '';
        $userProfile->bloodtype = '';
        $userProfile->height = '';
        $userProfile->weight = '';
        $userProfile->alipay = '';
        $userProfile->msn = '';
        $userProfile->email = '';
        $userProfile->taobao = '';
        $userProfile->site = '';
        $userProfile->bio = '';
        $userProfile->interest = '';
        $userProfile->workerid = '';

        $userProfile->save();

        //获取产品分类用于兴趣类别
        $category_model = new ShoppingCategory();
        $category = $category_model->category(['enabled' => 1, 'parentid' => 0]);

        return $this->render('interest', [
            'category' => $category
        ]);
    }

    public function actionInterestSubmit()
    {
        $uid = Yii::$app->session['userinfo']['uid'];

        //获取兴趣id
        $hobby = explode(',', Yii::$app->request->get('hobby', ''));

        //保存到数据库
        foreach ($hobby as $cat_id) {
            $interest = new MembersInterest();
            $interest->uid = $uid;
            $interest->cateid = $cat_id;
            $interest->pcateid = 0;
            $interest->status = 0;
            $interest->createdt = time();
            $interest->save();
        }

        //跳转到首页
        $this->redirect('/home');
    }

    public function actionNickname()
    {
        return $this->render('nickname', [
        ]);
    }
}

//end file
