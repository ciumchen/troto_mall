<?php $this->title = '分享成钱'; ?>

<div class="personal-top">
    <ul>
        <li><a href="javascript:;">
                <figure>
                    <img src="<?= ($member->avatar ? $member->avatar : '/images/hader.png') ?>">
                </figure>
            </a></li>
        <li><h2><?= $member->nickname ?></h2></li>
        <li>
            可提现分成：￥<?= $member->credits6 ?> &ensp;
            <a href="/broker/withdraw" id="btn">提&#8197;现</a>
        </li>
    </ul>
</div>
<ul id="between">
    <li>
        <ol>
            <li>业绩：￥<?= $member->credits7 ?></li>
            <li>分成：￥<?= $member->credits5 ?><span></span></li>
        </ol>
    </li>
    <li>我的海淘家族<p><?= $totalUnderLine ?>人</p></li>
</ul>

<ul id="grade">
<?php if ($agent): ?>
    <li class="tabin">我的海淘一代<br><span><?= count($threeUnderLines[0]) ?>人</span></li>
    <li>我的海淘二代<br><span><?= count($threeUnderLines[1]) ?>人</span></li>
    <li>我的海淘后代<br><span>最近成交订单</span></li>
<?php else : ?>
    <li class="tabin" style="width: 49%;">我的海淘一代<br><span><?= count($threeUnderLines[0]) ?>人</span></li>
    <li style="width: 49%;">我的海淘二代<br><span><?= count($threeUnderLines[1]) ?>人</span></li>
<?php endif; ?>
</ul>

<!-- 我的海淘下代 -->
<?php foreach ($threeUnderLines as $level => $brokers): ?>
    <div class="member1" style="<?= ($level === 0 ? 'display: block;' : '') ?>">
        <?php foreach ($brokers as $broker): ?>
            <ul>
                <li><a href="#"><img src="<?= $broker['avatar'] ?>" alt=""></a></li>
                <li>
                    <ol>
                        <li><a href="">昵称：<?= $broker['nickname'] ?></a></li>
                        <li><a href="">加入时间：<?= date('Y-m-d', $broker['joindate']) ?></a></li>
                        <li><a href="">ID：<?= $broker['uid'] ?></a></li>
                    </ol>
                </li>
                <?php if ($broker['credits2'] == 0): ?>
                    <li>状态<br><a href="">未激活</a></li>
                <?php else: ?>
                    <li>贡献业绩<br><a href="">￥<?= $broker['credits2'] ?></a></li>
                <?php endif; ?>
            </ul>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<!-- 代理则显示下线近期订单 -->
<?php if($agent): ?>
    <div class="member1" id="underlines-order">
        <?php foreach ($underLinesOrder as $broker): ?>
            <ul>
                <li><a href="#"><img src="<?= $broker['avatar'] ?>" alt=""></a></li>
                <li>
                    <ol>
                        <li><a href="#" class="ayc"><?= $broker['nickname'] ?></a><a href="#" class="afr">加入时间：<?= date('Y-m-d', $broker['joindate']) ?></a></li>
                        <li><a href="#">订单号：SDYK201608081813007930012</a></li>
                        <li>订单状态：已支付</li>
                    </ol>
                </li>
                <li></li>
                <li>
                    <dl>
                        <dd>支付时间 09-25 15:26</dd>
                        <dd>分成：￥1654.26</dd>
                    </dl>
                </li>
            </ul>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    // 选项卡
    $(function () {
        $("#grade li").click(function () {
            $(this).addClass("tabin").siblings().removeClass("tabin");
            var index = $(this).index();
            $(".member1").hide();
            $(".member1").eq(index).show();
        })
    });
</script>