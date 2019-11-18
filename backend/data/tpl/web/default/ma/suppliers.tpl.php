<?php defined('IN_IA') or exit('Access Denied');?><?php  $newUI = true;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ol class="breadcrumb" style="padding:5px 0;">
	<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; <?php  echo $module_types;?></a></li>
	<li><a href="<?php  echo url('home/welcome/ext', array('m' => $module['name']));?>"><?php  echo $module;?></a></li>
</ol>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/suppliers-nav', TEMPLATE_INCLUDEPATH)) : (include template('common/suppliers-nav', TEMPLATE_INCLUDEPATH));?>
<?php  if($operation == 'display') { ?>
<div class="panel panel-default">
	<div class="panel-body table-responsive" >
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th>ID</th>
					<th>联系人</th>
					<th>公司</th>
					<th>电话</th>
					<th>手机</th>
					<th>状态</th>
					<th>邮箱</th>
					<th>QQ</th>
					<th>网站</th>
					<th width=200>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr class="list" data-sid='<?php  echo $item["sid"];?>' data-state='<?php  echo $item["status"];?>'>
					<td class="redpacket-cid"><?php  echo $item['sid'];?></td>
					<td><?php  echo $item['linkman'];?></td>
					<td><?php  echo $item['company'];?></td>
					<td><?php  echo $item['tel'];?></td>
					<td><?php  echo $item['mobile'];?></td>
					<td >
						<?php  if($item['status'] == '1') { ?>
						<span class="btn btn-success btn-sm btn-contry on">正常</span>
						<?php  } else if($item['status'] == '0') { ?>
						<span class="btn btn-danger btn-sm btn-contry">禁用</span>
						<?php  } ?>
					</td>
					<td><?php  echo $item['email'];?></td>
					<td><?php  echo $item['qq'];?></td>
					<td><?php  echo $item['site'];?></td>
					<td>
						<a class="btn btn-info btn-sm" href="<?php  echo url('ma/suppliers/address',array('sid'=>$item['sid']))?>">地址</a>
						<a class="btn btn-info btn-sm" href="<?php  echo url('ma/suppliers/suppliers',array('sid'=>$item['sid'], 'op' => 'handle'))?>">编辑</a>
					</td>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $pager;?>
<?php  } else if($operation == 'handle') { ?>
<?php  if($id) { ?>
<div class="panel panel-default">
	<div class="panel-heading">供应商信息</div>
	<div class="panel-body">
		<div class="form-group" style="float:left;width:30%">
			<label style="width:90px" class="ol-xs-5 col-md-3 control-label">公司：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($item['company'] == "") { ?>无<?php  } else { ?><?php  echo $item['company'];?><?php  } ?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:30%">
			<label style="width:90px" class="ol-xs-5 col-md-3 control-label">联系人：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($item['linkman'] == "") { ?>无<?php  } else { ?><?php  echo $item['linkman'];?><?php  } ?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:30%">
			<label style="width:90px" class="ol-xs-5 col-md-3 control-label">手机：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($item['mobile'] == "") { ?>无<?php  } else { ?><?php  echo $item['mobile'];?><?php  } ?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:30%">
			<label style="width:90px" class="ol-xs-5 col-md-3 control-label">电话：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($item['tel'] == "") { ?>无<?php  } else { ?><?php  echo $item['tel'];?><?php  } ?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:30%">
			<label style="width:90px" class="ol-xs-5 col-md-3 control-label">邮箱：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($item['email'] == "") { ?>无<?php  } else { ?><?php  echo $item['email'];?><?php  } ?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:30%">
			<label style="width:90px" class="ol-xs-5 col-md-3 control-label">QQ：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($item['qq'] == "") { ?>无<?php  } else { ?><?php  echo $item['qq'];?><?php  } ?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:30%">
			<label style="width:90px" class="ol-xs-5 col-md-3 control-label">主页：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($item['site'] == "") { ?>无<?php  } else { ?><?php  echo $item['site'];?><?php  } ?></span>
			</div>
		</div>
	</div>
</div>
<?php  } ?>
<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading">
			<?php  echo $ptr_title;?>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">公司：</label>
				<div class="col-sm-9 col-xs-12">
					<input  class="form-control" name="company" value="<?php  echo $item['company'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">联系人：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="linkman" value="<?php  echo $item['linkman'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="mobile" value="<?php  echo $item['mobile'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">电话：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="tel" value="<?php  echo $item['tel'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">邮箱：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="email" value="<?php  echo $item['email'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">QQ：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="qq" value="<?php  echo $item['qq'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">主页：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="site" value="<?php  echo $item['site'];?>">
				</div>
			</div>
			<div class="form-group col-sm-12">
				<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
				<input type="hidden" name="said" value="<?php  echo $item['said'];?>">
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</div>
</form>
<?php  } ?>
<script>
	var $start = $('.btn-contry'),
			cid, type, url;
		$start.on('click',function(){
			var $this = $(this);
			type = ($this.hasClass('on')) ? 1 : 0;  //1 正常状态 0 禁用状态
			sid = $this.parents('tr').find('.redpacket-cid').text();
			$.ajax({
				type: 'post',
				url: location.href,
				data: {sid: sid, type: type},
				dataType:'json',
				success: function(data) {
					console.log(data)
					if (data.status == 200) {
						if (data.type == 1) {
							$this.text('正常').addClass('on').removeClass('btn-danger');
						} else {
							$this.text('禁用').removeClass('on').addClass('btn-danger');
						}
					} else {
						alert(data.msc);
					}
				}
			});
		});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>