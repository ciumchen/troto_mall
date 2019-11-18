<?php
use yii\helpers\Html;

$request = Yii::$app->request;
$route_name = $this->context->request->getPathInfo();
$categoryId = 21;
$this->title = '商品分类 - '.Yii::$app->params['site_name'];
?>
<style type="text/css">
.container { margin:0; padding:0 }
.blk { background: #fff; border-radius: 10px; margin: 5px 10px; height: 100%; padding: 10px; width: 95%; }
.list-group-item-text{ line-height: 1.5; }
.txtbold{ color: #3e5569; font-weight:bold; }
.txtbold span{ background-color: #7f7fdc;
    color: #fff;
    border-radius: 5px;
    padding: 2px 5px;
    margin-right: 3px;
    font-size: 12px;
}
.btnserve { border-radius: 15px;
    background-color: #5895c7;
    border-color: #73b0e5;
    line-height: .8;
    width: 78%;
    font-size: 12px;
}
.item-act {
    display: inline-block;
    float: right;
    color: #e5cfcf;
    font-size: samll;
    font-weight: bold;
    padding-bottom: .03rem;
}

aside {
    display: block;
}
.menu-left {
    background: #fff;
}
.menu-left, .menu-right {
    position: fixed;
    left: 0;
    top: .2rem;
    bottom: 0;
    overflow-y: scroll;
}
.menu-left ul li {
    padding: 1.15rem .12rem;
    box-sizing: border-box;
    font-size: 1.35rem;
    width: 9.6rem;
    text-align: center;
    font-weight: bold;
    border-bottom: 1px #efeaea solid;
}
.menu-left ul li.active {
    background:#f2f2f2;
    position: relative;
}
.menu-left ul li.active:before {
    content: " ";
    position: absolute;
    display: block;
    width: 2px;
    height: 100%;
    background: #00A0EA;
    top: 0;
    left: 0;
}
.menu-right {
    background:#f2f2f2;
    position: inherit;
    margin-left: 9.8rem;
    margin-top: .2rem;
    right: 0;
    bottom: 0;
    left: 9.8rem;
    padding: .3rem;
}
.blktop{
    position: fixed;
    z-index: 999;
    right: 0;
    left: 0;
}
.blkcontent{
    position: fixed;
    z-index: 999;
    top: 5.8rem;
    right: 0;
    left: 0;
}
.menu-right ul {
    overflow: hidden;
}
.menu-right ul li {
    text-align: center;
}
.menu-right ul li:nth-child(3n+1) a {
    left: 0;
    right: .7rem;
}
.menu-right ul li a {
    display: block;
    position: absolute;
    left: .3rem;
    top: .7rem;
    bottom: .7rem;
    right: .3rem;
}
.menu-right ul li img {
    width:8.5rem;
    height: auto;
}
.menu-right ul li span {
    line-height: 2.6rem;
    overflow: hidden;
}
.w-3 {
    width: 33.33%;
    float: left;
    padding: .6rem .4rem;
    box-sizing: border-box;
    position: relative;
}
.w-3:nth-child(3n+1) {
    padding-left: 0;
    padding-right: .8rem;
}
.content-items li {
    background-color: #fff;
    border-radius: 15px;
    margin-bottom: 5px;
}
.content-items li img{
    vertical-align: unset;
}
.list-group { margin-left: -10px; }
.menu-right ul li .lf{text-align: left;}
</style>
<div class="row" style="background-color:#f2f2f2; margin:0; padding:0">
    <aside>
        <div class="menu-left scrollbar-none" id="sidebar">
          <ul>
            <li class="active">全 部</li>
          <?php foreach ($pcate as $cateItem): ?>
            <li><?=$cateItem['name']?></li>
          <?php endforeach; ?>
          </ul>
        </div>
    </aside>

    <section class="menu-right blktop">
      <ul>
        <li class="w-3"><a href="#"></a> <img src="/images/brand_sanli.png"><!-- <span>三力</span> --></li>
        <li class="w-3"><a href="#"></a> <img src="/images/brand_kaili.png"><!-- <span>三力</span> --></li>
        <li class="w-3"><a href="#"></a> <img src="/images/brand_sanli.png"><!-- <span>速通</span> --></li>
        <li class="w-3"><a href="#"></a> <img src="/images/brand_sanli.png"><!-- <span>速通</span> --></li>
        <li class="w-3"><a href="#"></a> <img src="/images/brand_sanli.png"><!-- <span>速通</span> --></li>
      </ul>
    </section>

    <section class="menu-right blkcontent" style="padding-bottom:50px; background:#f2f2f2;">
      <ul class="content-items">
<?php for ($i=0; $i<5; $i++):?>
        <li>
            <div class="row" style="width: 98%; margin: 5px;">
              <div class="row mtb5">
                <div class="col-xs-4"><a href="/goods/item/" style="position: inherit;"><img src="/images/logo.png" class="img-thumbnail"></a></div>
                <div class="col-xs-8">
                  <div class="row">
                    <div class="col-xs-12" style="padding: 0px;">
                      <ul class="list-group" style="margin-bottom:5px;">
                        <li class="list-group-item-text txtbold"><a href="/goods/item/" style="position: inherit;">三力轮胎 19R20P 特惠</a></li>
                        <li class="list-group-item-text txtbold lf"><span>花纹：L819r</span><span>规格：9R20 16pr</span></li>
                        <li class="list-group-item-text lf" style="padding-right:15px; font-size:12px;"><img src="/images/icon_loc.png" style="height:12px; width:14px"> 深圳 南山区前海壹号企业公园 距离5.2KM</li>
                      </ul>
                    </div>
                  </div>
                </div>
                    <div class="col-xs-6 txtbold">￥8946.00</div>
                    <div class="col-xs-6">库存：3</div>
              </div>
          </div>
        </li>
<?php endfor; ?>
      </ul>
    </section>

    <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>
<script type="text/javascript">
function stopDrop() {
    var lastY;//最后一次y坐标点
    $(document.body).on('touchstart', function(event) {
        lastY = event.originalEvent.changedTouches[0].clientY;//点击屏幕时记录最后一次Y度坐标。
    });
 
    $(document.body).on('touchmove', function(event) {
        var y = event.originalEvent.changedTouches[0].clientY;
        var st = $(this).scrollTop(); //滚动条高度  
        if (y >= lastY && st <= 10) {//如果滚动条高度小于0，可以理解为到顶了，且是下拉情况下，阻止touchmove事件。
            lastY = y;
            event.preventDefault();
        }
        lastY = y;
    });
}
// stopDrop();


function loadataProduct() {
    //浏览器的高度加上滚动条的高度
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
    var tite2 = $('.tite2').text();
    //当文档的高度小于或者等于总的高度的时候，开始动态加载数据
    if ($(document).height() <= totalheight) { 
        $.ajaxSetup({
            async : false //这是重要的一步，防止重复提交的  
        });
        //var page = $('.limit').val();
        var num = '';
        for(var i=1;i<$('.waress li').length;i++){ num = i; }
        var page = Math.ceil((num+1) / 8);
        var typeid = $('.cate #typeid').val();
        $.get("/goods/cate-data", {id: <?=$categoryId?>,page: page},
            function (result) {
                var s = result.join("");
                if(s=='false'){
                   $(".list-load").html("没有更多");return;
                }
                $(".waress").append(s);
                $(".list-load").css('display','block');
                $(".list-load").html("<img src='/images/loading.gif' style='width: 20px;'>");
            }, 'json');
    }
}

$(function(){
    //默认第一屏
    $.get("/goods/cate-data", {id: <?=$categoryId?>}, function (result) {
        if(result=='false'){
           $(".no").html("<div class='tite2'  ><span>暂无产品</span></div>");return;
        }else{
            var s = result.join("");
            $('.waress').html(s);
        }
    }, 'json');

    $("#form1").click(function () {
       window.location.href="/goods/q?wd="+$('#realname').val();
    });
});

//滚动加载
$(window).scroll( function() { loadataProduct();});
</script>

<?php include '../views/layouts/TrotoWxshare.php';?>
