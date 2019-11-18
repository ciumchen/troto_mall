<?php
use yii\helpers\Html;
$this->title = '邀请好友加入十点一刻';

$shareInfo = [];
$shareInfo['title'] = '十点一刻，深圳人都做了啥？';
$shareInfo['desc'] = '你的好友正在淘尽全球好货，你也来看看吧！';
$shareInfo['thumb'] = 'http://mall.troto.com.cn/images/logo_128px.jpg';
$shareInfo['link'] = 'http://mall.troto.com.cn/home/index?uid='.$userInfo['uid'];
?>

<!--分享二维码-->
<div class="two-dimension">
    <ul>
        <li><img src="<?=$userInfo['avatar']?>"></li>
        <li><span class="icon-plus"></span></li>
        <li><img src="/images/head-portrait02.png"></li>
    </ul>
    <div class="dimension">
        <span class="dime01"></span>
        <p>一见钟情，再见倾心 <br>我们满满的真诚，我们拒绝套路 <br>我在十点一刻等你来</p>
        <span class="dime02"></span>
    </div>
</div>

<div class="share-ewm">
    <img src="/member/invite-qr-code"/>
    <a href="">扫一扫二维码，加入我的十点一刻</a>
</div>

<script type="text/javascript">
wx.config({
    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
    appId: '<?=$signPackage['appId']?>', // 必填，公众号的唯一标识
    timestamp: <?=$signPackage['timestamp']?>, // 必填，生成签名的时间戳
    nonceStr: '<?=$signPackage['nonceStr']?>', // 必填，生成签名的随机串
    signature: '<?=$signPackage['signature']?>',//   必填，签名，见附录1
    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
});

wx.ready(function () {
    wx.onMenuShareTimeline({
        title: '<?=$shareInfo['title']?>',
        link: '<?=$shareInfo['link']?>',
        imgUrl: '<?=$shareInfo['thumb']?>',
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: '<?=$shareInfo['title']?>',
        desc: '<?=$shareInfo['desc']?>',
        link: '<?=$shareInfo['link']?>',
        imgUrl: '<?=$shareInfo['thumb']?>',
        type: '', // 分享类型,music、video或link，不填默认为link
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
