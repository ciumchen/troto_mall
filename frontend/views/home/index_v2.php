<?php $this->title = Yii::$app->params['site_name']; ?>

<link rel="stylesheet" type="text/css" href="/css/newest-style.css">
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
        <!-- 搜索栏 -->
        <div class="search">
            <div class="search-border">
                <form action="/goods/q" method="get" id="query-form">
                    <input type="text" id="keywords" name="wd" placeholder="搜索感兴趣的商品关键词"/>
                    <span class="icon-search" onclick="$('#query-form').submit();"></span>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="service-lan">
    <ul>
        <li><span></span><h3>官方授权</h3></li>
        <li><span></span><h3>海关监管</h3></li>
        <li><span></span><h3>正品保障</h3></li>
        <li><span></span><h3>售后无忧</h3></li>
    </ul>
</div>

<!--  优惠券   -->
<!--
<div class="discount-coupon">
    <ul>
        <li>
            <div class="discount-left">10</div>
            <div class="discount-right">
                <p>首单立减券</p>
                <button class="btn-get-coupon" data-typeid="8">立领取即</button>
            </div>
        </li>
        <li>
            <div class="discount-left">20</div>
            <div class="discount-right">
                <p>满199元立减</p>
                <button class="btn-get-coupon" data-typeid="3">立领取即</button>
            </div>
        </li>
        <li>
            <div class="discount-left">50</div>
            <div class="discount-right">
                <p>满499元立减</p>
                <button class="btn-get-coupon" data-typeid="4">立领取即</button>
            </div>
        </li>
    </ul>
    <a href="/site/article?id=1">玩转十点一刻优惠券攻略秘籍</a>
</div>
-->
<!-- 商品推荐 -->
<?php if (isset($recommendGoods[0]['thumb2'])) :?>
<div class="recom">
    <div class="recom-left">
        <a href="/goods/detail?id=<?=$recommendGoods[0]['id']?>"><img src="<?=$recommendGoods[0]['thumb2']?>"></a>
    </div>
    <div class="recom-right">
        <div class="recom-right-top"><a href="/goods/detail?id=<?=$recommendGoods[1]['id']?>"><img src="<?=$recommendGoods[1]['thumbtheme']?>"></a></div>
        <ul>
            <li><a href="/goods/detail?id=<?=$recommendGoods[2]['id']?>"><img src="<?=$recommendGoods[2]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[3]['id']?>"><img src="<?=$recommendGoods[3]['thumb']?>"></a></li>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php if ($topicList): ?>
<!-- topic list -->
<div class="expand">
<?php foreach ($topicList as $topicItem): ?>
    <?php if (trim($topicItem['link'])!=''):?>
    <a href="<?=$topicItem['link']?>"><img src="<?=$topicItem['thumb']?>"></a>
    <?php else:?>
    <a href="/goods/topic?id=<?=$topicItem['topicid']?>"><img src="<?=$topicItem['thumb']?>"></a>
    <?php endif;?>
<?php endforeach;?>
</div>
<?php endif; ?>

<!-- 商品 -->
<div class="topical-subject">
<?php if (isset($recommendGoods[4])):?>
    <div class="topical-content">
        <div class="topical-title">
            <h2><?=$recommendGoods[4]['cname']?></h2>
            <p><?=$recommendGoods[4]['cdesct']?></p>
        </div>
        <a href="/goods/cate?id=<?=$recommendGoods[4]['pcate']?>"><img src="<?=$recommendGoods[4]['cthumb']?>"></a>
        <ul>
            <li><a href="/goods/detail?id=<?=$recommendGoods[4]['id']?>"><img src="<?=$recommendGoods[4]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[5]['id']?>"><img src="<?=$recommendGoods[5]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[6]['id']?>"><img src="<?=$recommendGoods[6]['thumb']?>"></a></li>
        </ul>
        <div class="topical-introduce">
            <div class="presentation">
                <a href="/goods/detail?id=<?=$recommendGoods[7]['id']?>">
                    <img src="<?=$recommendGoods[7]['thumb']?>">
                </a>
                <span class="heart<?php echo $recommendGoods[7]['like'] ? ' heatbgc':'';?>" data-goodsid="<?=$recommendGoods[7]['id']?>"></span>
                <ul>
                    <?php if($recommendGoods[7]['brand']):?><li><h1><?=$recommendGoods[7]['brand']?></h1></li><?php endif;?>
                    <li><?=$recommendGoods[7]['title']?></li>
                    <li>￥<?=$recommendGoods[7]['marketprice']?><s>￥<?=$recommendGoods[7]['originalprice']?></s></li>
                    <?php if ($recommendGoods[7]['tags']):?>
                    <li>
                    <?php foreach ($recommendGoods[7]['tags'] as $tag):?><span><?=$tag?></span><?php endforeach;?>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
