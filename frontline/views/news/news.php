<link href="/css/news.css" rel="stylesheet">
<input type="hidden" id="page_id" name="page_id" value="3" rpid="3">

    <div id="overflow_canvas_container">	

    	<!-- 替换位置 -->
		<div id="canvas">
			<div id="layer_308" type="media" class="cstlayer">
				<div class="wp-media_content">
					<div class="img_over">
						<img class="img_lazy_load paragraph_image" src="/img/500135674.jpg">
					</div>
				</div>
			</div>
			<div id="layer_FF1" type="media" class="cstlayer">
				<div class="wp-media_content" >
					<div class="img_over" >
						<img  class="img_lazy_load paragraph_image" src="/img/xrs2.png" >
					</div>
				</div>
			</div>
			<div id="layer_317" type="title" class="cstlayer">
				<div class="wp-title_content" >
					<div style="text-align: center;">
						<span id="newtitle" >新闻动态</span>
					</div>
				</div>
			</div>
			<div id="layer_8BA" type="article_category" class="cstlayer">
				<div class="wp-article_category_content" style="border: 0px solid transparent; width: 960px; height: 40px; padding: 0px;">
					<div skin="hs9" class="nav1 menu_hs9" ishorizon="" colorstyle="black" direction="0" more="更多" moreshow="1" morecolor="darkorange" hover="1">
			  			<ul id="nav_layer_8BA" class="navigation">
			  				<li style="width:33.333333333333%;z-index:2;" class="wp_subtop wp_subtop_new <?php if($cateid==1):?>lihover<?php endif;?>" pid="1">
			  					<a class="<?php if($cateid==1):?>ahover<?php endif;?>" href="/news/index?cateid=1">
			  						<span style="display:block;overflow:hidden;">公司新闻</span>
			  					</a>
			  				</li>
			  				<li style="width:33.333333333333%;z-index:2;" class="wp_subtop wp_subtop_new <?php if($cateid==2):?>lihover<?php endif;?>" pid="2">
			  					<a class="<?php if($cateid==2):?>ahover<?php endif;?>" href="/news/index?cateid=2">
			  						<span style="display:block;overflow:hidden;">行业资讯</span>
			  					</a>
			  				</li>
			  				<li style="width:33.333333333333%;z-index:2;" class="wp_subtop wp_subtop_new <?php if($cateid==3):?>lihover<?php endif;?>" pid="3">
			  					<a class="<?php if($cateid==3):?>ahover<?php endif;?>" href="/news/index?cateid=3">
			  						<span style="display:block;overflow:hidden;">健康频道</span>
			  					</a>
			  				</li>
			  			</ul>
		   			<div class="default_pid" style="display:none; width:0px; height:0px;"></div>
		 			</div> 
				</div>

			</div>
			<div id="layer_870" type="article_list" class="cstlayer">
				<div class="wp-article_content wp-article_list_content">
					<div skin="skin4" class="wp-article_css wp-article_list_css" style="display:none;"></div>
					<div class="article_list-layer_870" style="overflow:hidden;">	
						<ul>
							<?php foreach($newsList as $new):?>
							<li class="wpart-border-line" >
								<div class="time" >
									<span class="wp-new-ar-pro-time"><?php echo date('d',$new['createdt'])?></span>
									<span class="date" ><?php echo date('Y-m',$new['createdt'])?></span>
								</div>
		    					<div class="conts">
									<p class="title">
										<?php if($new['outlink']==''):?>
										<a class="articleid" articleid="46" href="/news/new-info?id=<?= $new['id']?>&cateid=<?= $new['cateid']?>" title="<?= $new['title']?>"><?= $new['title']?></a>
										<?php else:?>
										<a class="articleid" articleid="46" href="<?= $new['outlink']?>" target='_blank' title="<?= $new['title']?>"><?= $new['title']?></a>
										<?php endif;?>
									</p>
									<p class="abstract"><?= $new['intro']?></p>
		   						</div>
							</li>
						<?php endforeach;?>
		 				</ul>
					</div>
				</div>


			</div>
		</div>
		<!-- 替换结束 -->
<script>
$(".wp_subtop_new").hover(function(){
	$("#nav_layer_8BA").find(".lihover").removeClass("lihover").addClass("seld");
	$("#nav_layer_8BA a").removeClass("ahover");
},function(){
	$("#nav_layer_8BA").find(".seld").addClass("lihover").removeClass("seld");
	$("#nav_layer_8BA").find(".lihover").find("a").addClass("ahover");
});
</script>
<script>
	$(document).ready(function(){ 
		$("#nav_layermenu li").eq(2).addClass("lihover");
		$("#nav_layermenu li>a").eq(2).addClass("ahover");
		$("#nav_fomune li").eq(2).addClass("lihover");
		$("#nav_fomune li>a").eq(2).addClass("ahover");
		$("#scroll_container_bg").css("height",2053);
	})
</script>
