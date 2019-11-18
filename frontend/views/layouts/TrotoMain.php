<?php
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;

AppAsset::register($this);

$route_name = Yii::$app->request->getPathInfo();
?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <?= Html::csrfMetaTags() ?>
  <?php $this->head() ?>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <meta name="keywords" content="<?= Yii::$app->params['site_keywords'] ?>"/>
  <meta name="description" content="<?= Yii::$app->params['site_descript']?>"/>
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
  <meta content="telephone=no" name="format-detection"/>
  <meta http-equiv="pragma" content="no-cache"/>
  <meta name="msapplication-tap-highlight" content="no">
  <meta name="visit-user-ip" content="<?=Yii::$app->request->userIP?>">
  <title><?= Html::encode($this->title) ?></title>
  <link rel="shortcut icon" href="/favicon.ico" />
  <meta http-equiv="x-dns-prefetch-control" content="on"/>
  <link rel="dns-prefetch" href=""/>
  <link rel="stylesheet" href="https://cdn.bootcss.com/twitter-bootstrap/3.3.2/css/bootstrap.min.css">
  <link href="/styles/common.css" rel="stylesheet">
  <link href="/fonts/fonts.css" rel="stylesheet">
  <script src="https://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
  <script type="text/javascript" src="http://libs.baidu.com/jquery.cookie/1.4.1/jquery.cookie.min.js"></script>
  <script src="https://cdn.bootcss.com/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="//res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
</head>
<body>
<?php $this->beginBody() ?>
<div class="container"><?= $content ?></div>
<?php $this->endBody() ?>
</body>
<!-- STAT_ANALYSIS_CODE -->
<div style="display:none"></div>
<?php if(in_array(Yii::$app->request->userIP, Yii::$app->params['debugIP'])):?>
<script src="https://cdn.bootcss.com/vConsole/3.3.4/vconsole.min.js"></script>
<script>var vConsole = new VConsole();</script>
<?php endif; ?>
</html>