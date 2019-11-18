<!-- 底部图标 -->
<?php $session = Yii::$app->session;
$session->open();//开启session?>
<div class="purchase">
    <ul>
        <li>
            <ol id="purc-ol">
                <?php if ($re != 2): ?>
                    <li onclick="<?php echo ($re) ? ' ' : 'collect()'; ?>">
                        <a href="#">
                            <span class="icon-Collection"></span>
                            <i class="states" style="font-style:normal"><?php echo ($re == 1) ? '已收藏' : '收藏'; ?></i>
                        </a>
                    </li>
                <?php else: ?>
                    <li><a href="#"><span class="icon-Collection"></span><i style="font-style:normal">收藏</i></a></li>
                <?php endif; ?>
                <li><a href="/cart">
                        <span class="icon-shopcar"></span>购物车
                        <i href="##" id="data">
                            <?php echo ($session->get('carNum')) ? $session->get('carNum') : 0; ?>
                        </i>
                    </a>
                </li>
            </ol>
        </li>
        <li onclick='addCart()'><a href='#'>加入购物车</a></li>
        <li><a href="/order/pay">立即购买</a></li>
    </ul>
</div>

<!--返回顶部开始-->
<!-- <a id="gotop"></a> -->
<script src="/js/commonality.js"></script>
<script type="text/javascript">
    // 选项卡
    $(function () {
        $("#blush li").click(function () {
            $(this).addClass("tabin").siblings().removeClass("tabin");
            var index = $(this).index();
            $(".nav-list").hide();
            $(".nav-list").eq(index).show();
        })
    });
</script>


<!-- Swiper JS -->
<script src="/js/swiper.min.js"></script>
<!-- 幻灯片js -->
<script>
    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        paginationClickable: true,
        spaceBetween: 60,
        centeredSlides: true,
        autoplay: 3500,
        autoplayDisableOnInteraction: false
    });

    $('audio,video').mediaelementplayer({
        success: function (player, node) {
            $('#' + node.id + '-mode').html('mode: ' + player.pluginType);
        }
    });
</script>
