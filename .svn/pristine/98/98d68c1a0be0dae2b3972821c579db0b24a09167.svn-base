<!DOCTYPE html>
<html lang="ZH-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="选择性别"/>
    <meta name="description" content="我的个人资料"/>
    <meta name="author" content="云吉(www.rikee.com)"/>
    <meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1; user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="msapplication-tap-highlight" content="no">
    <title>选择性别</title>
    <link rel="stylesheet" href="/css/global.css">
    <link rel="stylesheet" href="/css/gender.css">
    <link rel="stylesheet" href="/css/demo-files/demo.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/mediaelementplayer.css"/>
    <link rel="stylesheet" href="/css/mejs-skins.css"/>
    <script type="text/javascript" src="http://libs.baidu.com/jquery/1.8.3/jquery.min.js"></script>
</head>

<body>
<div class="gender-box">
    <h1>选择您的性别<br>为你展现更好的产品</h1>
    <ul id="gender">
        <li id="gender1">
            <h2>女士</h2>
            <a href="##">Women</a>
            <span id="select01"></span>
        </li>
        <li id="gender2">
            <h2>男士</h2>
            <a href="##">Men</a>
            <span id="select02"></span>
        </li>
    </ul>

    <div class="next">
        <button id="btn_next">下一步</button>
        <a href="#">（1/2）</a>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var man = $("#select01");
        var lady = $("#select02");

        $("#gender1").click(function () {
            lady.removeClass('select02');

            if (man.hasClass("select01")) {
                man.removeClass("select01");
            } else {
                man.addClass("select01");
            }
        });

        $("#gender2").click(function () {
            man.removeClass('select01');

            if (lady.hasClass("select02")) {
                lady.removeClass("select02");
            } else {
                lady.addClass("select02");
            }
        });

        $("#btn_next").click(function () {
            if (man.hasClass("select01") || lady.hasClass("select02")) {
                var sex = man.hasClass("select01") ? 1 : 0;
                window.location.href = "/register/interest?sex=" + sex;
            } else {
                alert('请选择您的性别！');
            }
        });
    });
</script>
</body>
</html>