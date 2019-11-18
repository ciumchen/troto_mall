<?php defined('IN_IA') or exit('Access Denied');?><?php  if(!empty($item['id'])):?>
<!-- 
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
	<div class="col-sm-6 col-xs-6">
		<a href="<?php  echo $_W['siteroot'];?>/app/index.php?i=<?php  echo $_W['uniacid'];?>&c=entry&id=<?php  echo $item['id'];?>&do=detail&m=ewei_shopping&wxref=mp.weixin.qq.com#wechat_redirect" target="_blank">点击查看商品展示效果...</a>
	</div>
</div>
 -->
<?php  endif;?>

<link href="./resource/components/select2/select2.min.css" rel="stylesheet" />
<script src="./resource/components/select2/select2.min.js"></script>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品链接</label>
	<div class="col-sm-6 col-xs-6">
		<span class="label label-info" style="font-size: inherit;">http://mall.troto.com.cn/goods/item/<?php  echo $item['id'];?></span>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>商品名称</label>
	<div class="col-sm-6 col-xs-6">
		<input type="text" name="goodsname" id="goodsname" class="form-control" value="<?php  echo $item['title'];?>" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品简介</label>
	<div class="col-sm-6 col-xs-6">
		<input type="text" name="fdesc" id="fdesc" class="form-control" value="<?php  echo $item['fdesc'];?>"/>
		<span class="help-block">如：羊年手链，精致可爱，送女友最佳情人节礼物</span>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-4 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>分类设置</label>
	<div class="col-sm-3 col-xs-5">
		<select class="form-control" style="margin-right:15px;" id="pcate" name="pcate" onchange="fetchChildCategory(this.options[this.selectedIndex].value)"  autocomplete="off">
			<option value="0">请选择分类</option>
			<?php  if(is_array($category)) { foreach($category as $row) { ?>
			<?php  if($row['parentid'] == 0) { ?>
			<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $item['pcate']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
			<?php  } ?>
			<?php  } } ?>
		</select>
	</div>
	<label class="col-xs-4 col-sm-3 col-md-2 control-label"><span style='color:red'>*</span>品牌设置</label>
	<div class="col-sm-3 col-xs-5">
		<select class="" style="margin-right:15px;width: 100%; height: 35px;" name="brand" id="select2brand">
			<option value="0">请选择商品品牌</option>
			<?php  if(is_array($brandList)) { foreach($brandList as $row) { ?>
				<option value="<?php  echo $row['brand'];?>" <?php  if($row['brand'] == $_GPC['brand'] || $row['brand'] == $item['brand']) { ?> selected="selected"<?php  } ?>><?php  echo $row['brand'];?> -- <?php  echo $row['spellname'];?></option>
			<?php  } } ?>
		</select>
		<span class="help-block">提示：如果没有选项需要先去[<a href="/index.php?c=site&a=entry&do=branding&m=ewei_shopping">品牌管理</a>]中添加品牌项</span>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品型号编码</label> 
	<div class="col-sm-4 col-xs-12">
		<input type="text" name="goodssn" id="goodssn" class="form-control" value="<?php  echo $item['goodssn'];?>" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品属性</label>
	<div class="col-sm-9 col-xs-12" >
		<label for="isrecommand" class="checkbox-inline">
			<input type="checkbox" name="isrecommand" value="1" id="isrecommand" <?php  if($item['isrecommand'] == 1) { ?>checked="true"<?php  } ?> /> 首页推荐
		</label>
		<label for="isnew" class="checkbox-inline">
			<input type="checkbox" name="isnew" value="1" id="isnew" <?php  if($item['isnew'] == 1) { ?>checked="true"<?php  } ?> /> 新品推荐
		</label>
		<label for="ishot" class="checkbox-inline">
			<input type="checkbox" name="ishot" value="1" id="ishot" <?php  if($item['ishot'] == 1) { ?>checked="true"<?php  } ?> /> 热卖推荐
		</label>
		<label for="isdiscount" class="checkbox-inline">
			<input type="checkbox" name="isdiscount" value="1" id="isdiscount" <?php  if($item['isdiscount'] == 1) { ?>checked="true"<?php  } ?> /> 特惠商品
		</label>
<!-- 		<label for="istime" class="checkbox-inline">
			<input type="checkbox" name="istime" value="1" id="istime" <?php  if($item['istime'] == 1) { ?>checked="true"<?php  } ?> /> 限时卖
		</label> -->
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">限时卖时间</label>
	<div class="col-sm-4 col-xs-6"><?php echo tpl_form_field_date('timestart', !empty($item['timestart']) ? date('Y-m-d H:i',$item['timestart']) : date('Y-m-d H:i'), 1)?></div>
	<div class="col-sm-4 col-xs-6"><?php echo tpl_form_field_date('timeend', !empty($item['timeend']) ? date('Y-m-d H:i',$item['timeend']) : date('Y-m-d H:i'), 1)?></div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品图</label>
	<div class="col-sm-9 col-xs-12">
		<?php  echo tpl_form_field_image('thumb', $item['thumb'], '', array('extras' => array('text' => 'readonly')))?>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">首页展示图</label>
	<div class="col-sm-9 col-xs-12">
		<?php  echo tpl_form_field_image('thumb1', $item['thumb1'], '', array('extras' => array('text' => 'readonly')))?>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品标签</label>
	<div class="col-sm-6 col-xs-6">
		<input type="text" name="label" id="label" class="form-control" value="<?php  echo $item['label'];?>"/>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">其他图片</label>
	<div class="col-sm-9 col-xs-9">
		<?php  echo tpl_form_field_multi_image('thumbs',$piclist)?>
	</div>
