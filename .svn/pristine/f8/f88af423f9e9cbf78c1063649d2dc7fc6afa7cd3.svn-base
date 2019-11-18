<?php
use yii\helpers\Html;

$request = Yii::$app->request;
$this->title = '搜索 '.$searchKeywords.' - '.Yii::$app->params['site_name'];
?>
<style type="text/css">
.hot-tags {
   display: block;
   float: left;
   background-color: #000;
   color: #fff;
   font-size: 16px;
   margin-right: 10px;
   margin-top: 10px;
   box-sizing: border-box;
   padding: 8px 10px;
   float: left;
   text-align: center;
   margin: 0 5% 12px 0;     
}
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
    <form class="search-form class-search-form" action="/goods/q" method="get">
        <p>
            <input type="text" name="wd" class="form-control" style="display: inline;"  value="<?=$searchKeywords?>">
            <button class="index-search-button search-form-btn" type="submit" id='form1'></button>
        </p>
    </form>

    <div class="class-con">
    	<ul class="waress" >
    	<?php if(!empty($search)):?>
            <?=$search;?>
        <?php else:?>
            <p class="without" style='margin-top: 10px; font-size: 14px;'>
    			暂未找到与 <span style="font-weight: bold; color: #f00;"><?=$searchKeywords?></span> 相关的商品<br><br>请换个词，或者直接打开下面标签看看
    		</p>
    		<?php if(!empty($tags)):?>
			<div class="search-queries">
				<h4>热门搜索</h4>
                <div style="text-align: center;">
                    <?php foreach($tags as $tagsData):?>
                    <span class="hot-tags" onclick="window.location.href='<?=$tagsData['url']?>'"><?=$tagsData['tagname']?></span>
                    <?php endforeach;?>
                </div>
			</div> 
            <?php endif;?>
        <?php endif;?>
    	</ul>
    	<?php if(!empty($search)):?><div class="list-load" style="display: block; bottom: 5px;">没有商品了</div><?php endif;?>
    </div>
</div>
</div>

<script type="text/javascript">
function loadataProduct(){
    //浏览器的高度加上滚动条的高度
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());

    //当文档的高度小于或者等于总的高度的时候，开始动态加载数据
    if ($(document).height() <= totalheight) {
      var num = '';
		  for(var i=1;i<$('.waress li').length;i++){ num = i; }
      $.ajaxSetup({
              async : false //这是重要的一步，防止重复提交的  
      });
      var page = Math.ceil((num+1) / 8);
      $.get("/goods/q?action=ajax", {wd: "<?=$searchKeywords?>",page:page}, function (result) {
          if(result=='false' || result==false){
                  $('.no').html("<div class='tite2' id='more'><span>没有更多</span></div>");return;
          }else{
              $(".waress").append(result);
          }
        }, 'json');
      } 
}

$(function(){
    $("#form1").click(function () {
       window.location.href="/goods/q?wd="+$('#realname').val();
    });

    $(window).scroll( function() { loadataProduct(); });
});
</script>