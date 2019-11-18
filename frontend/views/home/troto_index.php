<?php $this->title = Yii::$app->params['site_name']; ?>
<link rel="stylesheet" href="https://cdn.bootcss.com/Swiper/3.2.7/css/swiper.min.css">
<script src="https://cdn.bootcss.com/Swiper/3.2.7/js/swiper.min.js"></script>
<script src="https://cdn.bootcss.com/layzr.js/1.4.2/layzr.min.js"></script>
<style type="text/css">
.pro_price { font-size:12px; }
.pro_oriprice { float:right; }
</style>
<div class="row">
    <div class="col-xs-12 searchbox">
        <div class="" style="position:relative;">
            <input type="text" class="w100" name="search" placeholder="搜索商品名称">
            <span class="glyphicon glyphicon-search searchicon"></span>
        </div>          
    </div>
    <!--轮播图尺寸900*300-->
    <?php if($slides): ?>
    <div class="col-xs-12 clearPadding index">
        <div class="swiper-container">
            <div class="swiper-wrapper">
            <?php foreach($slides as $slideKey=>$slideItem): ?>
                <div class="swiper-slide"><a href="<?=$slideItem['link']?>"><img src="<?=$slideItem['thumb']?>" class="w100"></a></div>
            <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>          
    </div>
    <?php endif;?>

    <div class="col-xs-12 bgf1" style="padding:0; padding-top:.5rem">
        <img src="/images/merchants-join.png" alt="" class="img-thumbnail" >
    </div>

    <!--活动专区-->
    <div class="col-xs-12 clearPadding bgf1">
        <div class="col-title mtb15 clearfix">
            <span class="font16">最新胎型</span>
            <!-- &nbsp;&nbsp;<span class="font12">(每人每天限购一次)</span> -->
            <a href="/tyres" class="pull-right font12">全部产品 &gt;&gt;</a>
        </div>
        <div class="sale-content">
            <?php foreach ($newGoods as $goodsKey=>$goodsItem): ?>
            <div class="col-xs-4" style="padding:2px; margin-bottom:5px">
                <div class="hot_imgbox">
                    <a href="/goods/item/<?=$goodsItem['id']?>"><img data-layzr="<?=$goodsItem['thumb1']?:'/images/layzr.gif'?>" src="/images/layzr.gif" class="w100"></a>
                    <!-- <div class="countbox texthidden">倒计时：<span class="timecount"></span></div> -->
                </div>
                <div class="hot_detail pb5 bg-white plr5">
                    <div class="pt10b3 texthidden"><a href="/goods/item/<?=$goodsItem['id']?>"><?=$goodsItem['brand']?> <?=$categories[$goodsItem['pcate']]['name']?></a></div>
                    <div>
                        <span class="pro_price"><strong>￥<?=$goodsItem['marketprice']?></strong></span>
                        <span class="pro_oriprice">￥<?=$goodsItem['productprice']?></span>
                    </div>
                    <!-- <div class="font12 color82"><span>已售：999</span><span class="pull-right">库存：<?=$goodsItem['total']?></span></div> -->
                </div>
            </div>
            <?php endforeach; ?>
        </div>          
    </div>
    <!--热卖专区-->
    <div class="col-xs-12 clearPadding bgf1" style="padding-bottom:52px;">
        <div class="col-title mtb15">
            <span class="font16">热卖胎型</span>
            <a href="/tyres" class="pull-right font12">全部产品 &gt;&gt;</a>
        </div>
        <div class="hot-content">
            <?php foreach ($hotGoods as $goodsKey=>$goodsItem): ?>
            <div class="col-xs-4" style="padding:2px; margin-bottom:5px">
                <a href="/goods/item/<?=$goodsItem['id']?>"><img data-layzr="<?=$goodsItem['thumb1']?:'/images/layzr.gif'?>" src="" class="w100"></a>
                <div class="pro_detail">
                    <div class="pt10b3 texthidden"><a href="/goods/item/<?=$goodsItem['id']?>"><?=$goodsItem['brand']?> <?=$categories[$goodsItem['pcate']]['name']?></a></div>
                    <div>
                        <span class="pro_price"><strong>￥<?=$goodsItem['productprice']?></strong></span>
                        <span class="pro_oriprice pull-right">￥<?=$goodsItem['productprice']?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>

<?php include '../views/layouts/TrotoWxshare.php';?>

<script type="text/javascript">
/*延迟加载*/
var layzr = new Layzr({
    container: null,
    selector: '[data-layzr]',
    attr: 'data-layzr',
    retinaAttr: 'data-layzr-retina',
    bgAttr: 'data-layzr-bg',
    hiddenAttr: 'data-layzr-hidden',
    threshold: 0,
    callback: null
});
/*轮播图*/
var swiper = new Swiper('.swiper-container', {
    pagination: '.swiper-pagination',
    paginationClickable: true,
    autoplay: 2500,
    autoplayDisableOnInteraction: false,
    loop: true,
    autoHeight:true
});
/*倒计时*/
var intDiff = parseInt(6000000);//倒计时总秒数量
function timer(intDiff){
    window.setInterval(function(){
    var day=0,
        hour=0,
        minute=0,
        second=0;//时间默认值        
    if(intDiff > 0){
        day = Math.floor(intDiff / (60 * 60 * 24));
        hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
        minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
        second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
    }
    if (minute <= 9) minute = '0' + minute;
    if (second <= 9) second = '0' + second;
    $(".timecount").html(day+'天'+hour+' : '+minute+' : '+second);
    intDiff--;
    }, 1000);
} 
$(function(){
    // timer(intDiff);
}); 
</script>