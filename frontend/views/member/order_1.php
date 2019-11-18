<?php $this->title = '已支付订单列表'; ?>
<style type="text/css">
#shipments > li { width: 20%; }
.headline-bottom>li:nth-child(n+2) a{float: right;}
.headline-bottom>li{
	width:50%;
}
body{ background-color: #fff;}
</style>
<div class="shipments-box">
    <ul id="shipments">
        <li onclick="window.location.href='/member/order'"><span class="icon-alloder"></span><p class="shipme">全部</p></li>
        <li onclick="window.location.href='/member/order?status=0'"><span class="icon-oder"></span><p class="shipme">未支付</p></li>
        <li class="click"><span class="icon-cargo"></span><p class="shipme">已付款</p></li>
        <li onclick="window.location.href='/member/order?status=2'"><span class="icon-cargocar"></span><p class="shipme">已发货</p></li>
        <li onclick="window.location.href='/member/order?status=3'"><span class="icon-finish"></span><p class="shipme">已完成</p></li>
    </ul>
</div>

<?php if (empty($orderData)) { ?>
    <div class="mainbody">
        <img src="/images/not-have.png">
        <p>没有任何订单哦，去买买买</p>
    </div>
<?php } else { ?>
	<div class="indentmain nav-list">
	    <?php foreach ($orderData as $order) { ?>
	        <?php if ($order['status'] == 1): ?>
	            <ul class="headline-title">
	                <li>订单编号:<a href=""><?php echo $order['ordersn']; ?></a></li>
	                <li><a href=""><?php echo date("Y-m-d", $order['createtime']); ?></a></li>
	            </ul>
	            <ol class="main-content">
	                <?php foreach ($order['goods'] as $goods ): ?>
	                    <li>
	                        <ol class="content-tmain">
	                            <li><a href="/goods/detail?id=<?php echo $goods['goodsid'];?>"><img src="<?php echo $goods['thumb']; ?>" alt=""></a></li>
	                            <li><a href="#">￥<?php echo $goods['total'] * $goods['marketprice']; ?></a><a href="#">X<?php echo $goods['total']; ?></a></li>
	                            <li><a href="/goods/detail?id=<?php echo $goods['goodsid'];?>"><?php echo $goods['title']; ?></a></li>
	                        </ol>
	                    </li>
	                <?php endforeach; ?>
	            </ol>
	            <ul class="headline-bottom">
	                <li><a href="#">状态：已付款</a></li>
	                <li><a href="/order/detail?order_id=<?= $order['id'] ?>">查看详情<span></span></a></li>
	            </ul>
	        <?php endif; ?>
	    <?php } ?>
	</div>
<?php } ?>
<div style="width: 100%; height: 60px;"></div>