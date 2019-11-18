<?php
//$route_name = $this->context->request->getPathInfo();

$category_active = ($route_name === 'home' || $route_name === '' ? 'class="tali"' : '');
$activity_active = ($route_name === 'goods/cate' ? 'class="tali"' : '');
$order_active = ($route_name === 'order/show' ? 'class="tali"' : '');
$cart_active = ($route_name === 'cart' ? 'class="tali"' : '');
$member_active = ($route_name === 'member' ? 'class="tali"' : '');
?>

<!-- 底部图标 -->
<footer>
    <ul id="foot">
        <li <?= $category_active ?> onclick="window.location.href='/home'">
            <span class="icon-home"></span>首页
        </li>

        <li <?= $activity_active ?> onclick="window.location.href='/goods/cate'">
            <span class="icon-classification"></span>商品分类
        </li>

        <!--
        <li <?= $order_active ?> onclick="window.location.href='/order/show'">
			<span class="icon-like">
				<span class="path1"></span>
				<span class="path2"></span>
			</span>晒单现场
        </li>
        -->

        <li <?= $cart_active ?> onclick="window.location.href='/cart'">
            <span class="icon-shopcar"></span>购物车
        </li>

        <li <?= $member_active ?> onclick="window.location.href='/member'">
            <span class="icon-me"></span>个人中心
        </li>
    </ul>
</footer>