<?php endif;?>
<?php if (isset($recommendGoods[8])):?>
    <div class="topical-content">
        <div class="topical-title">
            <h2><?=$recommendGoods[8]['cname']?></h2>
            <p><?=$recommendGoods[8]['cdesct']?></p>
        </div>
        <a href="/goods/cate?id=<?=$recommendGoods[8]['pcate']?>"><img src="<?=$recommendGoods[8]['cthumb']?>"></a>
        <ul>
            <li><a href="/goods/detail?id=<?=$recommendGoods[8]['id']?>"><img src="<?=$recommendGoods[8]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[9]['id']?>"><img src="<?=$recommendGoods[9]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[10]['id']?>"><img src="<?=$recommendGoods[10]['thumb']?>"></a></li>
        </ul>
        <div class="topical-introduce">
            <div class="presentation">
                <a href="/goods/detail?id=<?=$recommendGoods[11]['id']?>">
                    <img src="<?=$recommendGoods[11]['thumb']?>">
                </a>
                <span class="heart<?php echo $recommendGoods[11]['like'] ? ' heatbgc':'';?>" data-goodsid="<?=$recommendGoods[11]['id']?>"></span>
                <ul>
                    <?php if($recommendGoods[11]['brand']):?><li><h1><?=$recommendGoods[11]['brand']?></h1></li><?php endif;?>
                    <li><?=$recommendGoods[11]['title']?></li>
                    <li>￥<?=$recommendGoods[11]['marketprice']?><s>￥<?=$recommendGoods[11]['originalprice']?></s></li>
                    <?php if ($recommendGoods[11]['tags']):?>
                    <li>
                    <?php foreach ($recommendGoods[11]['tags'] as $tag):?><span><?=$tag?></span><?php endforeach;?>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
<?php endif;?>
<?php if (isset($recommendGoods[12])):?>
    <div class="topical-content">
        <div class="topical-title">
            <h2><?=$recommendGoods[12]['cname']?></h2>
            <p><?=$recommendGoods[12]['cdesct']?></p>
        </div>
        <a href="/goods/cate?id=<?=$recommendGoods[12]['pcate']?>"><img src="<?=$recommendGoods[12]['cthumb']?>"></a>
        <ul>
            <li><a href="/goods/detail?id=<?=$recommendGoods[12]['id']?>"><img src="<?=$recommendGoods[12]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[13]['id']?>"><img src="<?=$recommendGoods[13]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[14]['id']?>"><img src="<?=$recommendGoods[14]['thumb']?>"></a></li>
        </ul>
        <div class="topical-introduce">
            <div class="presentation">
                <a href="/goods/detail?id=<?=$recommendGoods[15]['id']?>">
                    <img src="<?=$recommendGoods[15]['thumb']?>">
                </a>
                <span class="heart<?php echo $recommendGoods[15]['like'] ? ' heatbgc':'';?>" data-goodsid="<?=$recommendGoods[15]['id']?>"></span>
                <ul>
                    <?php if($recommendGoods[15]['brand']):?><li><h1><?=$recommendGoods[15]['brand']?></h1></li><?php endif;?>
                    <li><?=$recommendGoods[15]['title']?></li>
                    <li>￥<?=$recommendGoods[15]['marketprice']?><s>￥<?=$recommendGoods[15]['originalprice']?></s></li>
                    <?php if ($recommendGoods[15]['tags']):?>
                    <li>
                    <?php foreach ($recommendGoods[15]['tags'] as $tag):?><span><?=$tag?></span><?php endforeach;?>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
<?php endif;?>
<?php if (isset($recommendGoods[16])):?>
    <div class="topical-content">
        <div class="topical-title">
            <h2><?=$recommendGoods[16]['cname']?></h2>
            <p><?=$recommendGoods[16]['cdesct']?></p>
        </div>
        <a href="/goods/cate?id=<?=$recommendGoods[16]['pcate']?>"><img src="<?=$recommendGoods[16]['cthumb']?>"></a>
        <ul>
            <li><a href="/goods/detail?id=<?=$recommendGoods[16]['id']?>"><img src="<?=$recommendGoods[16]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[17]['id']?>"><img src="<?=$recommendGoods[17]['thumb']?>"></a></li>
            <li><a href="/goods/detail?id=<?=$recommendGoods[18]['id']?>"><img src="<?=$recommendGoods[18]['thumb']?>"></a></li>
        </ul>
        <div class="topical-introduce">
            <div class="presentation">
                <a href="/goods/detail?id=<?=$recommendGoods[19]['id']?>">
                    <img src="<?=$recommendGoods[19]['thumb']?>">
                </a>
                <span class="heart<?php echo $recommendGoods[19]['like'] ? ' heatbgc':'';?>" data-goodsid="<?=$recommendGoods[19]['id']?>"></span>
                <ul>
                    <?php if($recommendGoods[19]['brand']):?><li><h1><?=$recommendGoods[19]['brand']?></h1></li><?php endif;?>
                    <li><?=$recommendGoods[19]['title']?></li>
                    <li>￥<?=$recommendGoods[19]['marketprice']?><s>￥<?=$recommendGoods[19]['originalprice']?></s></li>
                    <?php if ($recommendGoods[19]['tags']):?>
                    <li>
                    <?php foreach ($recommendGoods[19]['tags'] as $tag):?><span><?=$tag?></span><?php endforeach;?>
                    </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </div>
<?php endif;?>
</div>

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

<!-- Swiper JS -->
<script src="/js/swiper.min.js"></script>
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
</script>

<script type="text/javascript">
$(function () {
    // 选项卡
    $("#blush li").click(function () {
        $(this).addClass("tabin").siblings().removeClass("tabin");
    });

    //领取优惠券
    $(".btn-get-coupon").click(function(){
      var typeid = $(this).attr("data-typeid");
      $.post("/coupon/get-coupon", {'typeid':typeid, '_csrf':'<?= Yii::$app->request->csrfToken ?>'}, function(data){
            alert(data.msg);
      }, 'json').error(function() { 
            alert("网络发生错误，请稍后再试！"); 
      });
      return false;
    });

    //收藏操作
    $(".heart").click(function(){
      var goodsid = $(this).attr("data-goodsid");
      var changeElement = $(this);
      $.get('/collect/like?id='+goodsid, function (result) {
        if (result.success) {
            changeElement.toggleClass("heatbgc");
        }
      }, "JSON");
    });
});
</script>
