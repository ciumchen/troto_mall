
<link href="/css/new_info.css" rel="stylesheet">
<div id="overflow_canvas_container">	
    <!-- main 开始 -->
	<div id="canvas">
		<div id="layer_972" type="media" class="cstlayer">
			<div class="wp-media_content">
				<div class="img_over">
					<img  class="img_lazy_load paragraph_image"  type="zoom" src="/img/500135674.jpg" >
				</div>
			</div>
		</div>
		<div id="layer_311" type="media" class="cstlayer">
			<div class="wp-media_content" >
				<div class="img_over">
					<img  class="img_lazy_load paragraph_image" type="zoom" src="/img/xrs2.png">
				</div>
			</div>
		</div>
		<div id="layer_3B7" type="title" class="cstlayer">
			<div class="wp-title_content">
				<div style="text-align: center;">
					<span>新闻动态</span>
				</div>
			</div>
		</div>
		<div id="layer_194" type="article_category" class="cstlayer">
			<div class="wp-article_category_content">
				<div class="nav1 menu_hs9" more="更多">
		  			<ul id="nav_layer_194" class="navigation">
		  				<li style="width:33.333333333333%;z-index:2;" class="wp_subtop <?php if($new['cateid']==1):?>lihover<?php endif;?>" pid="1">
		  					<a class="<?php if($new['cateid']==1):?>ahover<?php endif;?>" href="/news/index?cateid=1">
		  						<span style="display:block;overflow:hidden;">公司新闻</span>
		  					</a>
		  				</li>
		  				<li style="width:33.333333333333%;z-index:2;" class="wp_subtop <?php if($new['cateid']==2):?>lihover<?php endif;?>" pid="2">
		  					<a class="<?php if($new['cateid']==1):?>ahover<?php endif;?>" href="/news/index?cateid=2">
		  						<span style="display:block;overflow:hidden;">行业资讯</span>
		  					</a>
		  				</li>
		  				<li style="width:33.333333333333%;z-index:2;" class="wp_subtop <?php if($new['cateid']==3):?>lihover<?php endif;?>" pid="3">
		  					<a class="<?php if($new['cateid']==1):?>ahover<?php endif;?>" href="/news/index?cateid=3">
		  						<span style="display:block;overflow:hidden;">化妆技巧</span>
		  					</a>
		  				</li>
		  			</ul>
	   				<div class="default_pid" style="display:none; width:0px; height:0px;"></div>
	 			</div> 
			</div>
		</div>
		<script>
			$("#nav_layer_194 .wp_subtop").hover(function(){
				$("#nav_layer_194").find(".lihover").removeClass("lihover").addClass("seld");
				$("#nav_layer_194 a").removeClass("ahover");
			},function(){
				$("#nav_layer_194").find(".seld").addClass("lihover").removeClass("seld");
				$("#nav_layer_194").find(".lihover").find("a").addClass("ahover");
			});
		</script>
		<div id="layer_F08" type="article_detail" class="cstlayer">
			<div class="wp-article_detail_content">
				<div class="artdetail_title"><?=$new['title']?></div>
				<div class="artview_info">
					<div class="sourcedata" style="width: 712px;margin-left: 260px;">
						<span class="detail_head_title pub_txt_span"><span class="pub_txt">发布时间: </span><?php echo date('Y-m-d H:i:s', $new['createdt'])?></span>
						<span class="text-source-left01">|</span>
						<span class="detail_head_title times_txt_span"><?= $new['pv']?> <span class="times_txt">次浏览</span></span>
						<span class="text-source-left01 shareshow">|</span>	 
		 				<span class="detail_head_title shareshow">分享到: </span>
		 				<span class="bdsharebuttonbox shareshow bdshare-button-style2-16" data-bd-bind="1503905341370">		
							<a href="javascript:;" class="bds_sqq_a" data-cmd="sqq" title="分享到QQ好友"></a>
							<a href="javascript:;" class="bds_weixin_a" data-cmd="weixin" title="分享到微信"></a>
							<a href="javascript:;" class="bds_tsina_a" data-cmd="tsina" title="分享到新浪微博"></a>
							<a href="javascript:;" class="bds_douban_a" data-cmd="douban" title="分享到豆瓣"></a>
							<a href="javascript:;" class="bds_qzone_a" data-cmd="qzone" title="分享到QQ空间"></a>
						</span>
						<script>
							jsModern.share({
							    qrcode: ".bds_weixin_a",
							    douban: ".bds_douban_a",
							    qzone: ".bds_qzone_a",
							    sina: ".bds_tsina_a",
							    qq: ".bds_sqq_a"
							});  
						</script>
					</div>
				</div>
				<div class="artview_intro" style="margin: auto; width: 600px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $new['intro'] ?></div>
				<div class="artview_content">
					<div class="artview_detail" id="article46">
						<?php echo html_entity_decode($new['content'])?>
						<div style="clear:both;"></div>
					</div>	
				</div>  		
				<div class="artview_prev_next">
					<div style="margin-top:20px;width:100%;">
						<div style="cursor: pointer;" class="prevlist">
							<span class="up_arrow"></span>
							<span class="prev_next_text prev_txt" style="display:block;float:left;margin-right: 5px;">上一篇： </span> 
							<?php if(empty($upnew)):?>
								<span class="prev_next_link" style="display:block;;float:left;">没有了</span>
							<?php else:?>
								<a class="prev_next_link" style="display:block;float:left;" href="/news/new-info?id=<?=$upnew['id']?>&cateid=<?= $cateid?>" title="<?= $upnew['title']?>">
								 <?= $upnew['title']?>
							</a>
							<?php endif;?>
							<div style="clear:both;"></div>
						</div>
						<div style="" class="nextlist">
							<span class="down_arrow"></span>
							<span class="prev_next_text next_txt" style="display:block;float:left;margin-right: 5px;">下一篇：</span>
							<?php if(empty($nextnew)):?>
								<span class="prev_next_link" style="display:block;;float:left;">没有了</span>
							<?php else:?>
								<a class="prev_next_link" style="display:block;float:left;" href="/news/new-info?id=<?=$nextnew['id']?>&cateid=<?= $cateid?>" title="<?= $nextnew['title']?>">
								 <?= $nextnew['title']?>
								</a>
							<?php endif;?>
							<div style="clear:both;"></div>
						</div>
					</div>	
				</div>
			</div>
    	</div>	
    </div>
    <!-- main 结束-->
</div>


<script>
	$(document).ready(function(){ 
		$("#nav_layermenu li").eq(2).addClass("lihover");
		$("#nav_layermenu li>a").eq(2).addClass("ahover");
		$("#nav_fomune li").eq(2).addClass("lihover");
		$("#nav_fomune li>a").eq(2).addClass("ahover");
		$("#scroll_container_bg").css("height",2086);
	})
</script>