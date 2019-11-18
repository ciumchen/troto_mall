<?php defined('IN_IA') or exit('Access Denied');?><div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品类型</label>
	<div class="col-sm-9 col-xs-12">
		<label for="isshow3" class="radio-inline"><input type="radio" name="type" value="1" id="isshow3" <?php  if(empty($item['type']) || $item['type'] == 1) { ?>checked="true"<?php  } ?> onclick="$('#product').show()" /> 实体商品</label>&nbsp;&nbsp;&nbsp;<label for="isshow4" class="radio-inline"><input type="radio" name="type" value="2" id="isshow4"  <?php  if($item['type'] == 2) { ?>checked="true"<?php  } ?>  onclick="$('#product').hide()" /> 虚拟商品</label>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">是否上架</label>
	<div class="col-sm-9 col-xs-12">
		<label for="isshow1" class="radio-inline"><input type="radio" name="status" value="1" id="isshow1" <?php  if($item['status'] == 1) { ?>checked="true"<?php  } ?> /> 是</label>
		&nbsp;&nbsp;&nbsp;
		<label for="isshow2" class="radio-inline"><input type="radio" name="status" value="0" id="isshow2"  <?php  if($item['status'] == 0) { ?>checked="true"<?php  } ?> /> 否</label>
		<span class="help-block"></span>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="displayorder" class="form-control" value="<?php  echo $item['displayorder'];?>" />
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品标签</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="tags" class="form-control" value="<?php  echo $item['tags'];?>" />
		<span class="help-block"><span style="color:#f00; font-weight:bold;">注意：</span>标签数量：5-15个；每个关键词间使用英文逗号分隔；每个标签首尾不能有空格；含有字符（/=+-_|，）的字符串会被隔断为两个关键词</span>
	</div>
</div>
<div class="form-group">
	<label class="col-xs-12 col-sm-3 col-md-2 control-label">相关商品推荐</label>
	<div class="col-sm-9 col-xs-12">
		<input type="text" name="relatedgoods" class="form-control" value="<?php  echo $item['relatedgoods'];?>" />
		<span class="help-block"><span style="color:#f00; font-weight:bold;">注意：</span>填写商品id(3-8个)，数字ID之间使用英文逗号分隔开，每个ID首尾不能有空格</span>
	</div>
</div>