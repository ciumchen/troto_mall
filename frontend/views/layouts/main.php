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
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?= Yii::$app->params['site_keywords'] ?>"/>
    <meta name="description" content="<?= Yii::$app->params['site_descript']?>"/>
    <meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1; user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="msapplication-tap-highlight" content="no">
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <meta http-equiv="x-dns-prefetch-control" content="on"/>
    <link rel="dns-prefetch" href="http://assets.troto.com.cn"/>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/personal.css?v=160112">
    <link rel="stylesheet" href="/css/demo-files/demo.css">
    <link rel="stylesheet" href="/css/swiper.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/gender.css">
    <?php if($route_name=='goods/cate' || $route_name=='goods/q'):?><link href="/css/cate.css" rel="stylesheet"><?php endif;?>
    <link rel="stylesheet" href="/js/tip-yellowsimple/tip-yellowsimple.css" type="text/css"/>
    <?php if($route_name=='home/index'):?><script type="text/javascript" src="/js/mediaelement-and-player.min.js"></script><?php endif;?>
    <script type="text/javascript" src="http://libs.baidu.com/jquery/1.7.2/jquery.min.js"></script>
    <script type="text/javascript" src="http://libs.baidu.com/jquery.cookie/1.4.1/jquery.cookie.min.js"></script>
    <script type="text/javascript" src="/js/jquery.poshytip.js"></script>
    <script type='text/javascript' src='/js/jq.validate.js'></script>
    <script type='text/javascript' src='/js/daojishi.js'></script>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <?php if(!in_array($this->context->id, ['home','goods'])){echo '<script type="text/javascript" src="/js/area.js">_init_area();</script>';}?>
</head>
<?php
if(!in_array($this->context->id, ['home','goods'])){
    echo '<body onload="setup()">';
}else {
    echo '<body>';
}?>
<?php $this->beginBody() ?>

<?= $content ?>

<?php
$notDisplayNavbarUrl = ['goods/detail', 'member/invite'];
if (!in_array($route_name, $notDisplayNavbarUrl)) {
    include '../views/bottomNavBar.php';
}
?>
<?php $this->endBody() ?>
</body>
<div style="display:none"><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1260225307'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/stat.php%3Fid%3D1260225307' type='text/javascript'%3E%3C/script%3E"));</script></div>
</html>