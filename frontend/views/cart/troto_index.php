<?php $this->title = '购物车 - '.Yii::$app->params['site_name'];?>
<script type="text/javascript" src="/js/lib/jquery.cookie.js"></script>
<style type="text/css">
h1, h2, h3, h4, h5, h6 { font-weight:normal; }
input[type="checkbox"] { -webkit-appearance:none; outline:none; }
.shopcart-container { clear:both; overflow:hidden; height:auto; padding-bottom:60px; background:#f5f5f5; }
.shop-group-item { background: #fff; margin: 10px; margin-bottom: 5px; border-radius: 5px; border: 1px #00a2ea solid; }
.shop-group-item ul { padding:5px }
.shop-group-item ul li { border-bottom: 1px dotted #ccc; }
.shop-name { height: 35px; line-height: 35px; padding: 0 15px; position: relative; }
.shop-name h4 { float: left; font-size: 15px; margin-left: 20px; }
input.check { background: url(/images/icon_radio.png) no-repeat center left; background-size: 20px 20px; position: absolute; top:25%; left: 10px;  argin-top: -18px; width: 20px; height: 35px; }
input.check:checked { background:url(/images/icon_radio_checked.png) no-repeat center left; background-size:20px 20px; }
.shop-name .del { float: right;}
.shop-name .del span { display: inline-block; padding: 0 5px; }
.shop-info { height: 120px; position: relative; }
.shop-info .shop-info-img { position: absolute; top: 15px; left: 35px; width: 90px; height: 90px; }
.shop-info .shop-info-text { margin-left: 130px; padding: 15px 0; }
.shop-info .shop-info-text h4 {
    font-size: 14px;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
    overflow: hidden;
    line-height: 1.2;
}
.shop-info .shop-info-text .shop-brief { height: 25px; line-height: 25px; font-size: 12px; color: #81838e; white-space: nowrap; }
.shop-info .shop-info-text .shop-price { height:24px; line-height:24px; position:relative; }
.shop-info .shop-info-text .shop-brief span { display:inline-block; margin-right:8px; }
.shop-info .shop-info-text .shop-price .shop-pices { color:red; font-size:16px;}
.shop-info .shop-info-text .shop-arithmetic { position:absolute; right:52px; top:0; width:84px; box-sizing:border-box; white-space:nowrap; height:100%; border:1px solid #e0e0e0; border-radius:5px; }
.shop-info .shop-info-text .shop-arithmetic a { display:inline-block; width:23px; height:22px; line-height:22px; color:#fff; font-size:16px; font-weight:bolder; text-align:center; background:#00a2ea; }
.shop-info .shop-info-text .shop-arithmetic .minus { border-right:1px solid #e0e0e0; }
.shop-info .shop-info-text .shop-arithmetic .num { width:40px; text-align:center; border:none; display:inline-block; height:100%; box-sizing:border-box; vertical-align:top; margin:0 -6px; }
.shop-info .shop-info-text .shop-arithmetic .plus { border-left:1px solid #e0e0e0; }
.shopPrice { background:#fff; height:35px; line-height:35px; padding:0 15px; margin:auto 5px; text-align:right; }
.payment-bar { clear:both;overflow:hidden;width:100%;height:49px;position:fixed;bottom:0;border-top:1px solid #e0e0e0;background:#fff; }
.payment-bar .shop-total {
    float: left;
    -webkit-box-flex: 1.0;
    box-flex: 1.0;
    margin: 9px 20px 9px 35px;
}
.payment-bar .shop-total strong { display:block; font-size:16px; }
.payment-bar .shop-total span { display:block; font-size:12px; }
.payment-bar .settlement { display:inline-block; float:right; width:100px; height:49px; line-height:49px; text-align:center; color:#fff; font-size:16px; background:#00a2ea;}
 a.del{display: inline-block; width: 28px; height: 22px; line-height: 22px; color: #fff; font-size: 16px; font-weight: bolder; text-align: center; background: #00a2ea;}
</style>
<div class="row">
    <div class="shopcart-container">
        <?php foreach ($cartData as $cartCabinet): ?>
        <div class="shop-group-item">
            <div class="shop-name">
                <input type="checkbox" class="check goods-check shopCheck" data-shopid="<?=$cartCabinet[0]['cabinetid']?>" style="top:unset;">
                <h4><?=$cartCabinet[0]['addr_city'].$cartCabinet[0]['addr_dist'].' '.$cartCabinet[0]['name']?></h4>
                <div class="del"><!--<span>删除</span><span class="shop-total-amount ShopTotal">0</span>--></div>
            </div>
            <ul>
            <?php foreach ($cartCabinet as $cartItem): ?>
                <li>
                  <div class="shop-info">
                    <input type="checkbox" class="check goods-check goodsCheck" data-cartid="<?=$cartItem['cartid']?>" data-goodsid="<?=$cartItem['goodsid']?>" data-shopid="<?=$cartCabinet[0]['cabinetid']?>">
                    <div class="shop-info-img"><a href="#"><img src="<?=$cartItem['thumb1']?>" class="img-thumbnail" style="width:100%"></a></div>
                    <div class="shop-info-text">
                        <h4><?=$cartItem['title']?></h4>
                        <div class="shop-brief"><?=$cartItem['description']?></div>
                        <!-- <div class="shop-brief"><span>花纹：L819r</span><span>规格：9R20 16pr</span></div> -->
                        <div class="shop-price">
                            <div class="shop-pices">￥<b class="price" style="font-size:14px;"><?=$cartItem['price']?></b></div>
                            <div class="shop-arithmetic">
                                <a href="javascript:;" class="minus" data-cartid="<?=$cartItem['cartid']?>">-</a>
                                <span class="num" data-maxbuy="<?=$cartItem['stock']?>"><?=$cartItem['total']?></span>
                                <a href="javascript:;" class="plus" data-cartid="<?=$cartItem['cartid']?>">+</a>
                            </div>
                            <div style="position: absolute; right: 5px; top: 0; width: 28px; box-sizing: border-box; white-space: nowrap; height: 100%; border: 1px solid #e0e0e0; border-radius: 5px;"><a href="javascript:;" class="del" data-cartid="<?=$cartItem['cartid']?>">×</a></div>
                        </div>
                    </div>
                  </div>
                </li>
            <?php endforeach;?>
            </ul>
            <div class="shopPrice">小计：￥<span class="shop-total-amount ShopTotal">0.00</span></div>
        </div>
        <?php endforeach;?>
    </div>

    <div class="payment-bar">
        <div class="shop-total">
            <strong>总价：<i class="total" id="AllTotal">0.00</i></strong>
            <span>减免优惠：0.00</span>
        </div>
        <a href="#" id="submitOrder" class="settlement">去结算</a>
    </div>
</div>
<?php include '../views/layouts/TrotoWxshare.php';?>

<script>
$(function(){
    var _csrf = $('meta[name="csrf-token"]').attr("content");
    function sumTotalPrice() {
        var allprice = 0; //总价
        $(".shop-group-item").each(function() { //循环每个店铺
          var oprice = 0; //店铺总价
          $(this).find(".goodsCheck").each(function() { //循环店铺里面的商品
            if ($(this).is(":checked")) { //如果该商品被选中
              var num = parseInt($(this).parents(".shop-info").find(".num").text()); //得到商品的数量
              var price = parseFloat($(this).parents(".shop-info").find(".price").text()); //得到商品的单价
              var total = price * num; //计算单个商品的总价
              oprice += total; //计算该店铺的总价
            }
            $(this).closest(".shop-group-item").find(".ShopTotal").text(oprice.toFixed(2)); //显示被选中商品的店铺总价
          });
          var oneprice = parseFloat($(this).find(".ShopTotal").text()); //得到每个店铺的总价
          allprice += oneprice; //计算所有店铺的总价
        });
        $("#AllTotal").text(allprice.toFixed(2)); //输出全部总价
    }

    $(".minus").click(function() {
        var t = $(this).parent().find('.num');
        if (t.text()>1){
            $.post("/cart/edit", {"cartid":$(this).attr("data-cartid"),'total':(parseInt(t.text())-1), _csrf:_csrf}, function(result){
                if (result.code==2000) {
                    t.text(parseInt(t.text())-1);
                    if (t.text() <= 1) { t.text(1); }
                    sumTotalPrice();
                }
            });
        }
    });
    $(".plus").click(function() {
        var t = $(this).parent().find('.num');
        if (parseInt(t.text())<parseInt(t.attr("data-maxbuy"))) {
            $.post("/cart/edit", {"cartid":$(this).attr("data-cartid"),'total':(parseInt(t.text())+1), _csrf:_csrf}, function(result){
                if (result.code==2000) {
                    t.text(parseInt(t.text())+1);
                    sumTotalPrice();
                }
            });
        }
    });
    $(".del").click(function() {
        function delCallback(that){
            return function(result){
                if (result.code==2000) {
                    that = that.parent().parent().parent().parent();
                    if (that.parent().length==1) that.parent().parent().parent().remove();
                    else  that.remove();
                    sumTotalPrice();
                }
            }
        }
        $.post("/cart/del", {"cartid":$(this).attr("data-cartid"), _csrf:_csrf}, delCallback($(this)));
    });

    //点击商品按钮
    $(".goodsCheck").click(function() {
        var goods = $(this).closest(".shop-group-item").find(".goodsCheck"); //获取本店铺的所有商品
        var goodsChecked = $(this).closest(".shop-group-item").find(".goodsCheck:checked"); //获取本店铺所有被选中的商品
        var Shops = $(this).closest(".shop-group-item").find(".shopCheck"); //获取本店铺的全选按钮
        Shops.parent().parent().prevAll().find("input[type=checkbox]").each(function(){ $(this).attr("checked", false) })
        Shops.parent().parent().nextAll().find("input[type=checkbox]").each(function(){ $(this).attr("checked", false) })
        if (goods.length == goodsChecked.length) { //如果选中的商品等于所有商品
            Shops.prop('checked', true); //店铺全选按钮被选中
            if ($(".shopCheck").length == $(".shopCheck:checked").length) { //如果店铺被选中的数量等于所有店铺的数量
              sumTotalPrice();
            } else {
              sumTotalPrice();
            }
        } else { //如果选中的商品不等于所有商品
            Shops.prop('checked', false); //店铺全选按钮不被选中
            sumTotalPrice();
        }
    });

    // 点击店铺按钮
    $(".shopCheck").click(function() {
        if ($(this).prop("checked") == true) { //如果店铺按钮被选中
          $(this).parents(".shop-group-item").find(".goods-check").prop('checked', true); //店铺内的所有商品按钮也被选中
          if ($(".shopCheck").length == $(".shopCheck:checked").length) { //如果店铺被选中的数量等于所有店铺的数量
            sumTotalPrice();
          } else {
            sumTotalPrice();
          }
        } else { //如果店铺按钮不被选中
          $(this).parents(".shop-group-item").find(".goods-check").prop('checked', false); //店铺内的所有商品也不被全选
          $("#AllCheck").prop('checked', false); //全选按钮也不被选中
          sumTotalPrice();
        }
    });

    $("#submitOrder").click(function(){
        var toShoppingCartGoods = [];
        $(".shop-group-item").find("input[type=checkbox]:checked").each(function() { 
            if ($(this).data("goodsid")!=undefined) {
                toShoppingCartGoods.push($(this).data("cartid"));
            }
        });
        //提交生成订单
        if (toShoppingCartGoods.length) {
            var form = $("<form method='post'></form>");
            var input;
            form.attr({"action":'/order/confirm'});
            input = $("<input type='hidden'>");
            input.attr({"name":"_csrf"});
            input.val(_csrf);
            form.append(input);
            $.each(toShoppingCartGoods, function (key,value) {
                input = $("<input type='hidden'>");
                input.attr({"name":'cartids[]'});
                input.val(value);
                form.append(input);
            });
            $(document.body).append(form);
            form.submit();
        }
    });
});

</script>