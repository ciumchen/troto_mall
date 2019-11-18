<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('warehouse',array('op' =>'display'))?>">仓库管理</a></li>
	<li<?php  if(empty($warehouse['id']) && $operation == 'post') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('warehouse',array('op' =>'post'))?>">添加仓库信息</a></li>
	<?php  if(!empty($warehouse['id']) && $operation== 'post') { ?> <li class="active"><a href="<?php  echo $this->createWebUrl('warehouse',array('op' =>'post','id'=>$warehouse['id']))?>">编辑仓库信息</a></li> <?php  } ?>
</ul>

<?php  if($operation == 'display') { ?>
<div class="main panel panel-default">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th>ID</th>
					<th>仓库名称</th>
					<th>是否需要报关</th>
					<th>启用状态</th>
					<th>显示顺序</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><?php  echo $item['id'];?></td>
					<td><?php  echo $item['name'];?></td>
					<td><?php echo $item['declaration']==1 ? '<span class="label label-default">&nbsp;是&nbsp;</span>' : '<span class="label label-warning">&nbsp;否&nbsp;</span>'?></td>
					<td><?php echo $item['status']==1 ? '已启用' : '未启用'?></td>
					<td><?php  echo $item['displayorder'];?></td>
					<td style="text-align:left;">
						<a href="<?php  echo $this->createWebUrl('warehouse', array('op' => 'post', 'id' => $item['id']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="修改"><i class="fa fa-pencil"></i></a>
						<a href="<?php  echo $this->createWebUrl('warehouse', array('op' => 'delete', 'id' => $item['id']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="删除"><i class="fa fa-times"></i></a>
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
		<input type="hidden" name="id" value="<?php  echo $warehouse['id'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">仓库信息设置</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">排序</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="displayorder" class="form-control" value="<?php  echo $warehouse['displayorder'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>仓库名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" id='warehousename' name="warehousename" class="form-control" value="<?php  echo $warehouse['name'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">需要申报海关</label>
					<div class="col-sm-9 col-xs-12">
						<label class='radio-inline'>
						 	<input type='radio' name='declaration' value=1' <?php  if($warehouse['declaration']==1) { ?>checked<?php  } ?> />需要申报
						</label>
						<label class='radio-inline'>
							<input type='radio' name='declaration' value=0' <?php  if($warehouse['declaration']==0) { ?>checked<?php  } ?> />无需申报
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">启用状态</label>
					<div class="col-sm-9 col-xs-12">
						<label class='radio-inline'>
						 	<input type='radio' name='status' value=1' <?php  if($warehouse['status']==1) { ?>checked<?php  } ?> />已启用
						</label>
						<label class='radio-inline'>
							<input type='radio' name='status' value=0' <?php  if($warehouse['status']==0) { ?>checked<?php  } ?> />未启用
						</label>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">介绍</label>
					<div class="col-sm-9 col-xs-12">
						<textarea name="description" class="form-control" cols="70"><?php  echo $warehouse['description'];?></textarea>
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
		if ($("#warehousename").isEmpty()) {
			Tip.focus("warehousename", "请填写仓库名称!", "top");
			return false;
		}
		return true;
	}
</script>

<?php  } ?>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>