<?php $this->title = '我的订单 - '.Yii::$app->params['site_name'];?>
<style type="text/css">
.blk { background: #fff; border-radius: 10px; margin: 5px 10px; height: 100%; padding: 10px; width: 95%; }
.list-group-item-text{ line-height: 1.5; }
.txtbold{ color: #3e5569; font-weight:bold; }
.btnserve { border-radius: 15px; background-color: #5895c7; border-color: #73b0e5; line-height: .8; width: 78%; font-size: 12px; }
.item-act { display: inline-block; float: right; color: #e5cfcf; font-size: samll; font-weight: bold; padding-bottom: .03rem; }
.item-act a{ color: #e5cfcf; font-size: samll; font-weight: bold; }
</style>
<div class="row" style="background-color:#E7F3ED; color:#3e5569;">
    <div class="col-xs-12">
      <form role="form" style="margin-top:15px; text-align:center;">
        <div class="form-group">
          <label class="radio-inline txtbold"><input type="radio" value="" <?php if($status==''){?> checked="checked" <?php }?> name="history" onclick="location.href='?status='">全部</label>
          <label class="radio-inline txtbold"><input type="radio" value="0" <?php if($status=='0'){?> checked="checked" <?php }?> name="history" onclick="location.href='?status=0'">待付款</label>
          <label class="radio-inline txtbold"><input type="radio" value="1" <?php if($status=='1'){?> checked="checked" <?php }?> name="history" onclick="location.href='?status=1'">待取胎</label>
          <label class="radio-inline txtbold"><input type="radio" value="5" <?php if($status=='5'){?> checked="checked" <?php }?> name="history" onclick="location.href='?status=5'">已完成</label>
        </div>
      </form>
    </div>

  <?php foreach($orderList as $orderItem): ?>
    <div class="col-xs-12 blk order-item" data-orderid="<?=$orderItem['orderid']?>">
        <div class="row" style="border-bottom: 1px dotted #ccc;margin-left:0px;width:100%;">
          <div class="col-xs-5">
            <?php if(strtoupper($orderItem['source'])=='BOX'):?>智能柜下单
            <?php elseif(strtoupper($orderItem['source'])=='WX'):?>微商城下单
            <?php else:?>其他途径下单
            <?php endif;?>
          </div>
          <div class="col-xs-7">下单时间：<?=date('Y-m-d', $orderItem['createtime'])?><span class="item-act"><a href="/order/detail/<?=$orderItem['id']?>">&gt;</a></span></div>
        </div>
        <div class="row mtb10">
          <div class="col-xs-4"><img src="<?=$orderItem['goods'][0]['thumb']?>" class="img-thumbnail"></div>
          <div class="col-xs-8">
            <div class="row">
              <div class="col-xs-12">
                <ul class="list-group">
                  <li class="list-group-item-text txtbold"><?=$orderItem['goods'][0]['title']?></li>
                  <li class="list-group-item-text txtbold"><span><?=$cateList[$orderItem['goods'][0]['pcate']]['name']?></span><span style="padding-left:15px"><?=$orderItem['goods'][0]['brand']?></span></li>
                </ul>
              </div>
              <!-- <div class="col-xs-6 txtbold">￥8946.00</div>
              <div class="col-xs-6"><button class="btn btn-primary btnserve" onclick="window.location.href='/member/handout?uid=98056'">申请售后</button></div> -->
            </div>
          </div>
        </div>
        <div class="row" style="border-top: 1px dotted #ccc; margin-left: 0px; width: 100%; padding-top: 5px;">
          <div class="col-xs-6">共<?=$orderItem['goodstotal']?>条轮胎</div>
          <div class="col-xs-6" style="color:#f00">订单总额￥<?=$orderItem['price']?></div>
        </div>
    </div>
  <?php endforeach; ?>

  <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>

<?php include '../views/layouts/TrotoWxshare.php';?>

<script type="text/javascript">
$(function(){ $(".order-item:last").css("margin-bottom","58px"); });
</script>