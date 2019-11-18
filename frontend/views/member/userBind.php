<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '微信用户绑定手机号 | 多轮多微服务';
?><!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <?php $this->head() ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="<?= Yii::$app->params['site_keywords'] ?>"/>
    <meta name="description" content="<?= Yii::$app->params['site_descript']?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="msapplication-tap-highlight" content="no">
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <meta http-equiv="x-dns-prefetch-control" content="on"/>
    <link rel="dns-prefetch" href="http://assets.troto.com.cn"/>
    <link href="/styles/user.bind.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="trt-flexView">
<div class="trt-scrollView">
    <div class="trt-ver-head">
        <div class="lowin-brand">
            <img src="/images/logo3.png" alt="logo">
        </div>
    </div>
    <div class="trt-ver-form">
        <!-- <h2>短信快捷登录</h2> -->
        <div class="trt-flex">
            <div class="trt-flex-box">
                <i class="icon icon-phone"></i>
                <input id="mobile" type="text" autocomplete="off" placeholder="手机号，必填">
            </div>
        </div>
        <div class="trt-flex">
            <div class="trt-flex-box">
                <i class="icon icon-code"></i>
                <input id="code" type="text" autocomplete="off" placeholder="验证码，必填">
            </div>
            <div class="trt-button-code">
                <input id="btnSendCode" type="button" class="btn btn-default" value="发送验证码" onclick="getValidCode()">
            </div>
        </div>
        <div class="trt-ver-button">
            <button onclick="binding()">绑定手机号</button>
        </div>
    </div>
    <div class="trt-login-box">
        <div class="trt-cell-box">
            <label class="trt-cell-right">
                <input type="checkbox" value="1" name="checkbox" checked="checked">
                <i class="cell-checkbox-icon"></i>
                <em>同意 <a href="##">多轮多微服务用户协议</a></em>
            </label>
        </div>
    </div>
</div>
</div>
<script type="text/javascript" src="//libs.baidu.com/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
    var phoneReg = /(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/;
    var count = 30;
    var InterValObj, curCount;
    function getValidCode() {
        curCount = count;
        var mobile = $.trim($('#mobile').val());
        if (!phoneReg.test(mobile)) {
            alert(" 请输入有效的手机号码");
            return false;
        }
        $.ajax({
            type: "POST",
            url: window.location.href,
            data:{'action':'getcode','mobile':mobile},
            dataType:'json',
            success: function(resp) {}
        });

        $("#btnSendCode").attr("disabled", "true");
        $("#btnSendCode").val( + curCount + "秒再获取");
        InterValObj = window.setInterval(SetRemainTime, 1000);
    }
    function SetRemainTime() {
        if (curCount == 0) {
            window.clearInterval(InterValObj);
            $("#btnSendCode").removeAttr("disabled");
            $("#btnSendCode").val("重新发送");
        }
        else {
            curCount--;
            $("#btnSendCode").val( + curCount + "秒再获取");
        }
    }

    function binding(){
        var mobile = $.trim($('#mobile').val()),
            code = $.trim($('#code').val());
        if (!phoneReg.test(mobile)) { alert(" 请输入有效的手机号码"); return false; }
        if (code>999999 || code<100000) { alert(" 请输入有效的验证码"); return false; }
        $.post(window.location.href, {mobile:mobile,code:code}, function(resp){
            if (resp.code==2000) { window.location.href = resp.data.redirect; }
            else { alert(resp.msg); return false;}
        }, "json");
    }
</script>
</body>
</html>
