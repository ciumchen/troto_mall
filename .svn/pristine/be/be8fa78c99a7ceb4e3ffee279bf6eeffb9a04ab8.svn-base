<?php
use yii\helpers\Html;
$this->title = $goods['title'];
?>
<style type="text/css">
.row {margin-top: -25px;}
.indexbottom { padding-bottom:0px; }
.col-title { border-left:5px solid #00A0EA; }
.goods-content img{width:100%;}
</style>
<div class="row">
    <div class="col-xs-12" style="padding:0; padding-top:.5rem">
        <img src="<?=$goods['thumb']?>" class="img-thumbnail">
        <div class="col-xs-9"><h4><?=$goods['title']?></h4></div>
        <div class="col-xs-3" style="font-size:18px; color:red; font-weight:bold;">￥<?=$goods['marketprice']?></div>
        <div class="col-xs-4">品牌：<?=$goods['brand']?></div>
        <div class="col-xs-4">已售：<?=$goods['sales']?></div>
        <div class="col-xs-4">库存：<?=$goods['total']?></div>
        <div class="col-xs-4">原价：<span style="text-decoration:line-through;">￥<?=$goods['productprice']?></span></div>
        <div class="col-xs-8">机柜：前海一号智能柜</div>
    </div>

    <?php if($goodsParam):?>
    <!--参数-->
    <div class="col-xs-12 clearPadding bgf1">
        <div class="col-title mtb10 clearfix"><span class="font16">轮胎技术参数</span></div>
        <div style="padding:5px 20px">
            <table class="table table-bordered" style="background-color:#fefafa;">
              <!-- <thead><tr><th>参数名称</th><th>参数值</th></tr></thead> -->
              <tbody>
              <?php foreach ($goodsParam as $param):?>
                <tr><th style="text-align:right; width:35%;"><?=$param['title']?></th><td><?=$param['value']?></td></tr>
              <?php endforeach;?>
              </tbody>
            </table>
        </div>          
    </div>
    <?php endif;?>

    <div class="col-xs-12 clearPadding bgf1" style="padding-bottom:40px;">
        <div class="col-title mtb5"><span class="font16">轮胎详情</span></div>
        <div class="goods-content" style="padding:5px"><?=$goods['content']?></div>
    </div>

    <!-- goods detail bar -->
    <div class="col-xs-12 clearPadding bottoms indexbottom">
        <div class="col-xs-4 text-center ">
            <a href="/" style="display:block; float:left; width:45%;">
                <i class="iconfont text-blue"></i>
                <div>首页</div>
            </a>
            <a href="/cart">
                <i class="iconfont text-muted"></i>
                <span id="cartbar" class="badge" style="padding:2px; position:fixed;"></span>
                <div>购物车</div>
            </a>
        </div>
        <div class="col-xs-4 text-center" style="background-color:#ff6400; color:#fff; height:42px; padding-top:10px;">
            <a style="color:#fff;" href="##">加入购物车</a>
        </div>
        <div class="col-xs-4 text-center" style="background-color:#f55; color:#fff; height:42px; padding-top:10px;">
            <a style="color:#fff;" href="##">立即购买</a>
        </div>
    </div>
</div>

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

</script>