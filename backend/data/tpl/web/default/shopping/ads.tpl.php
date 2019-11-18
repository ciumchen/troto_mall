<?php defined('IN_IA') or exit('Access Denied');?><?php  $newUI = true;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ol class="breadcrumb" style="padding:5px 0;">
	<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; 微商城</a></li>
	<li>商城功能</li>
	<li>广告幻灯片管理</li>
</ol>

<ul class="nav nav-tabs">
	<li <?php  if($do == 'display' && $operation == 'display') { ?> class="active" <?php  } ?>>
		<a href="<?php  echo url('shopping/ads', array('m'=>'ewei_shopping'))?>">广告幻灯片管理</a>
	</li>
	<li <?php  if($do == 'add') { ?> class="active"<?php  } ?>>
		<a href="<?php  echo url('shopping/ads/add', array('op'=>'handle', 'm'=>'ewei_shopping'))?>"><?php  if($adid) { ?>修改<?php  } else { ?>添加<?php  } ?>广告幻灯片</a>
	</li>
	<?php  if($do == 'detail' && $operation == 'display') { ?><li  class="active"><a href="##">广告幻灯片详情</a></li>
	<?php  } ?>
	<li <?php  if($do == 'address' && $operation == 'handle') { ?> class="active" <?php  } ?>>
		<a href="<?php  echo url('shopping/ads/address', array('op'=>'handle', 'm'=>'ewei_shopping'))?>">
			<?php  echo $ptr_title;?>
		</a>
	</li>
