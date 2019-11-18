<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>

<link href="./resource/components/select2/select2.min.css" rel="stylesheet" />
<script src="./resource/components/select2/select2.min.js"></script>
<script src="./resource/components/select2/jquery-3.2.1.min.js"></script>

<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('corpteam',array('op' =>'display'))?>">主卡用户列表</a></li>
	<?php  if($operation== 'post') { ?> <li class="active"><a href="##">编辑用户</a></li> <?php  } ?>
	<li<?php  if($operation == 'post' && empty($corpteam['id'])) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('corpteam',array('op' =>'import'))?>">导入数据</a></li>
</ul>

<?php  if($operation == 'display') { ?>
<div class="main panel panel-default">
	<div class="panel-heading">
		主卡用户（共 <label class="text text-danger"><?php  echo $total;?></label> 条数据）
	</div>
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
			<tr>
				<th style="width:50px;">主卡用户</th>
				<th style="width:50px;">电话</th>
				<th style="width:50px;">所属车队</th>
				<th style="width:50px;">账户余额</th>
				<th style="width:50px;">状态</th>
				<th style="width:50px;">创建时间</th>
				<th style="width:50px;">操 作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($corpteamList)) { foreach($corpteamList as $item) { ?>
			<tr>
				<td><?php  echo $item['realname'];?></td>
				<td><?php  echo $item['mobile'];?></td>
				<td><?php  echo $item['name'];?></td>
				<td><span>￥</span><?php  echo $item['deposit'];?></td>
				<td>
					<?php  if ($item['status'] == 1){?><label class="label label-success">启用</label><?php  }?>
					<?php  if ($item['status'] == 0){?><label class="label label-default">未启用</label><?php  }?>
				</td>
				<td><?php  echo date('Y-m-d', $item['createdt']) ?></td>
				<td style="text-align:left;">
					<a href="<?php  echo $this->createWebUrl('corpteam', array('op' => 'post', 'uid' => $item['uid'],'corpteamid' => $item['corpteamid']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="修改"><i class="fa fa-pencil"></i></a>
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
		<div class="panel panel-default">
			<div class="panel-heading">
				副卡用户设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label"><span style="color:red">*</span>主卡用户</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="name" class="form-control" value="<?php  echo $corpList[0]['realname'];?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label"><span style="color:red">*</span>所属车队</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="name" class="form-control" value="<?php  echo $corpList[0]['name'];?>" readonly="readonly"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label"><span style="color:red">*</span>账号余额</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="deposit" class="form-control" value="<?php  echo $corpList[0]['deposit'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-1 control-label">副卡管理</label>
					<div class="col-sm-9 col-xs-12">
						<table class="table table-hover ">
							<thead>
							<tr>
								<th width="20px;">副卡用户</th>
								<th width="20px;">电话</th>
								<th width="20px;">车牌</th>
								<th width="10px;">状态</th>
								<th width="10px;">分配金额</th>
							</tr>
							</thead>
							<tbody id="param-items">
								<?php  if(is_array($corpteamDriverList)) { foreach($corpteamDriverList as $driverItem) { ?>
								<tr>
									<td>
										<input name="driverid[]" type="hidden" class="form-control param_title" value="<?php  echo $driverItem['driverid'];?>"/>
										<input type="text" style="width: 150px;" class="form-control param_title" value="<?php  echo $driverItem['realname'];?>" readonly="readonly" />
									</td>
									<td><input type="text" style="width: 150px;" class="form-control param_title" value="<?php  echo $driverItem['phone'];?>" readonly="readonly"/></td>
									<td><input type="text" style="width: 150px;" class="form-control param_title" value="<?php  echo $driverItem['carsn'];?>" readonly="readonly"/></td>
									<td>
										<select name="status[]" class='form-control corpteam-status' style="width:120px;padding: 0px;height: fit-content;">
											<option value="1"<?php  if($driverItem['status']==1) { ?> selected="selected"<?php  } ?>>启用</option>
											<option value="0"<?php  if($driverItem['status']==0) { ?> selected="selected"<?php  } ?>>未启用</option>
										</select>
									</td>
									<td>
										<div class="input-group form-group" style="margin-bottom: 0px; margin: 0 0 0 5px; width: 180px;">
										<span class="input-group-addon">￥</span>
										<input type="text" name="fee[]" style="width: 120px;" class="form-control param_value" value="<?php  echo $driverItem['amount'];?>" />
										</div>
									</td>
								</tr>
								<?php  } } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-xs-4"><input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-4" /></div>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>
<!--文件上传-->
<?php  } else if($operation == 'import') { ?>
<div class="main">
	<p class="text-danger">
		“批量导入用户 EXCEL”的表格必须是使用点击最新的 --> “下载用户 EXCEL”导出的表格的基础上修改<br><br>
		<a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('corpteamoption', array('token' => token()))?>">
			下载用户 EXCEL
		</a>
	</p>
	<form action="./index.php" method="post" class="form-horizontal" role="form"  enctype="multipart/form-data">
		<input type="hidden" name="c" value="site" />
		<input type="hidden" name="a" value="entry" />
		<input type="hidden" name="m" value="ewei_shopping" />
		<input type="hidden" name="do" value="corpteam" />
		<input type="hidden" name="op" value="import" />
		<div class="panel panel-info">
			<div class="panel-heading">批量导入用户 EXCEL</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户 Execl导入</label>
					<div class="col-sm-6 col-xs-6">
						<input type="file" name="corpteam" class="form-control" >
					</div>
				</div>
			</div>
		</div>
		<style>
			.label{cursor:pointer;}
		</style>
		<div class="panel panel-default">
			<div class="panel-body table-responsive">
				<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
				<input type="submit" class="btn btn-primary" value="立即导入" />
			</div>
		</div>
	</form>
</div>
<?php  } ?>
<script>
	$('.corpteam-status').select2();
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>