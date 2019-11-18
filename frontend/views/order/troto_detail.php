<?php $this->title = '我的订单 - '.Yii::$app->params['site_name'];?>
<style type="text/css">
.blk { background: #fff; border-radius: 10px; margin: 5px 10px; height: 100%; padding: 10px; width: 95%; }
.list-group-item-text{ line-height: 1.5; }
.txtbold{ color: #3e5569; font-weight:bold; }
.btnserve {border-radius:15px; background-color:#5895c7; border-color:#73b0e5; line-height:.8; width:78%; font-size:12px;}
.item-act { display: inline-block; float: right; color: #e5cfcf; font-size: samll; font-weight: bold; padding-bottom: .03rem; }
.item-act a{ color: #e5cfcf; font-size: samll; font-weight: bold; }
.show-box { display: none; width: 100%; }
.show-box a { padding: 15px; display: block; position: relative; }
.show-box a:after { content: ''; position: absolute; z-index: 2; bottom: 0; left: 0; width: 100%; height: 1px; border-bottom: 1px solid #dedede;
    -webkit-transform: scaleY(0.5);
    transform: scaleY(0.5);
    -webkit-transform-origin: 0 100%;
    transform-origin: 0 100%;
}
.show-button { width: 135px; height: 30px; margin: 0 auto;  position: relative; font-style: normal; font-size: 0.65rem; color: #252529; border: 1px solid #a9a9a9; border-radius: 20px; padding: 0.2rem 0.5rem; text-align: center;
}
.show-button a {
    display: block;
    width: 80px;
}
.show-button span {
    width: 120px;
    display: block;
    margin: 4px 5px 10px 0;
    position: relative;
    font-size: 1.2rem;
}
.show-button a {
    display: block;
    width: 80px;
}
.show-button span {
    width: 120px;
    display: block;
    margin: 4px 5px 10px 0;
    position: relative;
    font-size: 0.8rem;
}
.show-button a { display: block; width: 80px; }
.show-button span { width:120px; display:block; margin: 4px 5px 10px 0; position:relative; font-size:1.2rem;}
</style>
<div class="row" style="background-color:#E7F3ED; color:#3e5569;">
    <div class="col-xs-12 blk order-item" data-orderid="349">
        <!--
        <div class="row" style="border-bottom: 1px dotted #ccc;margin-left:0px;width:100%;">
            <div class="col-xs-7">下单时间：2019-10-09</div>
            <div class="col-xs-5" style="text-align:right;">已收货</div>
        </div>
        -->
        <div class="col-xs-12 col-title mtb10 clearfix"><span class="font16">订单商品</span></div>
        <?php foreach ($orderGoods as $orderGoodsItem): ?>
        <?php if (!isset($goodsTotal)) $goodsTotal=0; $goodsTotal+=$orderGoodsItem['total']; ?>
        <div class="row mtb10" style="border-bottom:1px #cce dotted;width:100%; margin-left:0;     padding-bottom:5px;">
          <div class="col-xs-4"><img src="<?=$orderGoodsItem['thumb']?>" class="img-thumbnail"></div>
          <div class="col-xs-6" style="padding:0;">
            <ul>
                <li><h5 style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:bold;"><?=$orderGoodsItem['brand']?> <?=$orderGoodsItem['title']?></h5></li>
                <li><h4>￥<?=$orderGoodsItem['price']?></h4></li>
            </ul>
          </div>
          <div class="col-xs-2" style="padding-top: 33px;font-size: 14px;">× <?=$orderGoodsItem['total']?></div>
        </div>
        <?php endforeach; ?>

        <div class="row" style="margin-left: 0px; width: 100%; padding-top: 0px;">
          <div class="col-xs-6" style="color:#f00;"><?php if($orderInfo->status==0): ?>
            <a href="/order/prepay?sn=<?=$orderInfo->ordersn?>" style="text-align:center; border-radius:5px; color:#fff; background-color:#00a0ea;     padding: 2px 10px;">去付款</a>
          <?php else: ?><?=$orderStatus?>
          <?php endif; ?>
          </div>
          <div class="col-xs-6" style="color:#f00; text-align:right;">共<?=$goodsTotal?>条轮胎</div>
        </div>
        <div class="row" style="margin-left:0px; width:100%; padding-top:15px;">
          <div class="col-xs-6">取货柜机：</div>
          <div class="col-xs-6" style="text-align:right; background-color:" id="nav2cabinet"><?=$cabinetInfo->name?><br><span style="font-size:10px">(点击打开导航)</span></div>
        </div>
    </div>
    <div class="col-xs-12 blk order-item">
        <div class="col-xs-12 col-title mtb10 clearfix"><span class="font16">订单费用</span></div>
        <div class="row" style="margin-left:0px;width:100%;">
            <div class="col-xs-5">订单编号：</div>
            <div class="col-xs-7" style="text-align:right;"><?=$orderInfo->ordersn?></div>
        </div>
        <div class="row" style="margin-left:0px;width:100%; margin-top:10px;">
            <div class="col-xs-5">下单时间：</div>
            <div class="col-xs-7" style="text-align:right;"><?=date('Y-m-d H:i',$orderInfo->createtime)?></div>
        </div>
        <div class="row" style="margin-left:0px;width:100%; margin-top:10px;">
            <div class="col-xs-5">商品总额：</div>
            <div class="col-xs-7" style="text-align:right;">￥<?=$orderInfo->goodsprice?></div>
        </div>
        <div class="row" style="margin-left:0px;width:100%; margin-top:10px;">
            <div class="col-xs-5">服务费用：</div>
            <div class="col-xs-7" style="text-align:right;">￥<?=$orderInfo->taxtotal?></div>
        </div>
        <div class="row" style="margin-left:0px;width:100%; margin-top:10px;">
            <div class="col-xs-5">优惠金额：</div>
            <div class="col-xs-7" style="text-align:right;">￥0.00</div>
        </div>
        <div class="row" style="margin-left:0px;width:100%; margin-top:10px;">
            <div class="col-xs-5">订单总额：</div>
            <div class="col-xs-7" style="text-align:right;">￥<?=$orderInfo->price?></div>
        </div>
    </div>
    <div class="col-xs-12 blk order-item" style="margin-bottom: 60px;">
        <div class="show-box">
            <div class="col-xs-12 col-title mtb10 clearfix"><span class="font16">其它信息</span></div>
            <div class="row" style="margin-left:0px;width:100%; margin-top: 10px;">
                <div class="col-xs-12">支付方式：微信在线支付</div>
            </div>
            <div class="row" style="margin-left:0px;width:100%; margin-top: 10px;">
                <div class="col-xs-12">下单时间：<?=date('Y-m-d H:i:s',$orderInfo->createtime)?></div>
            </div>
            <div class="row" style="margin-left:0px;width:100%; margin-top: 10px;">
                <div class="col-xs-12">服务记录：
                    <ol>
                    <?php foreach (explode('^^', $orderInfo->ext) as $extItem): ?>
                        <li><?=$extItem?>；</li>
                    <?php endforeach; ?>
                    </ol>
                </div>
            </div>
            <!-- <div class="row" style="margin-left:0px;width:100%; margin-top: 10px;">
                <div class="col-xs-12">完成时间：2019-10-09</div>
            </div> -->
        </div>
        <div class="row show-button" style="margin-top: 10px;">
            <a class="" href="javascript:void(0);" id="show-btn">
                <span id="show-open">展开订单完整信息</span>
                <i class="icon fa fa-angle-right"></i>
            </a>
        </div>
    </div>

  <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>

<?php // include '../views/layouts/TrotoWxshare.php';?>

<!-- <script type="text/javascript">
$(function(){ $(".order-item:last").css("margin-bottom","58px"); });
</script> -->
<script type="text/javascript">wx.config(<?=$signPackage?>);</script>
<script type="text/javascript">
$(function view_details_click() {
    $("#show-btn").bind('click', function() {
        if ($(".show-box").is(":hidden")) {
            $(".show-box").show();
            $(this).find("#show-open").text(" 收起订单完整信息");
            $(this).find(".fa").removeClass("fa-angle-right").addClass("fa-angle-down");
        } else {
            $(".show-box").hide();
            $(this).find("#show-open").text(" 展开订单完整信息");
            $(this).find(".fa").removeClass("fa-angle-down").addClass("fa-angle-right");
        }
    });
});

$('#nav2cabinet').click(function () {
    wx.openLocation({
        latitude: <?=$cabinetInfo->lat?>, //纬度，浮点数，范围为90 ~ -90
        longitude: <?=$cabinetInfo->lng?>, //经度，浮点数，范围为180 ~ -180。
        name: '多轮多 <?=$cabinetInfo->name?>', //位置名
        address: '<?=$cabinetInfo->address?>', //地址详情说明
        scale: 10, //地图缩放级别,整形值,范围从1~28。默认为最大
    });

});
</script>