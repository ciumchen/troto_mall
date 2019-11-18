<?php
//处理分享信息
$_GET['uid'] = $uid;  //直接指定当前用户为分享用户
$domain   = Yii::$app->request->getHostInfo().'/';
$baseUrl  = $domain.Yii::$app->request->getpathinfo();
$shareUrl = $baseUrl.'?'.http_build_query($_GET);
$wxshare['title'] = isset($this->title)?$this->title:Yii::$app->params['site_name'];
$wxshare['thumb'] = isset($wxshare['thumb']) ? $wxshare['thumb'] : $domain.'images/logo_64px.jpg';
$wxshare['desc']  = isset($wxshare['desc']) ? $wxshare['desc'] : '十点一刻海淘生活馆主营母婴、保健、化妆、轻奢等商品。';
$wxshare['link']  = $baseUrl.'?'.http_build_query($_GET);
?>

<script type="text/javascript">
wx.config({
    debug: false,
    appId: '<?=$signPackage['appId']?>',
    timestamp: <?=$signPackage['timestamp']?>,
    nonceStr: '<?=$signPackage['nonceStr']?>',
    signature: '<?=$signPackage['signature']?>',
    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage']
});
wx.ready(function () {
    wx.onMenuShareTimeline({
        title: '<?=$wxshare['title']?>',
        link: '<?=$wxshare['link']?>',
        imgUrl: '<?=$wxshare['thumb']?>?imageslim',
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: '<?=$wxshare['title']?>',
        desc: '<?=$wxshare['desc']?>',
        link: '<?=$wxshare['link']?>',
        imgUrl: '<?=$wxshare['thumb']?>?imageslim',
        type: '', // 分享类型,music、video或link，默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
});
</script>
