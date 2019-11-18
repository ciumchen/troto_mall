<?php
namespace frontend\models;
use Yii;

require_once "../wxpay/lib/WxPay.Api.php";
require_once "../wxpay/example/WxPay.JsApiPay.php";

//①、获取用户openid
$tools = new JsApiPay();
//$openid = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetTotal_fee($orderPayTotal * 100);
//$input->SetTotal_fee(1);

/*if ($isFirstOrder) {
    $input->SetTotal_fee($order->price * 100);
} else {
    $input->SetTotal_fee($order->goodsprice * 100);
}*/
$input->SetBody($goods_name);  //商品名称
$input->SetAttach("Attach");
$input->SetGoods_tag("goods_tag");
$input->SetNotify_url("http://mall.troto.com.cn/wechat/wxpay-notify");
$input->SetOut_trade_no($order->ordersn);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openid);

$wxorder = WxPayApi::unifiedOrder($input);
$jsApiParameters = $tools->GetJsApiParameters($wxorder);

//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();
?>

<?php
echo Yii::$app->view->render('pay_detail_2', [
    'order'       => $order,
    'goods_name'  => $goods_name,
    'coupon'      => $coupon,
    'order_price' => $order_price,
    'taxtotal'    => $taxtotal,
    'isFirstOrder'=> $isFirstOrder
]);
?>

<script>
    //调用微信JS api 支付
    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $jsApiParameters; ?>,
            function (res) {
                WeixinJSBridge.log(res.err_msg);

                if (res.err_msg == "get_brand_wcpay_request:ok") {
                    window.location = '/order/paid?order_id=<?=$order->id?>';
                } //else {
                //window.location = '/order/paid-error?order_id=<?=$order->id?>';
                //}

                //注1：
                //JS API的返回结果get_brand_wcpay_request：ok仅在用户成功完成支付时返回。
                //由于前端交互复杂，get_brand_wcpay_request：cancel或者get_brand_wcpay_request：fail
                //可以统一处理为用户遇到错误或者主动放弃，不必细化区分。

                //注2：
                //使用以上方式判断前端返回,微信团队郑重提示：res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                //因此微信团队建议，当收到ok返回时，向商户后台询问是否收到交易成功的通知，若收到通知，前端展示交易成功的界面；
                //若此时未收到通知，商户后台主动调用查询订单接口，查询订单的当前状态，并反馈给前端展示相应的界面。
            }
        );
    }

    function callpay() {
        var ua = window.navigator.userAgent.toLowerCase();

        if (ua.match(/MicroMessenger/i) != 'micromessenger') {
            $('body').html('请使用微信打开页面！');
        } else {
            jsApiCall();
        }
    }

    //获取共享地址
    function editAddress() {
        WeixinJSBridge.invoke(
            'editAddress',
            <?php echo $editAddress; ?>,
            function (res) {
                var value1 = res.proviceFirstStageName;
                var value2 = res.addressCitySecondStageName;
                var value3 = res.addressCountiesThirdStageName;
                var value4 = res.addressDetailInfo;
                var tel = res.telNumber;

                //alert(value1 + value2 + value3 + value4 + ":" + tel);
            }
        );
    }

    window.onload = function () {
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                //document.addEventListener('WeixinJSBridgeReady', editAddress, false);
            } else if (document.attachEvent) {
                //document.attachEvent('WeixinJSBridgeReady', editAddress);
                //document.attachEvent('onWeixinJSBridgeReady', editAddress);
            }
        } else {
            //editAddress();
        }
    };
</script>