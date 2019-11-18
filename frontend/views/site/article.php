<?php
/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = $article['title'].' - '.Yii::$app->params['site_name'];
?><!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?=$this->title?></title>
    <meta name="format-detection" content="telephone=no, address=no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="keywords" content="<?=Yii::$app->params['site_name']?>" />
    <meta name="description" content="<?=$this->title?>">
    <link rel="shortcut icon" href="/images/global/wechat.jpg" />
    <link rel="stylesheet" type="text/css" href="/css/site-article.css">
    <script src="http://libs.baidu.com/jquery/1.7.2/jquery.min.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</head>

<body>
<div class="container container-fill">
<div id="activity-detail">
<script type="text/javascript">
    $(function(){
        $('body, .container-fill').css({'min-height':$(window).height()+'px'});
    })
</script>
<div class="page-bizinfo">
    <div class="header">
        <h1 id="activity-name"><?=$article['title']?></h1>
        <span id="post-date">
            <span><?php echo date('Y-m-d', $article['createtime']);?>&nbsp;&nbsp;</span>
            <span><?=$article['author']?></span>
        </span>
    </div>
</div>
<div class="page-content">
    <div class="text">
        <?=$article['content']?>
    </div>
</div>
</div>
</div>
<div id="mbutton">
    <span class="" onclick="$('#mcover').show()"><i class="fa fa-share-alt"></i> 转发</span>
    <span class="" onclick="$('#mcover').show()"><i class="fa fa-group"></i> 分享</span>
</div>
<div id="mcover" onclick="$(this).hide()"><img src="/images/share-guide.png"></div>
<script type="text/javascript">
wx.config({
    debug: false,
    appId: '<?=$signPackage['appId']?>',
    timestamp: <?=$signPackage['timestamp']?>,
    nonceStr: '<?=$signPackage['nonceStr']?>',
    signature: '<?=$signPackage['signature']?>',
    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage','onMenuShareQQ']
});
wx.ready(function () {
    wx.onMenuShareTimeline({
        title: '十点一刻海淘生活馆', // 分享标题
        link: window.location.href,
        imgUrl: 'http://mall.troto.com.cn/images/logo_64px.jpg', // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        'title': "<?=$this->title?>",
        'desc': "<?=$article['description']?>",
        'link': window.location.href,
        'imgUrl':'http://mall.troto.com.cn/images/logo_32px.jpg',
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
});
</script>
</body>
<div style="display:none"><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1260225307'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/stat.php%3Fid%3D1260225307' type='text/javascript'%3E%3C/script%3E"));</script></div>
<script type="text/javascript">var _hmt = _hmt || [];(function (){var hm = document.createElement("script");hm.src = "//hm.baidu.com/hm.js?ceb80469f1a8e8f59f47f66baa99844f";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(hm, s);})();</script>
<script>
</html>