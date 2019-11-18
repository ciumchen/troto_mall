<?php $this->title = '我的优惠券'; ?>

<!--优惠券页面-->
<div class="personal-box">
    <ul id="perso">
        <li class="peli">未使用</li>
        <li>已使用</li>
        <li>已过期</li>
    </ul>

    <?php foreach ($status_coupons as $key => $coupons): ?>
        <!-- 分类列表 -->
        <div class="discount-box nav-list" style="display: <?= $key === 0 ? 'block' : 'none' ?>;">
            <!-- 同一分类下，优惠券列表 -->
            <?php foreach ($coupons as $idx => $c): ?>
                <ul>
                    <li><a href="###">￥<?= $c['value'] ?></a></li>
                    <li>
                        <p>(共<?= $c['total'] ?>张)</p>
                        <h3>无条件使用</h3>
                        <span>截止时间：<?= date('Y-m-d', $c['expire_end']) ?></span>
                    </li>
                    <li>
                        <?php if ($key === 0): ?>
                            <a href="/"><h3>去逛逛</h3></a>
                        <?php endif; ?>
                    </li>
                </ul>
                <?php if (($idx + 1) < count($coupons)): ?>
                    <div class="line"></div>
                <?php endif; ?>
            <?php endforeach; ?>

            <!-- 本分类下，无优惠券提示 -->
            <?php if (count($coupons) === 0): ?>
                <div class="not-have">
                    <img src="../images/privilege-no.png">
                </div>

                <div style="text-align: center;margin-top: 2em;">
                    <a href="/coupon/exchange" class="convert-btn" style="margin-left: 0em;">优惠券兑换</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<script type="text/javascript">
    // 选项卡
    $(function () {
        $("#perso li").click(function () {
            $(this).addClass("peli").siblings().removeClass("peli");
            var index = $(this).index();
            $(".nav-list").hide();
            $(".nav-list").eq(index).show();
        })
    });
</script>