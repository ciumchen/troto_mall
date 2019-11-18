<?php $this->title = Yii::$app->params['site_name']; ?>
<link rel="stylesheet" type="text/css" href="/v3/css/index.css">
<script type="text/javascript" src="/v3/js/jquery.event.drag-1.5.min.js"></script>
<script type="text/javascript" src="/v3/js/jquery.touchSlider.js"></script>
<script type="text/javascript">
function submitSearch() { document.searchGoods.submit(); }
</script>

<?php if (!empty($slides)): ?>
<style type="text/css">
.main_image {width:100%;min-height:185px; border-top:1px solid #d7d7d7; overflow:hidden; margin:0 auto; position:relative}
.main_image ul {width:9999px;min-height:185px; overflow:hidden; position:absolute; top:0; left:0}
.main_image li {float:left; width:100%;min-height:185px;}
.main_image li span {display:block; width:100%; height:240px}
.main_image li a {display:block; width:100%; height:240px}
<?php foreach ($slides as $slideKey=>$slideItem): ?>
.main_image li .img_<?php echo $slideKey+1;?> {background: url('<?=$slideItem['thumb']?>') center top no-repeat;background-size: 100%;}
<?php endforeach; ?>
#btn_prev,#btn_next{z-index:11111;position:absolute;display:block;width:73px!important;height:74px!important;top:50%;margin-top:-37px;display:none;}
</style>
    <!-- slider -->
    <div class="main_visual">
        <div class="main_image">
            <ul>
            <?php foreach ($slides as $slideKey=>$slideItem): ?>
                <li><a href="<?=$slideItem['link']?>"><span class="img_<?php echo $slideKey+1;?>"></span></a></li>
            <?php endforeach; ?>
            </ul>
            <a href="##" id="btn_prev"></a>
            <a href="##" id="btn_next"></a>
        </div>
    </div>
<script type="text/javascript">
$(document).ready(function () {
    $dragBln = false;
    $(".main_image").touchSlider({
        flexible : true,
        speed : 600,
        btn_prev : $("#btn_prev"),
        btn_next : $("#btn_next"),
        paging : $(".flicking_con a"),
        counter : function (e) {
            $(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
        }
    });
    $(".main_image").bind("mousedown", function() {
        $dragBln = false;
    })
    $(".main_image").bind("dragstart", function() {
        $dragBln = true;
    })
    $(".main_image a").click(function() {
        if($dragBln) {
            return false;
        }
    })
    timer = setInterval(function() { $("#btn_next").click();}, 3000);
    $(".main_visual").hover(function() {
        clearInterval(timer);
    }, function() {
        timer = setInterval(function() { $("#btn_next").click();}, 3000);
    })
    $(".main_image").bind("touchstart", function() {
        clearInterval(timer);
    }).bind("touchend", function() {
        timer = setInterval(function() { $("#btn_next").click();}, 3000);
    })
});
</script>
<?php endif; ?>

<div class="search">
    <form action="/goods/q" method="get">
        <input type="text" name="wd" placeholder="搜索您需要的商品">
        <button onclick="searchGoods();"></button>
    </form>
    <div class="clear"></div>
</div>
<div class="clear"></div>

<div class="choice">
    <ul>
        <li><a href="/goods/cate"><img src="http://cdn.10d15.com/images/cate-icons/cate.png"><p>全部</p></a>
    <?php foreach ($pcateList as $cateItem): ?>
    <?php if ($cateItem['isrecommand']): ?>
        <li><a href="/goods/cate?id=<?=$cateItem['id']?>"><img src="<?=$cateItem['thumb']?>"><p><?=$cateItem['name']?></p></a></li></li>
    <?php endif; ?>
    <?php endforeach; ?>
        <div class="clear"></div>
    </ul>
</div>

<div class="pic">
    <a href="/flash"><img class="pic1" src="http://cdn.10d15.com/images/b-xsg.png"></a>
    <div class="pic-right">
        <a href="/goods/new"><img src="http://cdn.10d15.com/images/b-xpss.png"></a>
        <a href="/goods/hot"><img src="http://cdn.10d15.com/images/b-rqbk.png"></a>
    </div>
</div>
<div class="Special">
    <div class="special-left">
        <a href="/goods/bargain">
            <h3>天天特价</h3><p>Special Price Every Day</p>
        </a>
    </div>
    <div class="special-right">
        <a href="/goods/must">
            <h3>必买清单</h3><p>Must Have List</p>
        </a>
    </div>
</div>
<div class="product">
    <h2>懂品质&nbsp · &nbsp更懂您</h2>
    <h5>品质生活与您相伴，十点一刻</h5>
    <ul>
<!--
<?php /*****此处注释的html为模板样式，预留排错用******/ ?>
         <li>
            <div class="commodity">
            </div>
            <div class="commodity-text">
                <h3>儿童玩具]日本 TATSUMIYA 水中孵
                    化恐龙蛋</h3>
                <p>麻烦大家帮忙测试一下税金问题，无需支付，只需要跳转支付页面，上传微信支付页面截图即可</p>
                <div class="purchase">
                <spam class="red-text">￥39.00</spam><span style="text-decoration: line-through">￥20.00</span>
                <input type="button" value="立即抢购" >
                    <div class="clear"></div>
                </div>
            </div>
        </li>
-->
    <?php foreach ($recommendGoods as $goods): ?>
        <li>
            <div class="commodity"><a href="/goods/detail?id=<?=$goods['id']?>"><img src="<?=$goods['thumb']?>"></a></div>
            <div class="commodity-text">
                <h3><a href="/goods/detail?id=<?=$goods['id']?>"><?=$goods['title']?></a></h3>
                <p><?=$goods['fdesc']?></p>
                <div class="purchase">
                    <spam class="red-text">￥<?=$goods['marketprice']?></spam><span style="text-decoration: line-through">￥<?=$goods['originalprice']?></span>
                    <input type="button" value="立即抢购" onclick="window.location.href='/goods/detail?id=<?=$goods['id']?>'">
                    <div class="clear"></div>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
    </ul>
</div>

<?php include '../views/layouts/wxshare.php';?>