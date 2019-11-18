<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php  if($issolution == 1) { ?>
	<ol class="breadcrumb" style="padding:5px 0;">
		<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; 扩展功能</a></li>
		<li><a href="<?php  echo url('home/welcome/ext', array('m' => $m));?>"><?php  echo $module_types[$module['type']]['title'];?>模块 - <?php  echo $module['title'];?></a></li>
		<li class="active">操作人员列表</li>
	</ol>
<?php  } ?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo url('profile/worker');?>">操作人员列表</a></li>
</ul>
<div class="panel panel-default">
<div class="panel-body table-responsive">
	<?php  if($issolution == 1) { ?>
		<table class="table">
			<thead>
				<tr>
					<th style="width:80px;">用户编号</th>
					<th style="width:200px;">用户名</th>
					<th>角色</th>
					<th style="width:150px;">操作</th>
				</tr>
			</thead>
			<?php  if(is_array($works)) { foreach($works as $row) { ?>
				<tr>
					<td><?php  echo $row['uid'];?></td>
					<td><?php  echo $member[$row['uid']]['username'];?></td>
					<td>
						<?php  if($row['role'] == 'operator') { ?>
							<span class="label label-warning">操作员</span>
						<?php  } else if($row['role'] == 'manager') { ?>	
							<span class="label label-success">管理员</span>
						<?php  } ?>
					</td>
					<td>
						<a href="<?php  echo url('profile/permission', array('m' => $m, 'uid' => $row['uid']))?>">设置权限</a>
					</td>
				</tr>
			<?php  } } ?>
		</table>
	<?php  } else { ?>
		<table class="table">
			<thead>
				<tr>
					<th style="width:50px;">选择</th>
					<th style="width:80px">用户编号</th>
					<th style="width:200px;">用户名</th>
					<th>角色</th>
				</tr>
			</thead>
			<?php  if(is_array($works)) { foreach($works as $row) { ?>
				<tr>
					<td class="row-first"><input class="member" type="checkbox" value="<?php  echo $row['id'];?>" /></td>
					<td><?php  echo $row['uid'];?></td>
					<td><?php  echo $member[$row['uid']]['username'];?></td>
					<td>
						<?php  if($row['role'] == 'operator') { ?>
							<span class="label label-success">操作员</span>
						<?php  } else if($row['role'] == 'manager') { ?>	
							<span class="label label-warning">管理员</span>
						<?php  } ?>
					</td>
				</tr>
			<?php  } } ?>
			<tfoot>
				<tr>
					<td colspan="4">
						<input id="btn-add" class="btn btn-primary" type="button" value="添加账号操作员">
						<input id="btn-revo" class="btn btn-default" type="button" value="删除选定操作">
						<a class="btn" href="#" onclick="addUserPanel(this)">如果是添加一个新用户，请先添加该用户</a>
					</td>
				</tr>
			</tfoot>
		</table>
	<?php  } ?>
</div>
</div>
<script type="text/javascript">
var seletedUserIds = <?php  echo json_encode($uids);?>;
require(['biz'], function(biz){
	$(function(){
		$('#btn-add').click(function(){
			<?php  if($_W['isfounder']) { ?>
				biz.user.browser(seletedUserIds, function(us){
					$.post('<?php  echo url('account/permission', array('uniacid' => $_W['uniacid'], 'reference' => $_GPC['reference']));?>', {'do': 'auth', uid: us}, function(dat){
						if(dat == 'success') {
							location.href = location.href;
						} else {
							alert('操作失败, 请稍后重试, 服务器返回信息为: ' + dat);
						}
					});
				},{mode:'invisible'});
			<?php  } else { ?>
				u.message('抱歉，您无法添加操作人员，请联系系统管理员为您添加.');
			<?php  } ?>
		});
		$('#btn-revo').click(function(){
			<?php  if($_W['isfounder']) { ?>
			$chks = $(':checkbox.member:checked');
			if($chks.length >0){
				if(!confirm('确认删除当前选择的用户?')){
					return;
				}
				var ids = [];
				$chks.each(function(){
					ids.push(this.value);
				});
				$.post('<?php  echo url('account/permission', array('uniacid' => $uniacid));?>',{'do':'revos', 'ids': ids},function(dat){
					if(dat == 'success') {
						location.href = location.href;
					} else {
						alert('操作失败, 请稍后重试, 服务器返回信息为: ' + dat);
					}
				});
			}
			<?php  } else { ?>
			u.message('抱歉，您无法添加操作人员，请联系系统管理员为您添加.');
			<?php  } ?>
		});
	});
});

function addUserPanel() {
	require(['util'], function(util){
		util.ajaxshow('<?php  echo url('user/create');?>', '添加管理员', {'width': 800});
	});
}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