</div>

<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">销售配置</label>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">库存数量</span>
			<input type="text" name="total" id="total" class="form-control" value="<?php  echo $item['total'];?>" />
			<span class="input-group-addon">条</span>
		</div>
	</div>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">限买数量</span>
			<input type="text" name="maxbuy" id="maxbuy" class="form-control" value="<?php  echo $item['maxbuy'];?>" />
			<span class="input-group-addon">条</span>
		</div>
	</div>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">已售数量</span>
			<input type="text" name="sales" id="sales" class="form-control" value="<?php  echo $item['sales'];?>" />
			<span class="input-group-addon">条</span>
		</div>
	</div>
</div>

<!-- 
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">减库存方式</label>
	<div class="col-sm-9 col-xs-12">
		<label for="totalcnf1" class="radio-inline"><input type="radio" name="totalcnf" value="0" id="totalcnf1" <?php  if(empty($item) || $item['totalcnf'] == 0) { ?>checked="true"<?php  } ?> /> 拍下减库存</label>
		&nbsp;&nbsp;&nbsp;
		<label for="totalcnf2" class="radio-inline"><input type="radio" name="totalcnf" value="1" id="totalcnf2"  <?php  if(!empty($item) && $item['totalcnf'] == 1) { ?>checked="true"<?php  } ?> /> 付款减库存</label>
		&nbsp;&nbsp;&nbsp;
		<label for="totalcnf3" class="radio-inline"><input type="radio" name="totalcnf" value="2" id="totalcnf3"  <?php  if(!empty($item) && $item['totalcnf'] == 2) { ?>checked="true"<?php  } ?> /> 永不减库存</label>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">价格配置</label>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">销售价</span>
			<input type="text" name="marketprice" id="marketprice" class="form-control" value="<?php  echo $item['marketprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>
	</div>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">原价</span>
			<input type="text" name="originalprice" id="originalprice" class="form-control" value="<?php  echo $item['originalprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>
	</div>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">市场价</span>
			<input type="text" name="productprice" id="productprice" class="form-control" value="<?php  echo $item['productprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>
	</div>
		<div class="col-sm-2 col-xs-6">
			<span class="input-group-addon">成本价</span>
			<input type="text" name="costprice" id="costprice" class="form-control" value="<?php  echo $item['costprice'];?>" />
			<span class="input-group-addon">元</span>
		</div>
</div>
-->
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">其他配置</label>
	<div class="col-sm-3 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">购买积分</span>
			<input type="text" name="credit" id="credit" class="form-control" value="<?php  echo $item['credit'];?>" />
			<span class="input-group-addon">点(不配置 默认为不奖励)</span>
		</div>
	</div>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">商品单位</span>
			<input type="text" name="unit" id="unit" class="form-control" value="<?php  echo $item['unit'];?>" />
		</div>
	</div>
	<div class="col-sm-2 col-xs-6">
		<div class="input-group form-group">
			<span class="input-group-addon">重量</span>
			<input type="text" name="weight" id="weight" class="form-control" value="<?php  echo $item['weight'];?>" />
			<span class="input-group-addon">公斤</span>
		</div>
	</div>
</div>
<script language="javascript">
$(function(){
	var i = 0;
	$('#selectimage').click(function() {
		var editor = KindEditor.editor({
			allowFileManager : false,
			imageSizeLimit : '10MB',
			uploadJson : './index.php?act=attachment&do=upload'
		});
		editor.loadPlugin('multiimage', function() {
			editor.plugin.multiImageDialog({
				clickFn : function(list) {
					if (list && list.length > 0) {
						for (i in list) {
							if (list[i]) {
								html =	'<li class="imgbox" style="list-type:none">'+
								'<a class="item_close" href="javascript:;" onclick="deletepic(this);" title="删除"></a>'+
								'<span class="item_box"> <img src="'+list[i]['url']+'" style="height:80px"></span>'+
								'<input type="hidden" name="attachment-new[]" value="'+list[i]['filename']+'" />'+
								'</li>';
								$('#fileList').append(html);
								i++;
							}
						}
						editor.hideDialog();
					} else {
						alert('请先选择要上传的图片！');
					}
				}
			});
		});
	});
});
function deletepic(obj){
	if (confirm("确认要删除？")) {
		var $thisob=$(obj);
		var $liobj=$thisob.parent();
		var picurl=$liobj.children('input').val();
		$.post('<?php  echo $this->createMobileUrl('ajaxdelete',array())?>',{ pic:picurl},function(m){
			if(m=='1') {
				$liobj.remove();
			} else {
				alert("删除失败");
			}
		},"html");
	}
}
</script>

<script>
//选择品牌
$('#select2brand').select2();
//选择供应商
$('#select2supplier').select2();
</script>