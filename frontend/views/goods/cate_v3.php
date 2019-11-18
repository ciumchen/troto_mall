<?php
use yii\helpers\Html;

$request = Yii::$app->request;
$route_name = $this->context->request->getPathInfo();
if ($categoryId) {
    $this->title = $categoryName;
} else {
    $this->title = '商品分类';
}

$this->title .= ' - '.Yii::$app->params['site_name'];
?>
<link rel="stylesheet" type="text/css" href="/v3/css/goods-list.css">

<div class="goods_nav">
    <ul class="goods_title">
        <li><a href="/goods/cate" <?php if($categoryId==0):?>class="active"<?php endif;?>>全部</a></li>
    <?php foreach ($pcate as $pcateone): ?>
        <li><a <?php if($categoryId==$pcateone['id']):?>class="active"<?php endif;?> href="/goods/cate?id=<?=$pcateone['id']?>"><?=$pcateone['name']?></a></li>
    <?php endforeach; ?>
    </ul>

    <div class="nav_list2">
    <?php if (!empty($ccate)): ?>
        <ul class="nav_two" id="sub-nav">
        <?php foreach ($ccate as $ccateItem): ?>
            <?php if($categoryId==$ccateItem['id']):?>
                <li class="active_border"><a class="active" href="/goods/cate?id=<?=$ccateItem['id']?>"><?=$ccateItem['name']?></a></li>
            <?php else: ?>
                <li><a href="/goods/cate?id=<?=$ccateItem['id']?>"><?=$ccateItem['name']?></a></li>
            <?php endif;?>
        <?php endforeach; ?>
            <div class="clear"></div>
        </ul>
    <?php endif; ?>
        <input type="hidden" value="0" id="limitPage">
        <div class="goods_list">
            <ul id="goods-list"><div class="clear" id="goods-list-clear"></div></ul>
            <div class="list-load tite2" >正在加载更多...</div>
        </div>
    </div>
</div>

<?php include '../views/layouts/wxshare.php';?>

<script type="text/javascript">
function requestListMore(p){
    $.ajax({url:"/goods/cate-data", dataType: "json", type:'post', data:'id=<?=$categoryId?>&page='+p, success:function (result) {
        var row=fav=newgoods=hotgoods=discoutgoods='';
        if (result.total) {
            $(".list-load").css('display','block');
            $(".list-load").html("<img src='/images/loading.gif' style='width: 20px;'>");
            $("#limitPage").val(result.page);
            $.each(result.data, function(i, n){
                fav='common_gray';
                row=newgoods=hotgoods=discoutgoods='';
                if (n.fav) { fav='common_red'; }
                if (n.isnew>0) { newgoods='<div class="spaci_new iconfont icon-xinpinshangjia"></div>';}
                if (n.ishot>0) { hotgoods='<div class="spaci_new iconfont icon-19baokuan"></div>';}
                if (n.isdiscount>0) { discoutgoods='<div class="spaci_spac iconfont icon-tagtejia"></div>';}
                row = '<li class="list_li">'+discoutgoods+'<div class="bao_right">'+newgoods+hotgoods+'<div class="clear"></div></div><div class="goods_img"><a href="/goods/detail?id='+n.id+'"><img class="goods_pro" src="'+n.thumb+'"></a><span class="collect '+fav+'"></span></div><div class="goods_intro"><a href=""><p><a href="/goods/detail?id='+n.id+'">'+n.title+'</a></p></a><span class="goods_price">￥'+n.marketprice+'</span><a href="/goods/detail?id='+n.id+'"><span class="shop_car car_red"></span></a></div></li>';
                $("#goods-list-clear").before(row);
            });
            $(".list-load").html("");
        } else {
            $(".list-load").html("没有更多");
            return false;
        }
    }
    });

    
}
// 收藏按钮点击事件
$('#goods-list').on('click','span.collect',function(){
    var red='common_red',gray='common_gray'; 
    $(this).hasClass(red) ? $(this).removeClass(red).addClass(gray) : $(this).removeClass(gray).addClass(red);
});


function loadataProduct() {
    //浏览器的高度加上滚动条的高度
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
    totalheight += 502;
    // var tite2 = $('.tite2').text();
    //当文档的高度小于或者等于总的高度的时候，开始动态加载数据
    if ($(document).height() <= totalheight) {
		$.ajaxSetup({
            async : false //这是重要的一步，防止重复提交的
		});
        //var page = $('.limit').val();
	    var num = '';
	    for(var i=1;i<$('#goods-list li').length;i++){ num = i; }
 	    var page = Math.ceil((num+1) / 8);
        if ($("#limitPage").val()>page) requestListMore(page);
        else  $(".list-load").html("没有更多商品了~");
    }
}

$(function(){ requestListMore(0); });
//滚动加载
$(window).scroll( function() { loadataProduct(); })
</script>