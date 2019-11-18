<?php $this->title = Yii::$app->params['site_name']; ?>
<link rel="stylesheet" href="https://cdn.bootcss.com/Swiper/3.2.7/css/swiper.min.css">
<script src="https://cdn.bootcss.com/Swiper/3.2.7/js/swiper.min.js"></script>
<script src="https://cdn.bootcss.com/layzr.js/1.4.2/layzr.min.js"></script>
<style type="text/css">
.blk {background: #fff; border-radius: 4px; margin: 3px; height: 100%; padding: 10px; width: 98%;}
.brand { text-align:center; border-radius:5px; color:#fff; background-color:#00a0ea; line-height:1.8; font-weight:bolder; margin:2px;}
.brand:after { background-color:#90c5dc;}
.tyre-item-price { font-size:14px; font-weight:bold; color:#fff; background-color:#e00; padding:1px 3px; border-radius:3px; }
.list-group-item-text { line-height:1.4; font-size:16px; color:#e00; padding: 2px 0px; }
.cabinet-tlt { line-height:1.8; }
.cabinet-tlt span{display: block; width:33.33333%; float:left; }
#cabinet-tyres { padding-top:5px; }
.shop-arithmetic { display:block; float:left; margin-top:2px; margin-left:5px; box-sizing:border-box; white-space:nowrap; height:100%; border:1px solid #e0e0e0; border-radius:5px; }
.shop-arithmetic .minus, .shop-arithmetic .plus { border-right: 1px solid #e0e0e0;}
.shop-arithmetic .num { width:32px; text-align:center; border:none; display:inline-block; height:100%; box-sizing:border-box; vertical-align:top; margin:0 -6px; }
.shop-arithmetic a { display:inline-block; width:23px; height:22px; line-height:22px; color:#fff; font-size:16px; font-weight:bolder; text-align:center; background:#00a2ea; }
 ul.cbg-param {border: 1px #ccc solid; border-radius:2px; padding:3px; font-size:12px; margin:5px 0px;}
 ul.cbg-param li{border-bottom: 1px #ceceee dotted;}
 ul.cbg-param li:last-of-type{ border:none; }
 ul.cbg-param li span:first-child{display: inline-block; width:38% }
 #cfm-buy {display:none; font-size:12px; width:58px; background:rgba(0,0,0,0.5); border: 1px #a585e0 solid; position:fixed; text-align:center; bottom:60px; left:20px; border-radius: 50% / 52%; height:48px; padding-top:18px;}
 #cfm-buy span {display:block; position:absolute; top:5px; left:22px;}
</style>
<div class="row">
    <div class="col-xs-12 clearPadding bgf1" style="background-color:#fff; padding:5px; ">
        <div class="col-xs-2" style="padding:5px 1px">
            <div class="hot_imgbox"><img src="<?=$cabinetInfo['thumb']?>" class="w100" style="border-radius:5px;"></div>
        </div>
        <div class="col-xs-10" style="padding:2px; margin-bottom:5px">
            <h4 style="margin:3px 0px;"><strong><?=$cabinetInfo['name']?></strong></h4>
            <input type="hidden" name="<?=$cabinetInfo['cabinetid']?>">
            <div class="cabinet-tlt"  style="font-size:12px;">
                <span>库存: <?=$cabinetInfo['stock']?></span>
                <span>编号: <?=$cabinetInfo['sn']?></span>
                <span>距您: <?=$cabinetInfo['distance']?></span>
            </div>
            <div style="font-size:14px;">地址：<?=$cabinetInfo['address']?></div>
        </div>
    </div>

    <div id="cabinet-tyres" class="col-xs-12 clearPadding bgf1">
    <?php foreach ($cabinetGoods as $goodsItem): ?>
        <div class="col-xs-12 blk">
            <div class="row">
              <div class="col-xs-3" style="padding-right:0px;"><a href="##"><img src="<?=$goodsItem['thumb1']?>" class="img-thumbnail"></a></div>
              <div class="col-xs-9">
                <div class="row">
                  <div class="col-xs-12" style="padding: 0px 10px;">
                    <h5 class="tyre-item" data-title="<?=$goodsItem['title']?>" style="margin:0px 2px 5px; font-weight:bold;"><?=$goodsItem['brand']?> <?=$goodsItem['title']?></h5>
                    <div style="padding:0px;">
                    <?php if($goodsItem['params']): ?>
                        <ul class="cbg-param">
                        <?php foreach ($goodsItem['params'] as $param): ?>
                          <li><span><?=$param['title']?></span><span><?=$param['value']?></span></li>
                        <?php endforeach; ?>
                          <li><span>适用场景</span><span>国道标载/国道超载/高速长途</span></li>
                        </ul>
                    <?php endif; ?>
                    </div>
                    <ul class="list-group" style="margin-bottom:0px; width:68%; display:block; float:left;">
                      <li class="list-group-item-text txtbold"><span class="tyre-item-price">自提价</span> ￥<?=$goodsItem['price']?></li>
                      <li class="list-group-item-text txtbold"><span class="tyre-item-price">安装价</span> ￥<?=number_format($goodsItem['price']+30, 2, ".", "")?></li>
                    </ul>
                    <div class="shop-arithmetic">
                        <a href="javascript:;" class="minus">-</a>
                        <span class="num" id="c-<?=$goodsItem['pathway']?>-0" data-maxbuy="<?=$goodsItem['stock']?>" data-type="0" data-cabinetid="<?=$goodsItem['cabinetid']?>" data-goodsid="<?=$goodsItem['goodsid']?>" data-pathway="<?=$goodsItem['pathway']?>">0</span>
                        <a href="javascript:;" class="plus">+</a>
                    </div>
                    <div class="shop-arithmetic">
                        <a href="javascript:;" class="minus">-</a>
                        <span class="num" id="c-<?=$goodsItem['pathway']?>-1" data-maxbuy="<?=$goodsItem['stock']?>" data-type="1" data-cabinetid="<?=$goodsItem['cabinetid']?>" data-goodsid="<?=$goodsItem['goodsid']?>" data-pathway="<?=$goodsItem['pathway']?>">0</span>
                        <a href="javascript:;" class="plus">+</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

    <div id="cfm-buy"><a href="/order/confirm" style="color:#fff;"><span>5</span>确认订单</a></div>
    <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>

<?php include '../views/layouts/TrotoWxshare.php';?>

<script>
$(function(){
    var _csrf = $('meta[name="csrf-token"]').attr("content");
    $(".blk:last").css("margin-bottom","58px");

    //read cabinet cart goods for render
    if ($.cookie('cabinet_cart')==undefined) {
      $.cookie('cabinet_cart', JSON.stringify(cabinetCart), {expires:1});
    } else {
      cabinetCart = JSON.parse($.cookie('cabinet_cart'));
      //重置购物车数据为当前柜机列表
      if (cabinetCart.cabinetid!=<?=$cabinetInfo['cabinetid']?>) {
        var cabinetCart = {"cabinetid":<?=$cabinetInfo['cabinetid']?>};
        $.cookie('cabinet_cart', JSON.stringify(cabinetCart), {expires:1});
      }
      //根据历史记录刷新当前柜机下商品数量
      if (cabinetCart.list!=undefined) {
        var cartGoodsTotal = 0;
        $.each(cabinetCart.list, function (i, cabinetCartItem) {
          cartGoodsTotal = cartGoodsTotal+parseInt(cabinetCartItem.num);
          $("#c-"+cabinetCartItem.pathway+"-"+cabinetCartItem.type).text(cabinetCartItem.num)
        });
        if (cartGoodsTotal>0) {$("#cfm-buy").find("span").text(cartGoodsTotal);
          $("#cfm-buy").css("display", "block");}
      }
    }

    function freshCartData(){
      var cartTotal = 0;
      var cg = $("#cabinet-tyres").find('.num');
      var cabinetCart = {"cabinetid":<?=$cabinetInfo['cabinetid']?>};
       cabinetCart.list = {};
      $.each(cg, function (i, cgitem) {
          cartTotal = cartTotal + parseInt($("#cabinet-tyres").find('.num:eq('+i+')').text());

          var pw=$("#cabinet-tyres").find('.num:eq('+i+')').data("pathway")+'_'+$("#cabinet-tyres").find('.num:eq('+i+')').data("type");
          var pwd={
            'pathway':$("#cabinet-tyres").find('.num:eq('+i+')').data("pathway"),
            'type':$("#cabinet-tyres").find('.num:eq('+i+')').data("type"),
            'goodsid':$("#cabinet-tyres").find('.num:eq('+i+')').data("goodsid"),
            'num':$("#cabinet-tyres").find('.num:eq('+i+')').text()
          };
          cabinetCart.list[pw]= pwd;

          var aa = $("#cabinet-tyres").find('.num:eq('+i+')').text() +','+ $("#cabinet-tyres").find('.num:eq('+i+')').data("type") +','+ $("#cabinet-tyres").find('.num:eq('+i+')').data("goodsid") +','+ $("#cabinet-tyres").find('.num:eq('+i+')').data("pathway");
      });
      $.cookie('cabinet_cart', JSON.stringify(cabinetCart), {expires:1});

      if (cartTotal>0) {
          $("#cfm-buy").find("span").text(cartTotal);
          $("#cfm-buy").css("display", "block");
      } else {
          $("#cfm-buy").css("display", "none");
      }
    }

    $(".minus").click(function() {
        var t = $(this).parent().find('.num');
        if (t.text()>0){
            t.text(parseInt(t.text())-1);
            if (t.text()<=1) { t.text(0); }
        }
        freshCartData()
    });
    $(".plus").click(function() {
        var t = $(this).parent().find('.num');
        if (parseInt(t.text())<parseInt(t.attr("data-maxbuy"))) {
            t.text(parseInt(t.text())+1);
        }
        freshCartData()
    });
});
</script>
