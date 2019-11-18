<?php
$this->title = '订单支付';
//var_dump($addresses);
//var_dump($products);
//exit;
?>

<style type="text/css">
.main-content > li, .main-content > li:nth-child(n+2) { border: none; }
.main-content { padding: 0; }
.main-content > li { padding: 10px; }
#personal-ul > li:nth-child(3) {
    margin-top: 0;
    border-top: #e3e2e0 solid 0;
}
</style>

<input type="hidden" id="order_id" value="<?= $order->id ?>">

<ul id="personal-ul">
    <li>
        <a href="##">
            <h3>品名</h3>
            <p><?= $goods_name ?></p>
        </a>
    </li>

    <li>
        <a href="##">
            <h3>商品总额</h3>
            <p>￥<?= number_format($order->goodsprice, 2) ?></p>
        </a>
    </li>

    <?php if ($coupon !== false): ?>
        <li>
            <a href="##">
                <h3>优惠券</h3>
                <p>-￥<?= $coupon['value'] ?></p>
            </a>
        </li>
    <?php endif; ?>

    <?php if ($isFirstOrder): ?>
        <li>
            <a href="#">
                <h3>新人首单优惠</h3>
                <p>-￥<?= number_format($order->coupon, 2) ?></p>
            </a>
        </li>
    <?php endif; ?>

    <?php if ($taxtotal>0):?>
        <li>
            <a href="##">
                <h3>应付税费</h3>
                <p>￥<?= number_format($taxtotal, 2) ?></p>
            </a>
        </li>
    <?php endif; ?>

    <li>
        <a href="##">
            <h3>应付总额</h3>
            <p>￥<?= number_format(($order_price+$taxtotal), 2) ?></p>
        </a>
    </li>
</ul>

<div class="payment">
    <?php if ($order->status == 0) : ?>
        <button onclick="callpay();">立即支付</button>
    <?php else: ?>
        <button>订单已支付</button>
    <?php endif; ?>
</div>