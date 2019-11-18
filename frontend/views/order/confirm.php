<?php $this->title = '提交订单'; ?>

<style type="text/css">
.main-content > li, .main-content > li:nth-child(n+2) {border: none;}
.main-content {padding: 0;}
.main-content > li {padding: 10px;}
#personal-ul > li:nth-child(3) {
    margin-top: 0;
    border-top: #e3e2e0 solid 0;
}
.discount-box > ul > li:nth-child(1) a {
    line-height: 50px;
}
.discount-box {
    margin-top: 10px;
    padding: 10px 0 0 0;
    display: none;
}
.discount-box > ul > li:nth-child(3) button {
    border: none;
    display: inline-block;
    line-height: 20px;
    border-radius: 3px;
    background-color: #fff;
}
.btn-top {margin-top: 5px;}
.discount-box > ul > li:nth-child(2) span {line-height: 12px;}
.convert-btn {
    width: 60%;
    margin-left: 20%;
    color: #fff;
}
</style>
<?php if (empty($products)){ ?>
    <div style="text-align: center;padding:3em;">商品为空，无法下单！</div>
    <div class="payment"><button onclick="order_create();">回购物车</button></div>
    <script>
    function order_create() { window.location.href = '/cart'; }
    </script>
<?php } else { ?>
    <?php if (count($addresses) > 0): ?>
        <div class="location-affirm" data-address-id="<?= $addresses[0]['id']?>">
            <span class="icon-position"></span>
            <ul class="location">
                <li>收货人：<p class="realname"><?= $addresses[0]['realname'] ?></p> <span class="mobile"><?= $addresses[0]['mobile'] ?></span></li>
                <li class="address_detail">
                    收货地址：<?= $addresses[0]['province'] . ' ' . $addresses[0]['city'] . ' ' . $addresses[0]['area'] . ' ' . $addresses[0]['address'] ?></li>
            </ul>
        </div>
    <?php endif; ?>

    <ol class="main-content">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <li style="display:none;"><h2>暂无仓库</h2></li>
                <li>
                    <ol class="content-tmain">
                        <li><a href="/goods/detail?id=<?= $product['id'] ?>"><img src="<?= $product['thumb'] ?>"></a></li>
                        <li style="line-height: 18px; width: 40%">X<?= $product['total'] ?></li>
                        <li style="line-height: 18px;width: 40%">￥<?= number_format($product['marketprice'] * 1, 2); ?></li>
                        <li style="padding-top:10px; text-align: left; width: 77%"><?= $product['title'] ?></li>
                    </ol>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ol>

    <ul id="personal-ul">
<!-- 
        <li style="padding: 10px 15px 10px 15px;display: <?= count($valid_coupons) > 0 ? 'block' : 'none' ?>;">
            <input type="hidden" value="" id="coupon-id">
            <a href="javascipt:;" id="disco">
                <h3>使用优惠券</h3>
            </a>

            <div class="discount-box">
                <?php if (count($valid_coupons) > 0): ?>
                    <?php foreach ($valid_coupons as $idx => $c): ?>
                        <ul class="coupon-ticket">
                            <li><a href="#">￥<?= $c['value'] ?></a></li>
                            <li>
                                <p>(剩余 <?= $c['total'] ?> 张)</p>
                                <h3>无条件使用</h3>
                                <span>截止时间：<?= date('Y-m-d', $c['expire_end']) ?></span>
                            </li>

                            <li class="apply-coupon-btn" type_id="1">
                                <button coupon-id="<?= $c['id'] ?>" class="use-coupon-btn">立即使用</button>
                            </li>
                        </ul>

                        <?php if ($idx < (count($valid_coupons) - 1)): ?>
                            <div class="line"></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (count($status_coupons[0]) === 0): ?>
                    <div style="text-align: center;margin-top: 2em; margin-bottom: 2em;">
                        <a href="/coupon/exchange" class="convert-btn" style="width: 60%;margin-left: 20%;color: #fff;">优惠券兑换</a>
                    </div>
                <?php endif; ?>
            </div>
        </li>
 -->
        <li><a href="##">
                <h3>支付方式</h3>
                <p>微信支付<!-- <span class="fxspa" style="display:none;"></span> --></p>
            </a>
        </li>
        <?php if ($taxtotal):?>
        <li><a href="##">
                <h3>进口税费</h3>
                <p>￥<?=number_format($taxtotal, 2)?></p>
            </a>
        </li>
        <?php endif; ?>
        <li><h3>应付总额</h3></li>
    </ul>

    <div class="payment">
        <ul>
        <!-- 
            <?php if ($discount): ?>
               <li>新人首次支付省￥<?= $discount ?></li>
            <?php endif; ?>
        -->
            <li>￥<?= number_format(($sumPrice-$discount+$taxtotal), 2) ?>（含运费）</li>
            <li style="display:none;"><a href=""><span class="icon-warning"></span>点击查看价格详情</a></li>
        </ul>
<!--
        <a href="#" id="rule">
            <label for=""><input type="checkbox"></label>
            本人接受《进口个人申请委托》与《十点一刻服务协议》<span class="fxspa"></span>
        </a>
-->
        <div class="messlist">
            <label>备注信息</label>
            <textarea placeholder="备注内容" id="order-remark"></textarea>
        </div>
        <button onclick="order_create();">提交订单</button>
    </div>
    <!-- 选择收货地址 -->
    <div class="pos none selectAddress">
        <div class="possess">
            <div class="location-box location-box2">
                <h2>请选择收货地址</h2>
            </div>
        </div>
    </div>

    <script>
    $(function () {
        $("#disco").click(function () {
            $(".discount-box").slideToggle("slow");
        });

        $('.use-coupon-btn').click(function () {
            $('.coupon-ticket').hide();
            $('.line').hide();

            $(this).parent().parent().show();
            $('#coupon-id').val($(this).attr('coupon-id'));
            $(this).parent().html('已使用');
        });

        // 选择收货地址
        $(".location-affirm").click(function(){
            var $parent = $(".selectAddress"), $selectAddress = $parent.find(".location-box");
            $parent.show();
            $.ajax({
                url: '/member/get-address',
                type:'post',
                dataType:'json',
                success: function(data) {
                    if(data.success == 1){
                        $selectAddress.find("ul").remove();
                        $.each(data.data, function (key, row){
                            $selectAddress.append('<ul class="location getAddressIdByShopping" data-address-id="'+row.id+'"><li>收货人：<p class="realname">'+row.realname+'</p> <span class="mobile">'+row.mobile+'</span> <a class="addBor" href="#"></a></li><li>收货地址：<p class="address_detail">'+row.province+' '+row.city+' '+row.area+' '+row.address+'</p></li></ul>')
                        });
                    }else{
                        alert("收货地址不存在");
                    }
                }
            });
        });

        // 获取收货地址
        $(".selectAddress").delegate(".getAddressIdByShopping", "click", function(){
            var $this            = $(this);
            var $location_affirm = $(".location-affirm");
            $this.find(".addBor").addClass("bor");
            $location_affirm.attr("data-address-id", $this.data("address-id")); 
            $location_affirm.find(".realname").text($this.find(".realname").text());
            $location_affirm.find(".mobile").text($this.find(".mobile").text());
            $location_affirm.find(".address_detail").text("收货地址："+$this.find(".address_detail").text());
            $(".selectAddress").hide();
        });
    });
    
    function order_create() {
        var odata = {
            '_csrf':$('meta[name="csrf-token"]').attr("content"),
            'couponid':$('#coupon-id').val(),
            'addressid':$('.location-affirm').data("address-id"),
            'remark':$("#order-remark").val()
        };

        $.post("/order/create", odata, function(res){
            if (res.code==200) {
                window.location.href = '/order/pay?order_id='+res.data.orderid;
            } else {
                alert("网络发生错误，提交订单失败！");
            }
            return false;
        }, "JSON");
    }
    </script>
<?php } ?>