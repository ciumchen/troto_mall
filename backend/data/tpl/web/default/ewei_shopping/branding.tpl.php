<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('branding',array('op' =>'display'))?>">品牌管理</a></li>
	<li<?php  if(empty($branding['id']) && $operation == 'post') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('branding',array('op' =>'post'))?>">添加品牌信息</a></li>
	<?php  if(!empty($branding['name']) && $operation== 'post') { ?> <li class="active"><a href="<?php  echo $this->createWebUrl('branding',array('op' =>'post','id'=>$branding['id']))?>">编辑品牌信息</a></li> <?php  } ?>
</ul>

<?php  if($operation == 'display') { ?>
<div class="main panel panel-default">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:100px;">品牌图标</th>
					<th style="width:150px;">品牌名称</th>
					<th style="width:100px;text-align:center;">显示顺序</th>
					<th style="width:100px;">简拼</th>
					<th style="width:100px;">全拼</th>
					<th style="width:100px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><img src="<?php  echo $item['brandimg'];?>" class="img-thumbnail" style="height:32px;"></td>
					<td><?php  echo $item['brand'];?></td>
					<td style="text-align:center;"><?php  echo $item['displayorder'];?></td>
					<td><?php  echo $item['spellname'];?></td>
					<td><?php  echo $item['fullname'];?></td>
					<td>
						<a href="<?php  echo $this->createWebUrl('branding', array('op' => 'post', 'brandname' => $item['brand']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="修改"><i class="fa fa-pencil"></i></a>
						<!-- <a href="<?php  echo $this->createWebUrl('branding', array('op' => 'delete', 'brandname' => $item['brand']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="删除"><i class="fa fa-times"></i></a> -->
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
		</table>
		<?php  echo $pager;?>
	</div>
</div>

<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>

<?php  } else if($operation == 'post') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" onsubmit='return formcheck()'>
		<input type="hidden" name="brandname" value="<?php  echo $branding['brand'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">品牌信息设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="displayorder" class="form-control" value="<?php  echo $branding['displayorder'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>品牌名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='brandingname' name="brandingname" class="form-control" value="<?php  echo $branding['brand'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>品牌简拼</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='spellname' name="spellname" class="form-control" value="<?php  echo $branding['spellname'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>品牌全拼</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='fullname' name="fullname" class="form-control" value="<?php  echo $branding['fullname'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">品牌图标</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_image('brandimg', $branding['brandimg'], '', array('extras' => array('text' => 'readonly')))?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">品牌介绍</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="content" class="form-control" cols="70"><?php  echo $branding['content'];?></textarea>
					</div>
				</div> 
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>品牌介绍链接</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='brandurl' name="brandurl" class="form-control" value="<?php  echo $branding['brandurl'];?>" />
					</div>
				</div>
		</div>
	</div>
	<div class="form-group col-sm-12">
		<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" onclick="return formcheck()" />
		<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
	</div>
	</form>
</div>

<script language='javascript'>
	function formcheck() {
		if ($("#brandingname").isEmpty()) {
			Tip.focus("brandingname", "请填写品牌名称!", "top");
			return false;
		}
		return true;
	}
</script>

<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>