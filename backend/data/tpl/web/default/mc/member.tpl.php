<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'post') { ?> class="active"<?php  } ?>>
		<a href="<?php  echo url('mc/member/post', array('uid' => $uid));?>">编辑会员资料</a>
	</li>
	<li <?php  if($do == 'credit') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo url('mc/member/credit', array('uid' => $uid));?>">会员账户信息</a>
	</li>
	<li <?php  if($do == 'address') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo url('mc/member/address', array('uid' => $uid));?>">会员收货地址信息</a>
	</li>
	<li <?php  if($do=='orders') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo url('mc/member/orders', array('uid' => $uid));?>">订单记录</a>
	</li>
</ul>
<?php  if($do=='display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
		<input type="hidden" name="c" value="mc">
		<input type="hidden" name="a" value="member">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">UID</label>
				<div class="col-sm-6 col-md-8 col-lg-8 col-xs-12">
					<input type="text" class="form-control" name="uid" class="" value="<?php  echo $_GPC['uid'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机号码</label>
				<div class="col-sm-6 col-md-8 col-lg-8 col-xs-12">
					<input type="text" class="form-control" name="mobile" class="" value="<?php  echo $_GPC['mobile'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">昵称/姓名</label>
				<div class="col-sm-6 col-md-8 col-lg-8 col-xs-12">
					<input type="text" class="form-control" name="username" value="<?php  echo $_GPC['username'];?>" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">所属用户组</label>
				<div class="col-sm-6 col-md-8 col-lg-8 col-xs-12">
					<select name="groupid" class="form-control">
						<option value="" selected="selected">不限</option>
						<?php  if(is_array($groups)) { foreach($groups as $group) { ?>
						<option value="<?php  echo $group['groupid'];?>"<?php  if($group['groupid'] == $_GPC['groupid']) { ?> selected="selected"<?php  } ?>><?php  echo $group['title'];?></option>
						<?php  } } ?>
					</select>
				</div>
				<div class="pull-right col-xs-12 col-sm-3 col-md-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
		</form>
	</div>
</div>
<form method="post" class="form-horizontal" id="form1">
<div class="panel panel-default ">
	<div class="panel-heading">
		会员（当前搜索到 <label class="text text-danger"><?php  echo $total;?></label> 条数据）
	</div>
	<div class="table-responsive panel-body">
	<table class="table table-hover">
		<input type="hidden" name="do" value="del" />
		<thead class="navbar-inner">
			<tr>
				<th style="width:44px;">删?</th>
				<th style="width:80px;">会员编号</th>
				<th style="width:100px;">所属会员组</th>
				<th style="min-width:100px;">手机</th>
				<th style="width:150px;">昵称</th>
				<th style="width:150px;">真实姓名</th>
				<th style="width:100px;">红包</th>
				<th style="width:100px;">余额</th>
				<th style="min-width:90px;">注册时间</th>
				<th style="min-width:90px;">操作</th>
			</tr>
		</thead>
		<tbody>
		<?php  if(is_array($list)) { foreach($list as $li) { ?>
			<tr>
				<td><input type="checkbox" name="uid[]" value="<?php  echo $li['uid'];?>" class=""></td>
				<td><?php  echo $li['uid'];?></td>
				<td><?php  echo $groups[$li['groupid']]['title'];?></td>
				<td><?php  if($li['mobile']) { ?><?php  echo $li['mobile'];?><?php  } else { ?>未完善<?php  } ?></td>
				<td><?php  if($li['nickname']) { ?><?php  echo $li['nickname'];?><?php  } else { ?>未完善<?php  } ?></td>
				<td><?php  if($li['realname']) { ?><?php  echo $li['realname'];?><?php  } else { ?>未完善<?php  } ?></td>
				<td><?php  echo $li['credit1'];?></td>
				<td><?php  echo $li['credit2'];?></td>
				<td><?php  echo date('Y-m-d H:i',$li['createtime'])?></td>
				<td><a href="<?php  echo url('mc/member/post',array('uid' => $li['uid']))?>" data-toggle="tooltip" data-placement="top" title="编辑" class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a></td>
			</tr>
		<?php  } } ?>
		<tr>
			<td><input type="checkbox" name="" onclick="var ck = this.checked;$(':checkbox').each(function(){this.checked = ck});"></td>
			<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
			<td colspan="9"><input type="submit" name="submit" class="btn btn-primary" value="删除"></td>
		</tr>
		</tbody>

	</table>
</div>
</div>
	<?php  echo $pager;?>
</form>
<script>
require(['jquery', 'util'], function($, u){
	$('#form1').submit(function(){
		if($(":checkbox[name='uid[]']:checked").size() > 0){
			return confirm('删除后不可恢复，您确定删除吗？');
		}
		u.message('没有选择会员', '', 'error');
		return false;
	});
	$('.btn').hover(function(){
		$(this).tooltip('show');
	},function(){
		$(this).tooltip('hide');
	});
});
</script>
<?php  } ?>
<?php  if($do=='post') { ?>
<div class="main">
<form action="" class="form-horizontal form" method="post" enctype="multipart/form-data">
	<div class="panel panel-default">
		<input type="hidden" name="uid" value="<?php  echo $uid;?>" />
		<input type="hidden" name="fanid" value="<?php  echo $_GPC['fanid'];?>" />
		<input type="hidden" name="email_effective" value="<?php  echo $profile['email_effective'];?>" />
		<div class="panel-heading">基本资料</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">头像</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('avatar', $profile['avatar']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户UID</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" class="form-control" name="" value="<?php  echo $uid;?>" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">所在会员组</label>
				<div class="col-sm-9 col-xs-12">
					<select name="groupid" class="form-control">
						<?php  if(is_array($groups)) { foreach($groups as $group) { ?>
						<option value="<?php  echo $group['groupid'];?>" <?php  if($profile['groupid'] == $group['groupid']) { ?>selected<?php  } ?>><?php  echo $group['title'];?></option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">昵称</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('nickname',$profile['nickname']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">真实姓名</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('realname',$profile['realname']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">性别</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('gender',$profile['gender']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">生日</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('birth',array('year' => $profile['birthyear'],'month' => $profile['birthmonth'],'day' => $profile['birthday']));?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">户籍</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('reside',array('province' => $profile['resideprovince'],'city' => $profile['residecity'],'district' => $profile['residedist']));?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">详细地址</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('address',$profile['address']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('mobile',$profile['mobile']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">QQ</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('qq',$profile['qq']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">Email</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('email',$profile['email']);?>
				</div>
			</div>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">联系方式</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">固定电话</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('telephone',$profile['telephone']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">MSN</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('msn',$profile['msn']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">阿里旺旺</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('taobao',$profile['taobao']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付宝帐号</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('alipay',$profile['alipay']);?>
				</div>
			</div>
		</div>
	</div>
<!--
	<div class="panel panel-default">
		<div class="panel-heading">教育情况</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">学号</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('studentid',$profile['studentid']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">班级</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('grade',$profile['grade']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">毕业学校</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('graduateschool',$profile['graduateschool']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">学历</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('education',$profile['education']);?>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">工作情况</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">公司</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('company',$profile['company']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">职业</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('occupation',$profile['occupation']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">职位</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('position',$profile['position']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">年收入</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('revenue',$profile['revenue']);?>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">个人情况</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">星座</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('constellation',$profile['constellation']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">生肖</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('zodiac',$profile['zodiac']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">国籍</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('nationality',$profile['nationality']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">身高</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('height',$profile['height']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">体重</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('weight',$profile['weight']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">血型</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('bloodtype',$profile['bloodtype']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">身份证号</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('idcard',$profile['idcard']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">邮编</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('zipcode',$profile['zipcode']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">个人主页</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('site',$profile['site']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">情感状态</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('affectivestatus',$profile['affectivestatus']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">交友目的</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('lookingfor',$profile['lookingfor']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">自我介绍</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('bio',$profile['bio']);?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">兴趣爱好</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_fans_form('interest',$profile['interest']);?>
				</div>
			</div>
		</div>
	</div>
-->
	<?php  if(!empty($profile)) { ?>
	<div class="panel panel-default">
		<div class="panel-heading">修改密码(<span class="text-danger">暂不使用</span>)</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">新密码</label>
				<div class="col-sm-9 col-xs-12">
					<input type="password" class="form-control" name="newpwd" value="" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">重复新密码</label>
				<div class="col-sm-9 col-xs-12">
					<input type="password" class="form-control" name="rnewpwd" value="" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
				<div class="col-sm-9 col-xs-12">
					<a href="javascript:;" id="updatepwd" class="btn btn-primary">修改密码</a>
					<span class="label"></span>
				</div>
			</div>
		</div>
	</div>
	<?php  } ?>
	<div class="form-group">
		<div class="col-sm-12">
			<button type="submit" class="btn btn-primary col-lg-1" name="submit" value="提交">提交</button>
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</div>
</form>
</div>
<script type="text/javascript">
require(['jquery', 'util'], function($, u){
	$('#updatepwd').click(function(){
		var newpwd = $("input[name='newpwd']").val();
		var rnewpwd = $("input[name='rnewpwd']").val();
		if (newpwd.length < 6) {
			u.message('密码不得少于6个字符');
			return false;
		}
		if (newpwd != rnewpwd) {
			u.message('两次输入的密码不一致');
			return false;
		}
		var uid = window.location.href.substr(window.location.href.lastIndexOf('=') + 1);
		$.post(location.href, {uid:uid, password:newpwd}, function(resp){
			if(resp == 'success') {
				$('#updatepwd').next().removeClass().addClass('label label-success').text('密码修改成功');
			} else if (resp == 'error') {
				$('#updatepwd').next().removeClass().addClass('label label-danger').text('会员不存在或已删除');
			} else {
				$('#updatepwd').next().removeClass().addClass('label label-danger').text('网络错误，请稍后重试');
			}
		});

	});
});
</script>
<?php  } ?>

<?php  if($do=='credit') { ?>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th>UID</th>
					<th>昵称</th>
					<th>可消费余额</th>
					<th>累计消费额</th>
					<th>积分点数</th>
					<th>红包金额</th>
					<th>总分成金额</th>
					<th>可提现分成金额</th>
					<th>分销下线总消费额</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php  echo $memberInfo['uid'];?></td>
					<td><?php  echo $memberInfo['nickname'];?></td>
					<td><?php  echo $memberInfo['credits1'];?></td>
					<td><?php  echo $memberInfo['credits2'];?></td>
					<td><?php  echo $memberInfo['credits3'];?></td>
					<td><?php  echo $memberInfo['credits4'];?></td>
					<td><?php  echo $memberInfo['credits5'];?></td>
					<td><?php  echo $memberInfo['credits6'];?></td>
					<td><?php  echo $memberInfo['credits7'];?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<?php  } ?>

<?php  if($do == 'address') { ?>
<div class="clearfix">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="mc" />
				<input type="hidden" name="a" value="member" />
				<input type="hidden" name="do" value="address" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户ID</label>
					<div class="col-sm-5 col-md-3 col-lg-3 col-xs-5">
						<input type="text" class="form-control" name="uid" value="<?php  echo $_GPC['uid'];?>"/>
					</div>
					<div class="col-sm-5 col-md-3 col-lg-3 col-xs-5">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form action="?<?php  echo $_SERVER['QUERY_STRING'];?>" method="post" id="form1">
		<div class="panel-heading">
			用户地址 （当前搜索到 <label class="text text-danger"><?php  echo $res['total'];?></label> 条数据）
		</div>
		<div class="panel panel-default">
			<div class="panel-body table-responsive" ng-controller="advAPI">
				<table class="table table-hover"  style="width:100%;z-index:-10;" cellspacing="0" cellpadding="0">
					<thead class="navbar-inner">
						<tr>
							<th width=100>UID</th>
							<th width=100>收货人</th>
							<th width=120>联系方式</th>
							<th width=80>省</th>
							<th width=80>市</th>
							<th width=80>区</th>
							<th>详细地址</th>
							<th width=85>默认地址</th>
							<th width=75>已删除</th>
							<th width=180>创建时间</th>
							<th width=85>操作选项</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($res['list'])) { foreach($res['list'] as $item) { ?>
						<tr>
							<td><?php  echo $item['uid'];?></td>
							<td><?php  echo $item['realname'];?></td>
							<td><?php  echo $item['mobile'];?></td>
							<td><?php  echo $item['province'];?></td>
							<td><?php  echo $item['city'];?></td>
							<td><?php  echo $item['area'];?></td>
							<td><?php  echo $item['address'];?></td>
							<td><?php echo ($item['isdefault']==1)?'<span class="label label-danger">&nbsp;是&nbsp;</span>':'备用';?></td>
							<td><?php echo ($item['deleted']==1)?'<span class="label label-danger">&nbsp;是&nbsp;</span>':'正常';?></td>
							<td><?php  echo $item['createdt'];?></td>
							<td>
								<a href="javascript:;" OnClick="deladdress('<?php  echo $item['openid'];?>','<?php  echo $item['id'];?>')" data-placement="top" data-toggle="tooltip" title="删除" class="btn btn-default btn-sm">
									<i class="fa fa-times"></i>
								</a>
							</td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php  echo $res['pager'];?>
	</form>
</div>
<?php  } ?>


<?php  if($do == 'orders' ) { ?>
<div class="clearfix">
	<div class="panel panel-info">
		<div class="panel-heading">筛选</div>
		<div class="panel-body">
			<form action="./index.php" method="get" class="form-horizontal" role="form">
				<input type="hidden" name="c" value="mc" />
				<input type="hidden" name="a" value="member" />
				<input type="hidden" name="do" value="orders" />
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户ID</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-5">
						<input type="text" class="form-control" name="uid" value="<?php  echo $_GPC['uid'];?>"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单状态</label>
					<div class="col-sm-9 col-md-8 col-lg-8 col-xs-5">
						<select class="form-control" name="status" id="status">
							<option value="">所有</option>
							<option value="0"<?php  if($_GPC['status']=='0') { ?> selected="selected"<?php  } ?>>未支付</option>
							<option value="1"<?php  if($_GPC['status']=='1') { ?> selected="selected"<?php  } ?>>未发货</option>
							<option value="2"<?php  if($_GPC['status']=='2') { ?> selected="selected"<?php  } ?>>待收</option>
							<option value="3"<?php  if($_GPC['status']=='3') { ?> selected="selected"<?php  } ?>>异常关闭</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="pull-right col-xs-12 col-sm-3 col-md-2 col-lg-2">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<form action="?<?php  echo $_SERVER['QUERY_STRING'];?>" method="post" id="form1">
		<div class="panel-heading">
			用户订单 （当前搜索到 <label class="text text-danger"><?php  echo $total;?></label> 条数据）
		</div>
		<div class="panel panel-default">
			<div class="panel-body table-responsive" ng-controller="advAPI">
				<table class="table table-hover"  style="width:100%;z-index:-10;" cellspacing="0" cellpadding="0">
					<thead class="navbar-inner">
						<tr>
							<th width=70>ID</th>
							<th width=70>UID</th>
							<th width=160>订单号</th>
							<th width=80>订单金额</th>
							<th width=80>订单状态</th>
							<th width=80>支付方式</th>
							<th width=110>创建时间</th>
							<th width=110>发货时间</th>
							<th width=110>收货时间</th>
							<th width=80>配送方式</th>
							<th width=40>社区订单</th>
							<th width=85>操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($res)) { foreach($res as $item) { ?>
						<tr>
							<td><?php  echo $item['id'];?></td>
							<td><?php  echo $item['uid'];?></td>
							<td><?php  echo $item['ordersn'];?></td>
							<td><?php  echo $item['price'];?></td>
							<td><?php  echo $item['OrderType'];?></td>
							<!--
							<td><?php  if($item['status']=='0') { ?>生成订单
								<?php  } else if($item['cancelgoods']=='1') { ?>售后申请
								<?php  } else if($item['status']=='1') { ?>待发货
								<?php  } else if($item['status']=='2') { ?>代收货
								<?php  } else if($item['status']=='3') { ?>手动收货
								<?php  } else if($item['status']=='4') { ?>自动收货
								<?php  } else if($item['status']=='-1') { ?>订单取消
								<?php  } else if($item['status']=='-2') { ?>订单关闭
								<?php  } ?>
							</td>
							-->
							<td><?php  if($item['paytype']=='1') { ?>余额
								<?php  } else if($item['paytype']=='2') { ?>在线
								<?php  } else if($item['paytype']=='3') { ?>到付
								<?php  } ?>
							</td>
							<td><?php  if($item['createtime']) echo date('y-m-d H:i:s', $item['createtime']);?></td>
							<td><?php  if($item['sendexpress']) echo date('y-m-d H:i:s', $item['createtime']);?></td>
							<td><?php  if($item['receipttime']) echo date('y-m-d H:i:s', $item['createtime']);?></td>
							<td><?php  if($item['sendtype']=='2') { ?>自提<?php  } else { ?><?php  echo $item['expresssn'];?><?php  } ?></td>
							<td><?php echo $item['community']?'是':'否';?></td>
							<td>
								<a class="btn btn-primary" href="<?php  echo url('index.php', array('c'=>'site', 'a'=>'entry', 'op'=>'detail', 'id'=>$item['id'], 'do'=>'order', 'm'=>'ewei_shopping'));?>" target="_blank">查看详情</a>
							</td>
						</tr>
						<?php  } } ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php  echo $pager;?>
	</form>
</div>
<?php  } ?>
<script type="text/javascript">
	require(['bootstrap'],function($){
			$(".btn").hover(function(){
				$(this).tooltip('show');
			},function(){
				$(this).tooltip('hide');
			})
		});

function deladdress(openid,addid){
	if(confirm("是否删除？！")){
		if(openid !== undefined && addid !== undefined){
		$.get(location.href, {uid : openid, addid : addid} , function(data){
				if(data.status == 200){
					location.reload(location.href);
				} else{
					alert("删除出现了问题！");
					return false;
				}
			},'json')
		}
	}
	
}
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>