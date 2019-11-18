<?php $this->title = Yii::$app->params['site_name']; ?>

<a name="top"></a>
<!-- banner -->
<?php if (!empty($slides)): ?>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php foreach ($slides as $v): ?>
                <div class="swiper-slide"><img src="<?php echo $v['thumb']; ?>" onclick="window.location.href='<?php echo $v['link'] ?>'"></div>
            <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="search">
            <div class="search-border">
                <form action="/goods/q" method="get" id="query-form">
                    <input type="text" id="keywords" name="wd" placeholder="搜索货品"/>
                    <span class="icon-search" onclick="$('#query-form').submit();"></span>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- 搜索栏 -->

<!-- 限时抢购 -->
<!-- <div class="panic-box">
    <a href="/flash"><img src="/images/panic.jpg"></a>
    <ul>
        <li><span class="icon-tenfifteen" style="color:#ce7071;"></span>十点一刻限时秒杀活动</li>
        <li><a href="/flash"><span class="icon-more2"></span>点击进入</a></li>
    </ul>
</div> -->

<!-- 隔离条 -->
<div id="quarantine2" style="margin-top: -2px;">
    <span></span>
</div>

<!-- 专题list -->
<?php foreach ($topicList as $topic) { ?>
    <div class="panic-box">
        <a href="<?php echo $topic['isjump'] ? $topic['link'] : '/goods/topic?id=' . $topic['topicid']; ?>"><img src="<?php echo $topic['thumb']; ?>"></a>
        <p style="bottom: 10px;"><a href="">点击了解 <span class="icon-more2"></span></a></p>
    </div>
    <!-- 隔离条 -->
    <div id="quarantine2" style="margin-top: -2px;">
        <span></span>
    </div>
<?php } ?>

<!-- 视频 -->
<div style="width:100%; ">
    <video class="mejs-wmp" width="100%"
           src="http://7xritt.media1.z0.glb.clouddn.com/happyfit2_f.mp4" type="video/mp4"
           id="player1" poster="/images/1.jpg"
           controls="controls" preload="none"></video>
</div>
<!--
<div class="streamer">
    <span>热卖产品</span>
</div>
-->
<div class="content-box">
    <div class="comm01 nav-list" style="display:block;">
        <ul class="wares"></ul>
    </div>
    <i class='no'>
        <div class="tite2"><span>没有更多</span></div>
    </i>
</div>

<script>
    function loadataProduct() {
        totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());     //浏览器的高度加上滚动条的高度
        var tite2 = $('.tite2').text();

        //当文档的高度小于或者等于总的高度的时候，开始动态加载数据
        if ($(document).height() <= totalheight) {
            var limit = $('#limit').val();
            var num = '';

            for (var i = 1; i < $('.wares li').length; i++) {
                num = i;
            }
			$.ajaxSetup({  
				async : false //这是重要的一步，防止重复提交的  
			})
            var page = Math.ceil((num + 1) / 8);

            $.get("/home/isrecommand", {page: page}, function (result) {
                var s = result.join("");
                if (s == 'false') {
                    $(".no").html("<div class='tite2'  ><span>没有更多</span></div>");
                    return;
                }
                $(".wares").append(s);
                $(".no").html("<div class='tite2' ><span><img src='/images/loading.gif' style='width: 20px;'></span></div>");
            }, 'json');
        }
    }

    $(window).scroll(function () {
        loadataProduct();
    });

    $(function () {
        $.get("/home/isrecommand", {}, function (result) {
            var s = result.join("");
            $('.wares').append(s);
        }, 'json');
    });
</script>

<script>
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
        title: '十点一刻海淘生活馆', // 分享标题
        link: 'http://mall.troto.com.cn/?uid=<?=$uid?>', // 分享链接
        imgUrl: 'http://mall.troto.com.cn/images/logo_64px.jpg', // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: '十点一刻海淘生活馆', // 分享标题
        desc: '十点一刻海淘生活馆主营母婴、保健、化妆、轻奢等商品。', // 分享描述
        link: 'http://mall.troto.com.cn/?uid=<?=$uid?>', // 分享链接
        imgUrl: 'http://mall.troto.com.cn/images/logo_64px.jpg', // 分享图标
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

<!--返回顶部开始-->
<script src="/js/scrollTop.js"></script>
<a id="rtt"></a>

<script src="/js/commonality.js"></script>

<script type="text/javascript">
// 选项卡
$(function () {
    $("#blush li").click(function () {
        $(this).addClass("tabin").siblings().removeClass("tabin");
    });
});
</script>

<!-- Swiper JS -->
<script src="/js/swiper.min.js"></script>
<!-- 幻灯片js -->
<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        paginationClickable: true,
        spaceBetween: 60,
        centeredSlides: true,
        autoplay: 5000,
        autoplayDisableOnInteraction: false
    });

    $('audio,video').mediaelementplayer({
        success: function (player, node) {
            $('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        }
    });
</script>