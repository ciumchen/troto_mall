<?php defined('IN_IA') or exit('Access Denied');?>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品详情</label>
	<div class="col-sm-9 col-xs-12">
		<textarea name="content" class="form-control richtext" cols="70" rows="100"><?php  echo $item['content'];?></textarea>
	</div>
</div>
<script language='javascript'>
	require(['jquery', 'util'], function($, u){
		$(function(){
			u.editor($('.richtext')[0]);
		});
	});
</script>