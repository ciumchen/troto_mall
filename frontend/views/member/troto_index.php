<?php $this->title = '个人中心 - '.Yii::$app->params['site_name'];?>
<style type="text/css">
.col-title {border-left-color:#00A0EA}
.item-icon { width:22px; height:22px; }
.item-act { display: inline-block; float: right; color:#e5cfcf; font-size: large; font-weight: bold; padding-bottom:.03rem;}
</style>
<div class="row">
    <div class="col-xs-12 ptb20 bgf2" style="background-color:#00A0EA; font-weight:bold; color:#fff; padding-bottom:0px;">
        <div class="row text-center">
          <div class="col-xs-6"><img src="<?=$userInfo->avatar?>" style="width:68px; border:3px #fff solid; border-radius:50px; margin-right:1.68em;"><?=$userInfo->nickname?></div>
          <div class="col-xs-6">星级</div>
        </div>
        <div class="row mt20 text-center">
          <div class="col-xs-4">0<br>积分</div>
          <div class="col-xs-4" style=" border-left:#cecece 1px solid; border-right:#cecece 1px solid; "><?=$userInfo->deposit?><br>余额</div>
          <div class="col-xs-4">历史<br>清单</div>
        </div>
        <div class="row mt5" style="width: 110%;height: 100px;background-image:url(/images/bg_member_header.png);"></div>
    </div>

    <div class="col-xs-12 bgf2">
        <div class="col-title mtb10 clearfix"><span class="font16">我的</span></div>
        <div class="member-items">
            <ul class='list-group'>
              <li class="list-group-item"><a href="/member/handout"><span><img class="item-icon" src="/images/icon_handout.png"></span> 发钱 <sup style="color:red; font-size:65%;">VIP</sup> <span class="item-act">&gt;</span></a></li>
              <li class="list-group-item"><a href="/order/list"><span><img class="item-icon" src="/images/icon_orders.png"></span> 订单记录 <span class="item-act">&gt;</span></a></li>
              <li class="list-group-item"><span><img class="item-icon" src="/images/icon_handout.png"></span> 服务记录 <span class="item-act">&gt;</span></li>
              <li class="list-group-item"><a href="/member/credits"><span><img class="item-icon" src="/images/icon_credits.png"></span> 积分记录 <span class="item-act">&gt;</span></a></li>
            </ul>
        </div>
    </div>

    <div class="col-xs-12 bgf2" style="padding-bottom:52px;">
        <div class="col-title mtb10 clearfix"><span class="font16">服务</span></div>
        <div class="member-items">
            <ul class='list-group'>
              <li class="list-group-item"><a href="/about-us"><span><img class="item-icon" src="/images/icon_about.png"></span> 关于我们 <span class="item-act">&gt;</span></a></li>
              <li class="list-group-item"><a href="/service-policy"><span><img class="item-icon" src="/images/icon_service.png"></span> 服务政策 <span class="item-act">&gt;</span></a></li>
              <li class="list-group-item"><a hrefto="/service-customer" href="tel:<?=Yii::$app->params['serviceNumber']?>"><span><img class="item-icon" src="/images/icon_help.png"></span> 联系客服 <span class="item-act">&gt;</span></a></li>
            </ul>
        </div>
    </div>

    <?php include dirname(__DIR__).'/layouts/TrotoBottomNavBar.php'; ?>
</div>
