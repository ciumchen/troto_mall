<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>

<link href="./resource/components/select2/select2.min.css" rel="stylesheet" />
<script src="./resource/components/select2/select2.min.js"></script>
<script src="./resource/components/select2/jquery-3.2.1.min.js"></script>

<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('servicefee',array('op' =>'display'))?>">地区列表</a></li>
	<?php  if($operation== 'post') { ?> <li class="active"><a href="##">编辑地区</a></li> <?php  } ?>
</ul>

<?php  if($operation == 'display') { ?>
<div class="main panel panel-default">
	<div class="panel-heading">
		省市（共 <label class="text text-danger"><?php  echo $total;?></label> 条数据）
	</div>
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
			<tr>
				<th style="width:80px;">省份 市</th>
				<th style="width:80px;">开放情况</th>
				<th style="width:80px;">操 作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($list)) { foreach($list as $item) { ?>
			<tr>
				<td><?php  echo $provinceList[$item['parentid']]['name'];?> - <?php  echo $item['name'];?></td>
				<td>包含 <strong><?php  echo $item['total'];?></strong> 个区县，已经开放配送安装服务包含 <strong><?php  echo $item['open'];?></strong> 个</td>
				<td style="text-align:left;">
					<a href="<?php  echo $this->createWebUrl('servicefee', array('op' => 'post', 'id' => $item['regionid']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="修改"><i class="fa fa-pencil"></i></a>
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
		<input type="hidden" name="cabinetid" value="<?php  echo $cabinet['cabinetid'];?>" />
		<div class="panel panel-default">
			<div class="panel-heading">
				服务地区设置
			</div>
			<div style="margin: 8px 0 0 16px;">
				<span class="help-block">提示: 未添加的区域,服务费默认为上级区域的服务费金额。</span>
			</div>
			<div class="panel-body">
				<table class="table table-hover ">
					<thead>
					<tr>
						<th width="40">省份 城市 区(县)</th>
						<th width="30">状态</th>
						<th width="40">服务费</th>
					</tr>
					</thead>
					<tbody id="param-items">
						<?php  if(is_array($distList)) { foreach($distList as $p) { ?>
						<tr>
							<td>
								<input name="regionid[]" type="hidden" class="form-control param_title" value="<?php  echo $p['regionid'];?>"/>
								<input name="name[]" type="text" class="form-control param_title" value="<?php  echo $provinceList[$cityInfo['parentid']]['name'];?> - <?php  echo $cityInfo['name'];?> - <?php  echo $p['name'];?>" readonly="readonly" />
							</td>
							<td>
								<select name="status[]" class='form-control pathway-status'>
									<option value="1"<?php  if($p['status']==1) { ?> selected="selected"<?php  } ?>>开放服务</option>
									<option value="0"<?php  if($p['status']==0) { ?> selected="selected"<?php  } ?>>未开放服务</option>
								</select>
							</td>
							<td><input name="fee[]" type="text" class="form-control param_value" value="<?php  echo $p['fee'];?>"/></td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-4"><input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-4" /></div>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>