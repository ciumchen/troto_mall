<?php
$this->title = '订单支付';
//var_dump($addresses);
//var_dump($products);
//exit;
?>

<style type="text/css">
    .main-content > li, .main-content > li:nth-child(n+2) {
        border: none;
    }

    .main-content {
        padding: 0;
    }

    .main-content > li {
        padding: 10px;
    }

    #personal-ul > li:nth-child(3) {
        margin-top: 0;
        border-top: #e3e2e0 solid 0;
    }
</style>

<input type="hidden" id="order_id" value="<?= $order_id ?>">

<div class="location-affirm">
    <span class="icon-position"></span>
    <?php if (count($addresses) === 0): ?>
        <ul class="location">
            <li>收货人：<p>无</p> <span></span></li>
            <li>收货地址：无</li>
        </ul>
    <?php endif; ?>

    <?php if (count($addresses) > 0): ?>
        <ul class="location">
            <li>收货人：<p><?= $addresses[0]['realname'] ?></p> <span><?= $addresses[0]['mobile'] ?></span></li>
            <li>收货地址：<?= $addresses[0]['address'] ?></li>
        </ul>
    <?php endif; ?>
</div>

<ol class="main-content">
    <?php foreach ($products as $product): ?>
        <li><h2>暂无仓库</h2></li>
        <li>
            <ol class="content-tmain">
                <li><a href="##"><img src="<?= $product['thumb'] ?>" alt=""></a></li>
                <li><a href="##">X<?= 1 ?></a></li>
                <li><a href="##">￥<?= number_format($product['marketprice'] * 1, 2); ?></a></li>
                <li><a href="#"><?= $product['title'] ?></a></li>
            </ol>
        </li>
    <?php endforeach; ?>
</ol>

<ul id="personal-ul">
    <li><a href="#">
            <h3>优惠券</h3>
            <p>兑换优惠卷<span class="fxspa"></span></p>
        </a>
    </li>

    <li><a href="#">
            <h3>支付方式</h3>
            <p>微信支付<span class="fxspa"></span></p>
        </a>
    </li>

    <li><h3>应付总额</h3></li>
</ul>

<div class="payment">
    <ul>
        <li><a href="">￥<?= number_format($goods_price, 2) ?></a>（含运费）</li>
        <li><a href=""><span class="icon-warning"></span>点击查看价格详情</a></li>
    </ul>

    <a href="#" id="rule">
        <label for=""><input type="checkbox"></label>
        本人接受《进口个人申请委托》与《十点一刻服务协议》<span class="fxspa"></span>
    </a>

    <button onclick="callpay();">立即支付</button>
</div>