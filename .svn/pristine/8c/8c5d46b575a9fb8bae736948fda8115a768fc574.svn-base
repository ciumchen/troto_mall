<?php
use yii\helpers\Html;

$request = Yii::$app->request;
$route_name = $this->context->request->getPathInfo();

$this->title = '商品分类 - '.Yii::$app->params['site_name'];
?>
<style>
.class-con{margin-left:80px;}
.class-search-form p { margin: 0 15px 0 95px;}

li .cate-active a { color:#fff; }
</style>

<div class="container container-fill">
    <?php if(!empty($advData)):?>
    <div class="head search-head">
    <?php $Indexs = 1; foreach($advData as $adv):?>
    	<?php if($Indexs > 0 && $Indexs <= 1):?>
    		<img src="<?php echo $adv['thumb']?>"  onclick="window.location.href='<?php echo $adv['link']?>'">
    	<?php endif;?>
    <?php $Indexs++; endforeach;?>
    </div>
    <?php endif;?>

    <div class="shopping-main">
        <div class="class-nav">
            <ul id="enro">
        	<?php if(!empty($category)){ ?>
        		<?php foreach($category as $catkey=>$type):?>
        		<?php $id[] = $type['id']?>
                <?php if ($type['id']==$categoryId): ?>
                    <li data-pcate="<?=$type['id']?>" style="background-color: #2a2a2a;"><a style="color: #fff;" href="/goods/cate?id=<?=$type['id']?>"><?=$type['name']?></a>
                    </li>
                <?php else: ?>
                    <li data-pcate="<?=$type['id']?>"><a href="/goods/cate?id=<?=$type['id']?>"><?=$type['name']?></a>
                    </li>
                <?php endif; ?>
        		<?php endforeach;?>
        	<?php } ?>
            </ul>
        </div>

        <form class="search-form class-search-form" action="/goods/q" method="get">
            <p>
                <input type="text" name="wd" value="" class="form-control" style="display: inline;" placeholder="搜索感兴趣的商品关键词">
                <button class="index-search-button search-form-btn" type="submit" id='form1'></button>
            </p>
        </form>

        <div class="class-con">
            <!-- 商品内容 -->
            <?php if($route_name=='goods/cate'):?>
        	<ul class="waress"></ul>
        	<div class="list-load" style="display: block; bottom: 5px;">没有商品了</div>
        	<?php else:?>
        	<ul class="waress" >
        			<?php if(!empty($search)):?>
                                <?php echo $search;?>
                            <?php else:?>
                                    <p class="without" style='margin-top: 10px;'>
        								抱歉，暂无与此相关商品<br>
        								您可以换个词在搜索
        							</p>
        							<?php if(!empty($tags)):?>
        						<div class="search-queries">
        							<h4>热门搜索</h4>
        							<ul style='padding-top:0px;'>
        								<?php foreach($tags as $tagsData):?>
        								<li onclick="window.location.href='<?=$tagsData['url']?>'"><?=$tagsData['tagname']?></li>
        								<?php endforeach;?>
        							</ul>
        						</div> 
        							<?php endif;?>
                        <?php endif;?>
        		</ul>
        		<?php if(!empty($search)):?><div class="list-load" style="display: block; bottom: 5px;">没有商品了</div><?php endif;?>
        	<?php endif;?>
                
        </div>
    </div>
</div>

<script type="text/javascript">
function loadataProduct() {
    //浏览器的高度加上滚动条的高度
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());
    var tite2 = $('.tite2').text();
    //当文档的高度小于或者等于总的高度的时候，开始动态加载数据
    if ($(document).height() <= totalheight) { 
		$.ajaxSetup({
            async : false //这是重要的一步，防止重复提交的  
		});
        //var page = $('.limit').val();
	    var num = '';
	    for(var i=1;i<$('.waress li').length;i++){ num = i; }
 	    var page = Math.ceil((num+1) / 8);
        var typeid = $('.cate #typeid').val();
        $.get("/goods/cate-data", {id: <?=$categoryId?>,page: page},
            function (result) {
                var s = result.join("");
                if(s=='false'){
                   $(".list-load").html("没有更多");return;
                }
				$(".waress").append(s);
				$(".list-load").css('display','block');
				$(".list-load").html("<img src='/images/loading.gif' style='width: 20px;'>");
            }, 'json');
    }
}

$(function(){
    //默认第一屏
    $.get("/goods/cate-data", {id: <?=$categoryId?>}, function (result) {
        if(result=='false'){
           $(".no").html("<div class='tite2'  ><span>暂无产品</span></div>");return;
        }else{
            var s = result.join("");
            $('.waress').html(s);
        }
    }, 'json');

    $("#form1").click(function () {
       window.location.href="/goods/q?wd="+$('#realname').val();
    });
});

//滚动加载
$(window).scroll( function() { loadataProduct();});
</script>