<?php $this->title = '个人中心'; ?>

<div class="personal-top" style="margin:0;">
    <ul>
        <!--<li><a href="/member/profile"><img src="images/head-portrait.png"></a></li>-->
        <li><a href="/member/profile">
                <figure>
                    <img src="<?= ($UserInfoData['avatar']) ? $UserInfoData['avatar'] : '/images/hader.png'; ?>"/>
                </figure>
            </a>
        </li>
        <li><h2><?= ($UserInfoData['nickname']) ? $UserInfoData['nickname'] : ''; ?></h2></li>
    </ul>
</div>
<div class="personal-box">
    <ul id="personal-ul">
        <li><a href="/member/order"><h3>我的订单</h3>
                <p>查看我的订单<span class="fxspa"></span></p></a></li>
        <!-- 		<li class="listing">
                    <ul id="shipments">
                        <li><a href=""><span class="icon-oder"></span><p>待付款</p></a></li>
                        <li><a href=""><span class="icon-cargo"></span><p>待发货</p></a></li>
                        <li><a href=""><span class="icon-cargocar"></span><p>待收货</p></a></li>
                        <li><a href=""><span class="icon-finish"></span><p>已完成</p></a></li>
                    </ul>
                </li> -->
        <li><a href="/member/like"><h3>我的收藏</h3>
                <p>查看我的收藏<span class="fxspa"></span></p></a></li>
        <!--<li><a href="/member/coupon"><h3>我的优惠券</h3>
                <p>兑换优惠券<span class="fxspa"></span></p></a></li>-->
        <li><a href="/member/address"><h3>收货地址管理</h3><span class="fxspa"></span></a></li>
        <li><a href="/member/tips"><h3>闪购提醒</h3><span class="fxspa"></span></a></li>
        <li><a href="/member/invite"><h3>邀请好友</h3><span class="fxspa"></span></a></li>
        <li><a href="/member/share-money"><h3>分享成钱</h3>
                <p>查看好友消费<span class="fxspa"></span></p></a></li>
        <li><a href="/member/help"><h3>十点一刻客服</h3>
                <p>有问题找我们<span class="fxspa"></span></p></a></li>
        <li><a href="/member/aboutus"><h3>关于十点一刻</h3><span class="fxspa"></span></a></li>
    </ul>
</div>


<script src="js/commonality.js"></script>
<script type="text/javascript">
    // 选项卡
    $(function () {
        $("#ridge li").click(function () {
            $(this).addClass("ridge-li").siblings().removeClass("ridge-li");
            var index = $(this).index();
            $(".nav-list").hide();
            $(".nav-list").eq(index).show();
        })
    });
</script>