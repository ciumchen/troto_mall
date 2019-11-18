<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\Users;
use frontend\models\WeChat;
use frontend\models\SiteArticle;

use common\components\SMSComponents;
/**
 * Site controller
 */
class SiteController extends Controller {
    public function init() {
        parent::init();
        $this->layout = 'TrotoMain';
        Yii::$app->session->open();
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * 终端更新
     */
    public function actionCabinetApk() {
        $this->enableCsrfValidation = false;
        Yii::$app->response->format=Response::FORMAT_JSON;
        return [
            'code'=>2000,
            'version'=>'1.0.1',
            'data'=>'http://assets.troto.com.cn/app-release.apk'
        ];
    }

    /**
     * 关于我们
     */
    public function actionAboutUs() {
        return $this->render('about-us');
    }

    /**
     * 联系客服
     */
    public function actionServiceCustomer() {
        return $this->render('service-customer');
    }

    /**
     * 服务政策
     */
    public function actionServicePolicy() {
        return $this->render('service-policy');
    }


    /**
     * 定义404页面
     */
    public function actionError() {
        return $this->render('error', []);
    }

    public function actionStartBusiness() {
        $this->layout = 'blank';
        return $this->render('start-business', [
        ]);
    }

    /**
     * 站点文章
     */
    public function actionArticle($id) {
        $this->layout = 'blank'; //干掉底部菜单
        $articleId = intval($id);
        $article = SiteArticle::find()->where(['id'=>$articleId])->asArray()->one();

        if (empty($article)) {
            $article['title']   = '未找到您要查看的内容！';
            $article['description'] = $article['content'] = $article['title'];
            $article['createtime'] = time();
        }

        $WeChat = new WeChat();
        $signPackage = $WeChat->getSignPackage();

        return $this->render('article', [
            'article'     => $article,
            'signPackage' => $signPackage,
        ]);
    }



    /**
     * Logs in a user.
     * @return mixed
     */
    public function actionLogin($url = null) {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $this->login()) {
            if ($url === null) {
                return $this->goBack();
            } else {
                $this->redirect($url);
            }
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }
    private function login() {
        $post = Yii::$app->request->post()['LoginForm'];

        $user = Users::find()
            ->where(['username' => $post['username'], 'password' => $post['password']])
            ->one();

        if ($user !== null) {
            Yii::$app->session['userinfo'] = ['uid' => $user->uid, 'openid' => ''];
        }

        return ($user === null ? false : true);
    }

    /**
     * Logs out the current user.
     * @return mixed
     */
    public function actionLogout() {
        //Yii::$app->user->logout();
        unset(Yii::$app->session['userinfo']);
        $this->redirect('/site/login');
    }

    /**
     * Signs user up.
     * @return mixed
     */
    public function actionSignup() {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     * @return mixed
     */
    public function actionRequestPasswordReset() {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token) {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}
