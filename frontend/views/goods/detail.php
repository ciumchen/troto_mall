<?php
use yii\helpers\Html;
$this->title = $goods['title'];
// var_dump($goods);exit();
?>
<?php include "../views/prompt/information.php"; ?>

<style type="text/css">
.swiper-slide>img{ width: 70%; }
#enshrine{ width: 60px; height: 55px; display: inline-block; position: absolute; bottom:0; right: 10px; }
#enshrine>a img{ width: 22px; height: 22px; margin-bottom: 2px; }
.sellout { color: #fff; }
</style>

<input type="hidden" id="goods-id" value="<?= $goods['id'] ?>">
<!-- banner -->
<div class="swiper-container">
    <div class="swiper-wrapper" style="width:100%">
        <div class="swiper-slide">
            <img src="<?= $goods['thumb'] ?>">
            <div id="enshrine" onclick="collect(<?= $goods['id'] ?>);">
                <a href="javascript:;" id='icon'>
                    <span class="icon-Collection"></span>
                    <i class="states" id="is-collect" style="font-style:normal">收藏</i>
                </a>
            </div>
        </div>
    </div>
    <div class="swiper-pagination"></div>
</div>

<div class="recommend">
    <ul>
        <li><h1><a href=""><?= $goods['title'] ?></a></h1></li>
        <li><?= $goods['fdesc'] ?></li>
        <li data-marketprice="<?=$goods['marketprice']?>">￥<?=$goods['marketprice']?>&nbsp;</li>
    <? if ($goods['originalprice']>0): ?>
        <li>原价：<s>￥<?= $goods['originalprice'] ?></s>&nbsp;&nbsp;</li>
    <? endif; ?>
    <? if ($goods['marketprice']>0 && $goods['originalprice']>0): ?>
        <li style="font-size:15px; color:#ce0c0c; padding:0px 3px; border: 2px dotted #f00; float: right;"><?=round(($goods['marketprice']/$goods['originalprice'])*10, 1)?>折<span></span></li>
    <? endif; ?>
        <?php if ($goods['taxrate']>0): ?>
        <li>(此商品适用税率<?=$goods['taxrate']?>%)</li>
        <?php endif ?>
    <?php if(time() > $goods['starttime'] && time() < $goods['f_endtime']):?>
<!--
        <li>
            <div class="surplus-time" id="fnTimeCountDown" data-end="<?=date('Y/m/d H:i:s',$goods['f_endtime'])?>">
                <i><img src="/images/caution.png"></i>剩余<span class="day">00</span>天<span class="hour">00</span>时<span class="mini">00</span>分<span class="sec">00</span>秒
            </div>
        </li> 
-->
	<?php else:?>
		<?php if(time() < $goods['starttime']):?>
		<li>
			<div class="surplus-time" id="fnTimeCountDown" data-end="<?=date('Y/m/d H:i:s',$goods['starttime'])?>">
				<i><img src="/images/caution.png"></i>距开始<span class="day">00</span>天<span class="hour">00</span>时<span class="mini">00</span>分<span class="sec">00</span>秒
			</div>
		</li>
		<?php endif;?>
    <?php endif;?>
    </ul>
    <script type="text/javascript">
        $("#fnTimeCountDown").fnTimeCountDown("2016/07/20 11:43:00");
    </script>
    <ol>
        <li style="padding:0px  3px; color: #f00; font-weight: bold;border: 1px solid #f00; margin-right:5px">自营</li>
        <li style="padding:0px  3px; color: #f00; font-weight: bold;border: 1px solid #f00; margin-right:5px">包邮</li>
        <?php if ($goods['taxrate']==0): ?>
        <li style="padding:0px 3px; color: #f00; font-weight: bold;border: 1px solid #f00">包税</li>
        <?php endif ?>
        <?php if (isset($goods['name']) && $goods['name'] != '') { ?>
            <li><span><img src="/images/country_flags/<?= $goods['countryimg'] ?>" style="width: 12px;height: 12px;margin-right: 5px;"></span><?= $goods['name'] ?>
            </li>
        <?php } ?>
        <li>库存：<?= $goods['total'] ?></li>
        <li>品牌：<?= $goods['brand'] ?><?= $goods['brand'] ?></li>
    </ol>
</div>

<?php if ($goods['b_content'] && $goods['brandimg']): ?>
    <div class="brand">
        <span><img src="<?= $goods['brandimg'] ?>" alt=""></span>
        <p><?= $goods['b_content'] ?></p>
    </div>
<?php endif; ?>

<div class="referral">
    <span></span>
    <div class="refer"><h2>产品介绍</h2></div>
</div>
<?php if (count($goodsParam) > 0): ?>
    <div class="norms">
        <ul>
            <?php foreach ($goodsParam as $param): ?>
                <li><h2><?= $param['title'] ?>：</h2><?= $param['value'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<div class="referbox">
    <!-- 视频 -->
    <?php if ($goods['video']): ?>
        <div style="width:100%; overflow: hidden;">
            <video class="mejs-wmp" width="100%" src="<?= $goods['video'] ?>" type="video/mp4"
                   id="player1" poster="../images/1.jpg"
                   controls="controls" preload="none"></video>
        </div>
    <?php endif; ?>
    <?php if ($goods['content']): ?>
        <div class="refertext">
            <?= $goods['content'] ?>
        </div>
    <?php endif; ?>
</div>
<?php if ($Relatedgoods): ?>
    <div class="referral">
        <span></span>
        <div class="refer"><h2>商品推荐</h2></div>
    </div>
    <div class="movable-box03" style="display: block; margin-bottom: 70px;">
        <div class="comm01" style="display: block; padding: 0 10px; margin-top: 6px;">
            <ul class="wares">
                <?php foreach ($Relatedgoods as $Related): ?>
                    <li class="li2">
                        <ul class="wares-ul">
                            <li onclick='window.location.href="/goods/detail?id=<?php echo $Related['id']; ?>"'>
                                <img src="<?php echo $Related['thumb'] ?>">
                            </li>
                            <li><h3><span></span><?php echo $Related['title'] ?></h3></li>
                            <li><h3>RMB <?php echo $Related['marketprice'] ?></h3></li>
                            <li onclick='window.location.href="goods?id=<?php echo $Related['id']; ?>"'>
                                查看详情<span class="icon-more2"></span>
                            </li>
                        </ul>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
<!-- 底部图标 -->
<div class="purchase">
    <ul>
        <li>
            <ol id="purc-ol">
                <li><a href="/home"><span class="icon-home"></span>首页</a></li>
                <li>
                    <a href="/cart"><span class="icon-shopcar"></span>购物车<i href="#" id="data">0</i></a>
                </li>
            </ol>
        </li>
        <?php if ($goods['total']<1) :?>
        <li class="join" id=''>&nbsp;</li>
        <li class="immediately sellout">已售罄</li>
        <?php else: ?>
        <li class="join" id='cartState'><a href='##'>加入购物车</a></li>
        <li class="immediately" id='cartState'><a href="javascript:;">立即购买</a></li>
        <input type='hidden' id='userInfo' value="<?= $uid ?>">
        <?php if ($goods['wname']): ?>
            <input type='hidden' id='warehouseName' value="<?= $goods['wname'] ?>">
        <?php else: ?>
            <input type='hidden' id='warehouseName' value="暂无仓库">
        <?php endif; ?>
        <input type='hidden' id='data-goodstitle' value="<?= $goods['title'] ?>">
        <input type='hidden' id='data-goodsImg' value="<?= $goods['thumb'] ?>">
        <input type='hidden' id='data-marketprice' value="<?= $goods['marketprice'] ?>">
        <input type='hidden' id='data-total' value="<?= $goods['total'] ?>">
        <?php endif; ?>
    </ul>
</div>

<div class="window">
    <div class="spinner-box">
        <h3>数量选择：</h3>
        <div class="spinner">
        <?php if ($goods['maxbuy']==0) :?>
            <span class="decrease" onclick='disNum()'></span>
        <?php endif; ?>
            <input type="text" class="spinnerExample value" id='res' value="1" maxlength="2">
        <?php if ($goods['maxbuy']==0) :?>
            <span class="increase" onclick="incNum()"></span>
        <?php endif; ?>
        </div>
    </div>
    <ol>
        <li>
            <button id='cancel'>取消</button>
        </li>
        <li id='submit'>
            <button data-goodsid='<?= $goods['id'] ?>' class="btn_add_cart">确认</button>
            <span></span></li>
    </ol>
</div>
<script>
    $("#cancel").click(function () {
        $(".window").css("display", "none");
    });
    //购物车数量累加累减
    function incNum() {
        var total = $('#data-total').val();
        ;
        var str = $("#res").val();
        var num = parseInt(str) + parseInt("1");
        if (num <= total) {
            $("#res").val(num);
        }
    }
    function disNum() {
        var str = $("#res").val();
        var num = parseInt(str) - parseInt("1");
        if (num >= 1) {
            $("#res").val(num);
        }
    }
</script>
<?php
/***********************
 * 加入购物车操作流程：
 * 1、判断localstroage是否有效
 * 2、上一条如果无效js写入cookie，否则写入localstorage
 * 3、判断用户登录情况，如果会话信息证明用户登录了，则发送一个ajax请求增加记录（服务器段执行replace语.句）
 * $.cookie('the_cookie', 'the_value', {expires: 30});
 ***********************/
?>

<script type="text/javascript">
wx.config({
    debug: false,
    appId: '<?=$signPackage['appId']?>',
    timestamp: <?=$signPackage['timestamp']?>,
    nonceStr: '<?=$signPackage['nonceStr']?>',
    signature: '<?=$signPackage['signature']?>',
    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage']
});
wx.ready(function () {
    wx.onMenuShareTimeline({
        title: '<?=$goods['title']?>', // 分享标题
        link: 'http://mall.troto.com.cn/goods/detail?id=<?=$goods['id']?>&uid=<?=$uid?>', // 分享链接
        imgUrl: '<?=$goods['thumb']?>?imageView2/2/w/128/h/128', // 分享图标
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({
        title: '<?=$goods['title']?>', // 分享标题
        desc: '<?=$goods['fdesc']?>', // 分享描述
        link: 'http://mall.troto.com.cn/goods/detail?id=<?=$goods['id']?>&uid=<?=$uid?>', // 分享链接
        imgUrl: '<?=$goods['thumb']?>?imageView2/2/w/128/h/128', // 分享图标
        type: 'link', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {
            // 用户确认分享后执行的回调函数
        },
        cancel: function () {
            // 用户取消分享后执行的回调函数
        }
    });
});
</script>

<script type="text/javascript">
$(function () {
    $(".btn_add_cart").click(function () {
        var goodsid = $(this).attr('data-goodsid');
        var goodsImg = $('#data-goodsImg').val();
        var goodstitle = $('#data-goodstitle').val();
        var marketprice = $('#data-marketprice').val();
        var wname = $('#warehouseName').val();
        var product = {
            id: goodsid,
            title: goodstitle,
            total: $("#res").val(),
            thumb: goodsImg,
            marketprice: marketprice,
            name: wname,
            specifications: $('.selected ').text()
        };

        if ($.cookie('cart') === undefined) {
            var cart = [];
        } else {
            var cart = JSON.parse($.cookie('cart'));
        }

        var no_in_cart = true;
        $.each(cart, function (i, item) {
            if (item.id == product.id) {
                no_in_cart = false;
                item.total = $("#res").val();
                item.specifications = $('.selected').text();
            }
        });

        if (no_in_cart) {
            cart.push(product);
        }

        if ($('#userInfo').val() != 0) {
            $.get("/cart/addcart", {
                    op: "add",
                    total: $("#res").val(),
                    id: goodsid,
                }, function (result) {
                    if (result.msg == true) {
                        $('.window').css('display', 'none');
                        $('#data').text(result.num);
                    } else if (result.update_msg == false) {
                        alert('该商品信息已存在购物车，请重选规格或数量。');
                    } else if (result.msg == false) {
                        alert('网络错误！');
                    } else if(result.parameter==true){
                        alert('参数错误');
                    } else if(result.flash==false){
                        alert('加入购物车失败，库存不足。');
                    } else if(result.flash==true){
                        alert('活动结束');
                    } else if(result.activity==false){
    					alert('活动未开始');
    				}

                }, 'json');
        } else {
            $.cookie('cart', JSON.stringify(cart), {path: '/', expires: 7});
            $('#data').text(JSON.parse($.cookie('cart')).length);
            $('.window').css('display', 'none');
        }
    });
});

function isCollect() {
    var id = $('#goods-id').val();
    $.get('/collect/is-in?id=' + id, function (result) {
        if (result.is_in) {
            if(result.is_in==true){
                $('#icon span').removeClass('icon-Collection');
                $('#icon span').html("<img src='/images/xingxing.png' />");
            }
            $('#is-collect').html('已收藏');
        } else {
            $('#is-collect').html('收藏');
        }
    }, "JSON");
}

function isInCart() {
    var id = $('#goods-id').val();
    $.get('/cart/is-in?id=' + id, function (result) {
        if (result.is_in) {
            $('#cartState').html('已加入购物车')
        } else {
            $('#cartState').html('加入购物车')
        }
    }, "JSON");
}

function getCartNum() {
    $.get('/cart/get-num', function (result) {
        $('#data').html(result.cart_num);
    }, "JSON");
}
function collect(id) {
    $.get('/collect/like?id='+id,  function (result) {
        if (result.success) {				
			if(result.success==true && result.add==true){
				$('#icon span').removeClass('icon-Collection');
				$('#icon span').html("<img src='/images/xingxing.png' />");
			}else{
				$('#icon span img').remove();
				$('#icon span').addClass('icon-Collection');
			}
            $('.states').text((result.add ? '已收藏' : '收藏'));
        } else {
            alert(result.msg);
        }
    }, "JSON");
}
</script>

<script type="text/javascript">
$(function () {
    // 选项卡
    $("#blush li").click(function () {
        $(this).addClass("tabin").siblings().removeClass("tabin");
        var index = $(this).index();
        $(".nav-list").hide();
        $(".nav-list").eq(index).show();
    })
    <?php if ($goods['total']>0):?>
    $(".join").click(function () { $(".window").css("display", "block"); });
    $(".immediately").click(function () {
        $(".window").css("display", "block");
        $('#submit').html("<button data-goodsid='<?= $goods['id'] ?>' class='immediately'>确认</button>");
        $(".immediately").click(function () {
            var total = $("#res").val();
            window.location.href = "/immediately/pay?total=" + total + "&id=" +<?= $goods['id'] ?>
        });
    });
    <?php endif; ?>

    //商品规格选择
    $(".sys_item_spec").each(function () {
        var i = $(this);
        var p = i.find("ul>li");
        p.click(function () {
            if (!!$(this).hasClass("selected")) {
                $(this).removeClass("selected");
                i.removeAttr("data-attrval");
            } else {
                $(this).addClass("selected").siblings("li").removeClass("selected");
                i.attr("data-attrval", $(this).attr("data-aid"))
            }
        });
    });
});

$(function () {
    isCollect();
    <?php if ($goods['total']>0):?>isInCart();<?php endif; ?>
    getCartNum();
});
</script>