<!DOCTYPE html>
<html lang="ZH-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="keywords" content="选择您的爱好"/>
    <meta name="description" content="选择您的爱好"/>
    <meta name="author" content="云吉(www.rikee.com)"/>
    <meta name="viewport" content="width=device-width; initial-scale=1; maximum-scale=1; user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black"/>
    <meta content="telephone=no" name="format-detection"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <meta name="msapplication-tap-highlight" content="no">
    <title>选择您的爱好</title>
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
    <h1>选择您的爱好<br>为你展现更好的产品</h1>
    <ul id="hobby">
        <?php foreach ($category as $c): ?>
            <li>
                <a href="#">
                    <span></span><br>
                    <p catId="<?= $c->id ?>"><?= $c->name ?><i></i></p>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="next">
        <button id="btn_next">下一步</button>
        <a href="#">（2/2）</a>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $("#hobby li").click(function () {
            var hobby = $(this).find('i');

            if (hobby.hasClass('black')) {
                hobby.removeClass('black');
            } else {
                hobby.addClass('black');
            }
        });

        $("#btn_next").click(function () {
            var my_hobby = $('#hobby li i.black');

            if (my_hobby.length === 0) {
                alert('请至少选择一个产品类别！');
                return false;
            }

            var catId = [];
            
            $.each(my_hobby, function (i, hobby) {
                catId.push($(hobby).parent().attr('catId'));
            });

            window.location.href = '/register/interest-submit?hobby=' + catId.join(',');
        });
    });
</script>

</body>
</html>