</ul>
<?php  if($do=='display' && $operation=='display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal order-horizontal" role="form" id="form1">
			<input type="hidden" name="c" value="shopping" />
			<input type="hidden" name="a" value="ads" />
			<input type="hidden" name="m" value="ewei_shopping" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">广告幻灯片名称</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="advname" id="" type="text" value="<?php  echo $_GPC['advname'];?>" placeholder="">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">启用状态</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<select name="enabled" class="form-control">
						<option value="">不限</option>
						<option value="0" <?php  if($_GPC['enabled']=='0') { ?> selected="selected" <?php  } ?>>禁用</option>
						<option value="1" <?php  if($_GPC['enabled']=='1') { ?> selected="selected" <?php  } ?>>启用</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">展示有效期</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $list['param']['starttime']),'endtime'=>date('Y-m-d', $list['param']['endtime'])));?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">广告类型</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<select name="type" class="form-control">
						<option value="">不限</option>
						<?php  if(is_array($adsTypeList)) { foreach($adsTypeList as $key => $type) { ?>
						<option value="<?php  echo $key;?>" <?php  if(isset($_GPC['type']) && $_GPC['type'] != '' && $_GPC['type'] == $key) { ?> selected="selected" <?php  } ?>>
							<?php  echo $type;?>
						</option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">单页查询数量</label>
				<div class="col-sm-7 col-lg-9 col-xs-12">
					<select class="form-control" style="margin-right:15px;width:100px;" name="psize" >
						<option value="10"<?php  if($_GPC['psize']=='10'||!$_GPC['psize']) { ?> selected="selected"<?php  } ?>>10条</option>
						<option value="20"<?php  if($_GPC['psize']=='20') { ?> selected="selected"<?php  } ?>>20条</option>
						<option value="30"<?php  if($_GPC['psize']=='30') { ?> selected="selected"<?php  } ?>>30条</option>
						<option value="50"<?php  if($_GPC['psize']=='50') { ?> selected="selected"<?php  } ?>>50条</option>
						<option value="100"<?php  if($_GPC['psize']=='100') { ?> selected="selected"<?php  } ?>>100条</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-3 col-lg-2">
					<button class="btn btn-default">
						<i class="fa fa-search"></i> 搜索
					</button>
				</div>
			</div>
		</form>
	</div>
	</div>
<div class="panel panel-default">
	<div class="panel-heading">
		广告幻灯片（当前搜索到 <label class="text text-danger"><?php  echo $list['total'];?></label> 条数据）<br>PS. 首页展示使用逆序；&nbsp;&nbsp;&nbsp;只有在展示日期内、且状态为启用的广告幻灯片才会被显示
	</div>
	<div class="panel-body table-responsive" >
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th>ID</th>
					<th>广告幻灯片名称</th>
					<th>显示顺序</th>
					<th>广告类型</th>
					<th style="width:320px;">展示日期(开始时间--结束时间)</th>
					<th>状态</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list['list'])) { foreach($list['list'] as $item) { ?>
				<tr class="list" data-sid='<?php  echo $item["adid"];?>' data-state='<?php  echo $item["status"];?>'>
					<td><?php  echo $item['adid'];?></td>
					<td><?php  echo $item['advname'];?></td>
					<td><?php  echo $item['displayorder'];?></td>
					<td><?php  echo getAdsTypeStr($item['type'])?></td>
					<td><?php  echo date('Y-m-d H:i:s', $item['starttime'])?> -- <?php  echo date('Y-m-d H:i:s', $item['endtime'])?></td>
					<td>
						<?php  if($item['enabled'] == '1') { ?>
						<span class="btn btn-success btn-sm">启用</span>
						<?php  } else if($item['enabled'] == '0') { ?>
						<span class="btn btn-danger btn-sm">禁用</span>
						<?php  } ?>
					</td>
					<td>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/ads',array('adid'=>$item['adid'],'do'=>'add','op'=>'handle', 'm'=>'ewei_shopping'))?>">编辑</a>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/ads',array('adid'=>$item['adid'],'do'=>'detail', 'm'=>'ewei_shopping'))?>">详情</a>
					</td>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $list['pager'];?>
<?php  } else if($do=='detail' && $operation=='display') { ?>
<div class="panel panel-default">
	<div class="panel-heading"><?php  echo $op_type;?>广告幻灯片详情</div>
	<div class="panel-body">
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">广告幻灯片ID：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['adid'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">广告幻灯片名称：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['advname'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">启用状态：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php echo $detailData['enabled']?'启用':'禁用'?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">显示顺序：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['displayorder'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">开始时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['starttime'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">结束时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['endtime'])?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">广告类型：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo getAdsTypeStr($detailData['type'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">跳转链接：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['link'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-4 control-label">缩略图：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['thumb'];?></span>
			</div>
		</div>
	</div>
</div>
<?php  } else if($operation == 'handle') { ?>
<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading"><?php  echo $op_type;?> 广告幻灯片信息</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">广告幻灯片名称</label>
				<div class="col-sm-9 col-xs-12">
					<input  class="form-control" name="advname" value="<?php  echo $item['advname'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">启用状态</label>
				<div class="col-sm-9 col-xs-12">
					<label for="enabled" class="radio-inline"><input type="radio" name="enabled" value="0" id="enabled" <?php  if(empty($item) || $item['enabled'] == 0) { ?>checked="true"<?php  } ?> /> 禁用</label>
					&nbsp;&nbsp;&nbsp;
					<label for="enabled" class="radio-inline"><input type="radio" name="enabled" value="1" id="enabledenabled"  <?php  if(!empty($item) && $item['enabled'] == 1) { ?>checked="true"<?php  } ?> /> 启用</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">展示顺序</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="displayorder" value="<?php  echo $item['displayorder'];?>" style="width:85px">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">广告类型</label>
				<div class="col-sm-9 col-xs-12">
					<select name="type" class="form-control">
						<?php  if(is_array($adsTypeList)) { foreach($adsTypeList as $adk => $adv) { ?>
						<option value="<?php  echo $adk;?>" <?php  if(isset($item['type']) && $item['type'] != '' && $item['type'] == $adk) { ?> selected="selected"<?php  } ?>><?php  echo $adv;?>
						</option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">广告幻灯片链接</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="link" value="<?php  echo $item['link'];?>">
					<span class="help-block"><strong style="color:red">注意: </strong>如果是频道页面，填写频道页面url；否则，请置空</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">广告幻灯片图片</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_form_field_image('thumb', $item['thumb'], '', array('extras' => array('text' => 'readonly')))?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">有效时间</label>
				<div class="col-sm-4 col-xs-6"><?php echo tpl_form_field_date('starttime', !empty($item['starttime']) ? date('Y-m-d H:i',$item['starttime']) : date('Y-m-d H:i'), 1)?></div>
				<div class="col-sm-4 col-xs-6"><?php echo tpl_form_field_date('endtime', !empty($item['endtime']) ? date('Y-m-d H:i',$item['endtime']) : date('Y-m-d H:i', strtotime('+5 days')), 1)?></div>
			</div>
			<div class="form-group col-sm-12">
				<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</div>
</form>
<?php  } ?>
<script>
$(function(){
	var $fetchItem = $('.fetch-item');
	 $fetchItem.on('change',function(){
	 	var Item = $(this).children();
	 	//console.log(Item)
	 	Item.each(function(){
	 		if($(this).get(0).selected == true){	
	 			var this_Id = $(this).val(),
	 				fetchMobile = $('.fetch-mobile');
	 			fetchMobile.each(function(){
	 				var $this = $(this);
	 				 if($this.attr('data-rol') == this_Id){
			 				var fetchMobileTxt = $this.val();
			 				$("#suppliers-mobile").show();
			 				$this.parent('div').children(":first").val(fetchMobileTxt);
			 		 }
	 			})
	 			if($(this).get(0).index === 0){
	 					fetchMobile.parent('div').children(":first").val("");
	 					$("#suppliers-mobile").hide();
	 			}	
	 		}
	 		
	 	})
	 })
})
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>