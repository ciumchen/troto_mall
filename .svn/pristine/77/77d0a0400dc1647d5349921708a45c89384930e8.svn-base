
<link href="/css/product.css" rel="stylesheet">
<link href="/css/detail.css" rel="stylesheet">
    <script type="text/javascript" src="/js/jquery.qrcode.min.js"></script>
<div id="overflow_canvas_container">	
	<!-- 替换位置 -->
	<div id="canvas">
	<div id="layercstlayer" type="media" class="cstlayer">
		<div class="wp-media_content">
			<div class="img_over">
				<img id="wp-media-image_layercstlayer" class="img_lazy_load paragraph_image"type="zoom" src="/img/500216320.jpg">
			</div>
		</div>
	</div>	
	<div id="layerlazy_load" type="media" class="cstlayer">
		<div class="wp-media_content">
			<div class="img_over">
				<img id="wp-media-image_layerlazy_load" class="img_lazy_load paragraph_image" src="/img/a58t.png">
			</div>
		</div>
	</div>
	<div id="layerlast" type="title" class="cstlayer">
		<div class="wp-title_content">
			<div style="text-align: center;">
				<span>推介产品</span>
			</div>
		</div>
	</div>
	<div id="layertype" type="product_category" class="cstlayer">
		<div class="wp-product_category_content">
 			<div class="nav1 menu_hs9" more="更多">	
  				<ul id="nav_layertype" class="navigation">
  					
  					<li style="width:14.2857143%;z-index:2; " class="wp_subtop <?php if($typeid==22):?>lihover<?php endif;?>" pid="22">
  						<a class=" sub <?php if($typeid==22):?>ahover<?php endif;?>" href="/product/index?typeid=22">
  							<span style="display:block;overflow:hidden;">营养保健</span>
  						</a>
  					</li>
  					<li style="width:14.2857143%;z-index:2;" class="wp_subtop <?php if($typeid==23):?>lihover<?php endif;?>" pid="23">
  						<a class=" sub <?php if($typeid==23):?>ahover<?php endif;?>" href="/product/index?typeid=23">
  							<span style="display:block;overflow:hidden;">环球美食</span>
  						</a>
					</li>
					<li style="width:14.2857143%;z-index:2;" class="wp_subtop <?php if($typeid==25):?>lihover<?php endif;?>" pid="25">
						<a class=" sub <?php if($typeid==25):?>ahover<?php endif;?>" href="/product/index?typeid=25">
							<span style="display:block;overflow:hidden;">母婴用品</span>
						</a>
					</li>
					<li style="width:14.2857143%;z-index:2;" class="wp_subtop <?php if($typeid==24):?>lihover<?php endif;?>" pid="24">
						<a class=" sub <?php if($typeid==24):?>ahover<?php endif;?>" href="/product/index?typeid=24">
							<span style="display:block;overflow:hidden;">时尚轻奢</span>
						</a>
					</li>
					<li style="width:14.2857143%;z-index:2;" class="wp_subtop <?php if($typeid==26):?>lihover<?php endif;?>" pid="26">
						<a class=" sub <?php if($typeid==26):?>ahover<?php endif;?>" href="/product/index?typeid=26">
							<span style="display:block;overflow:hidden;">洗护彩妆</span>
						</a>
					</li>
					<li style="width:14.2857143%;z-index:2;" class="wp_subtop <?php if($typeid==27):?>lihover<?php endif;?>" pid="27">
						<a class=" sub <?php if($typeid==27):?>ahover<?php endif;?>" href="/product/index?typeid=27">
							<span style="display:block;overflow:hidden;">益智玩具</span>
						</a>
					</li>
					<li style="width:14.2857143%;z-index:2;" class="wp_subtop <?php if($typeid==21):?>lihover<?php endif;?>" pid="21">
  						<a class="sub  <?php if($typeid==21):?>ahover<?php endif;?>" href="/product/index?typeid=21">
  							<span style="display:block;overflow:hidden;">生活家居</span>
  						</a>
  					</li>
				</ul>
  				<div class="default_pid" style="display:none; width:0px; height:0px;"></div>
  			</div>
 		</div>
	</div>
	<script>
	$("#nav_layertype .wp_subtop").hover(function(){
		$("#nav_layertype").find(".lihover").removeClass("lihover").addClass("seld");
		$("#nav_layertype a").removeClass("ahover");
	},function(){
		$("#nav_layertype").find(".seld").addClass("lihover").removeClass("seld");
		$("#nav_layertype").find(".lihover").find("a").addClass("ahover");
	});
	</script>
	<div id="layer_product_list" type="product_list" class="cstlayer">
		<div class="wp-product_content wp-product_list_content">
			<div class="wp-product_css wp-product_list_css" style="display:none;"></div>
			<div class="product_list-layer_product_list" style="overflow:hidden;"> 
				<ul style="margin:0px;">
				<?php if(!empty($goods)):?>
				<?php foreach($goods as $key=>$goods):?>
					<li class="wp-new-article-style_lis" style="width: 290px; margin-right: <?php if(($key+1)%3==0):?>0<?php else:?>20<?php endif;?>px;">
						<div class="img" >
							<a href="##" class="aeffect goBuy" goodsid="<?= $goods['id']?>" alt="<?=$goods['title']?>" imgurl="<?=$goods['thumb']?>">
								<img src="<?=$goods['thumb']?>" class="wp-product_list-thumbnail img_lazy_load" alt="<?=$goods['title']?>">
							</a>
						</div>
						<div class="wp-new-article-style-c" style="height: 61px;">
							<p class="title" style="font: normal 120% &#39;Microsoft yahei&#39;, Arial, Verdana;height: 37px;">
								<a href="##" class="goBuy" goodsid="<?= $goods['id']?>" alt="<?=$goods['title']?>" imgurl="<?=$goods['thumb']?>"><?=$goods['title']?></a>
							<p class="wp-new-ar-pro-style-price" id="price"><label class="price_f_title">价格:</label><span class="price_f"><?=$goods['marketprice']?></span></p>
						</div>
					</li>
				<?php endforeach;?>
			<?php endif;?>
				</ul>
					<!--遮罩层-->
					<div class="msg"></div>
					<!--立即购买后弹出的内容-->
					<div class="Popup Bg-color">
						<a href="##" id="btn-off" class="iconfont icon-guanbianniu">X</a>
						<h2 class="Popup-title"></h2>
						<div class="Popup-main">
							<div class="Popup-main-lt ft-lf">
								<img src="" id="goodsimg" style=" border-radius: 10px;">
							</div>
							<div class="Popup-main-rt ft-lf">
								<div id="qrcode"></div>
								<p>(使用微信扫码了解详情)</p>
								<a href="https://shop156053788.taobao.com/search.htm?spm=a1z10.1-c.w5002-16750932553.1.56816f9eUSFDi1&search=y" target="_blank">点击跳转淘宝店铺</a>
							</div>
							<div class="cle"></div>
						</div>
					</div>
			</div>
		</div>
	</div>	
	</div>
	<!-- 替换结束 -->
</div>	
	<script>
	 $(".goBuy").on('click',function(){
	 	$(".msg").show();
	 	$(".Popup").show();
	 	var title = $(this).attr("alt");
	 	var imgurl = $(this).attr("imgurl"); 
	 	var id = $(this).attr("goodsid");
	 	$(".Popup-title").html(title);
	 	$("#goodsimg").attr("src",imgurl);
	 	$("#qrcode").qrcode({
				render: "canvas",
				width:200,
				height:200,
				background: "#dfedd1",
				foreground:'#737c40',
				text:"http:\/\/m.10d15.com\/goods\/detail?id="+id
		});
	 });
	 $("#btn-off").on('click',function(){
	 	$(".msg").hide();
	 	$(".Popup").hide();
	 	$("#qrcode").html("");
	 });
	</script>
	<script>
	$(document).ready(function(){ 
		$("#nav_layermenu li").eq(1).addClass("lihover");
		$("#nav_layermenu li>a").eq(1).addClass("ahover");
		$("#nav_fomune li").eq(1).addClass("lihover");
		$("#nav_fomune li>a").eq(1).addClass("ahover");
		$("#scroll_container_bg").css("height",2296);
	})
	</script>