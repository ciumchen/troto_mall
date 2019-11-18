<?php $this->title = '确认提交订单'; ?>
<style type="text/css">
body{background-color:#E7F3ED;}
.blk { background:#fff; border-radius:10px; margin: 5px 10px; height:100%; padding:10px; width:95%; }
.list-group-item-text{ line-height: 1.5; }
.txtbold{ color: #3e5569; font-weight:bold; }
.btnserve { font-size:15px; width:95%; border-radius:5px; background-color:#5895c7; border-color:#73b0e5; line-height:1.2; }
.item-act { display:inline-block; float:right; color:#e5cfcf; font-size:samll; font-weight:bold; padding-bottom:.03rem; }
</style>
<div class="row" style="background-color:#E7F3ED; color:#3e5569;">
    <!-- cart goods list -->
    <div class="col-xs-12 blk">
      <?php $tyresTotal=0; $amoutTotal=0; 
      foreach ($goodsList as $cartGoods): ?>
      <?php $tyresTotal+= $cartGoods['total']; $amoutTotal+=($cartGoods['total']*$cartGoods['price']); ?>
        <div class="row mtb10" style="border-bottom:1px dotted #ccc; padding-bottom:10px;">
          <div class="col-xs-4"><img src="<?=$cartGoods['thumb']?>" class="img-thumbnail" style="width:100%"></div>
          <div class="col-xs-8">
            <div class="row">
              <div class="col-xs-12">
                <ul class="list-group" style="margin-bottom:8px;">
                  <li class="list-group-item-text txtbold" style="line-height:2.2;"><?=$cartGoods['brand']?> <?=$cartGoods['title']?></li>
                </ul>
              </div>
              <div class="col-xs-9 txtbold"><?=$cartGoods['type']=='1'?'上门安装':'自助取胎'?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;￥<?=$cartGoods['price']?></div>
              <div class="col-xs-3">✖ <?=$cartGoods['total']?></div>
            </div>
          </div>
        </div>
      <?php endforeach ?>
        <div class="row" style="margin-left:0px; width:100%; padding-top:5px;">
          <div class="col-xs-8"></div>
          <div class="col-xs-4">共 <?=$tyresTotal?> 条轮胎</div>
        </div>
    </div>

    <!-- price count info -->
    <div class="col-xs-12 blk" style="margin-bottom: 60px;">
        <div class="row">
          <div class="col-xs-8" style="padding-left:2em; font-size: 16px">商品总额</div>
          <div class="col-xs-4" style="padding-right:2em;">￥<span id="ord-goods"><?=($amoutTotal-$tyresTotal*$cabinetInfo->fee)?></span></div>
        </div>
        <div class="row">
          <div class="col-xs-8" style="padding-left:2em; font-size: 16px">服务费</div>
          <div class="col-xs-4" style="padding-right:2em;">￥<span id="ord-fee"><?=($tyresTotal*$cabinetInfo->fee)?></span></div>
        </div>
        <div class="row">
          <div class="col-xs-8" style="padding-left:2em; font-size: 16px">优惠金额</div>
          <div class="col-xs-4" style="padding-right:2em;">-￥<span id="ord-discount">0.00</span></div>
        </div>
        <div class="row">
          <div class="col-xs-8" style="padding-left:2em; font-size: 16px">订单总额</div>
          <div class="col-xs-4" style="padding-right:2em;">￥<span class="ord-total"><?=$amoutTotal?></span></div>
        </div>
    </div>
</div>
<div class="row" style="position: fixed;bottom: 45px;background-color: #fff;color: #3e5569;width: 100%; ">
    <div class="col-xs-8 ord-total" style="padding-left:1.8em; font-size:18px; line-height:2.5; color:#f00;">￥<?=$amoutTotal?></div>
    <div class="col-xs-4" id="confirm-submit-order" style="padding-left:1.8em; font-size:18px; line-height:2.5; color:#fff; background-color:#00a2ea; ">立即付款</div>
    <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>

<?php include '../views/layouts/TrotoWxshare.php';?>

<script type="text/javascript">
$(function(){
  var _csrf = $('meta[name="csrf-token"]').attr("content");
  $("#confirm-submit-order").click(function() {
    $.post("/order/create2", {"_csrf":_csrf}, function(res){
      if (res.code==2000) {
        $.removeCookie('cabinet_cart', { path:'/'});
        window.location.href="/order/prepay?sn="+res.data.ordersn;
      }
    });
  });
});
</script>