<?php defined('IN_IA') or exit('Access Denied');?><?php  $newUI = true;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ol class="breadcrumb" style="padding:5px 0;">
	<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; 微商城</a></li>
	<li>商城功能</li>
	<li>优惠券管理</li>
</ol>

<ul class="nav nav-tabs">
	<li <?php  if($operation=='display') { ?> class="active" <?php  } ?>>
		<a href="<?php  echo url('shopping/coupon/manage', array('m'=>'ewei_shopping'))?>">优惠券管理</a>
	</li>
	<?php  if($id) { ?>
	<li <?php  if($operation == 'handle') { ?> class="active"<?php  } ?>>
		<a href="<?php  echo url('shopping/coupon/manage', array('op'=>'handle', 'm'=>'ewei_shopping', 'id'=>$id))?>">修改优惠券</a>
	</li>
	<?php  } ?>
	<?php  if($operation=='detail') { ?><li  class="active"><a href="##">优惠券详情</a></li>
	<?php  } ?>
	<li><a data-toggle="modal" data-target="#modal-close">批量生成优惠券</a></li>
</ul>
<?php  if($operation=='display') { ?>
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal order-horizontal" role="form" id="form1">
			<input type="hidden" name="c" value="shopping" />
			<input type="hidden" name="a" value="coupon" />
			<input type="hidden" name="do" value="manage" />
			<input type="hidden" name="m" value="ewei_shopping" />
			<?php  if(isset($_GPC['status'])){?>
			<input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>" />
			<?php  }?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">优惠券编号</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="sn" id="" type="text" value="<?php  echo $_GPC['sn'];?>" placeholder="可输入部分模糊查询">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">优惠券类型</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<select name="typeid" class="form-control">
						<option value="">不限</option>
						<?php  if(is_array($list['type'])) { foreach($list['type'] as $key => $type) { ?>
						<option value="<?php  echo $type['id'];?>" <?php  if(isset($_GPC['typeid']) && $_GPC['typeid']!='' && $_GPC['typeid']==$type['id']) { ?> selected="selected" <?php  } ?>><?php  echo $type['name'];?></option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户取得时间</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $list['param']['starttime']),'endtime'=>date('Y-m-d', $list['param']['endtime'])));?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">优惠券来源</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<select name="source" class="form-control">
						<option value="">不限</option>
						<?php  if(is_array($list['source'])) { foreach($list['source'] as $key => $source) { ?>
						<option value="<?php  echo $key;?>" <?php  if(isset($_GPC['source']) && $_GPC['source']!='' && $_GPC['source']==$key) { ?> selected="selected" <?php  } ?>>
							<?php  echo $source;?>
						</option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">单页数量</label>
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
		优惠券（当前搜索到 <label class="text text-danger"><?php  echo $list['total'];?></label> 条数据）
	</div>
	<?php  //echo pre($list)?>
	<div class="panel-body table-responsive" >
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:70px;">ID</th>
					<th>优惠券编号</th>
					<th>优惠券类型名称</th>
					<th style="width:70px;">面额</th>
					<th>来源途径</th>
					<th>状态</th>
					<th>创建时间</th>
					<th>用户取得时间</th>
					<th style="width:320px;">优惠券有效期</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list['list'])) { foreach($list['list'] as $item) { ?>
				<tr class="list" data-sid='<?php  echo $item["topicid"];?>' data-state='<?php  echo $item["status"];?>'>
					<td><?php  echo $item['id'];?></td>
					<td><?php  echo $item['no'];?></td>
					<td><?php  echo $item['name'];?></td>
					<td><?php  echo $item['value'];?></td>
					<td><?php  echo getSourceTypeStr($item['source'])?></td>
					<td><span class="label label-info"><?php  echo getStatusTypeStr($item['status'])?></span></td>
					<td><?php  echo date('Y-m-d H:i:s', $item['create_time'])?></td>
					<td><?php echo ($item['got_time']>100000) ? date('Y-m-d H:i:s', $item['got_time']) : ''?></td>
					<td><?php  echo date('Y-m-d H:i:s', $item['expire_begin'])?> - <?php  echo date('Y-m-d H:i:s', $item['expire_end'])?></td>
					<td>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/coupon/manage',array('id'=>$item['id'], 'op'=>'handle','m'=>'ewei_shopping'))?>">编辑</a>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/coupon/manage',array('id'=>$item['id'],'op'=>'detail'))?>">详情</a>
						<!-- <a class="btn btn-info btn-sm" href="<?php  echo url('site/entry/order', array('op'=>'detail', 'id'=>$item['order_id'], 'm'=>'ewei_shopping'))?>">消费订单</a> -->
					</td>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $list['pager'];?>

<?php  } else if($operation=='detail') { ?>
<div class="panel panel-default">
	<div class="panel-heading"><?php  echo $op_type;?>优惠券详情</div>
	<div class="panel-body">
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">优惠券ID：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['id'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">类型名称：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['name'];?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">券码：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="font-weight:bold;"><?php  echo $detailData['no'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">面额：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['value'];?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">创建时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['create_time'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">有效期：</label>
			<div class="col-sm-7">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['expire_begin'])?> -- <?php  echo date('Y-m-d H:i:s', $detailData['expire_end'])?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">当前状态：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo getStatusTypeStr($detailData['status'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">来源：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo getSourceTypeStr($detailData['source'])?></span>
			</div>
		</div>

		<?php  if($detailData['threshold']) { ?>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">订单满额限制：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['threshold'];?></span>
			</div>
		</div>
		<?php  } ?>
		<?php  if($detailData['goodsid']) { ?>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-4 control-label">限制使用商品：</label>
			<div class="col-sm-12">
				<span><a target="_blank" href="<?php  echo url('site/entry/goods', array('op'=>'post', 'id'=>$detailData['goodsid'],'m'=>'ewei_shopping','id'=>$detailData['goodsid']))?>">查看订单详情 <?php  echo $detailData['goodsid'];?></a></span>
			</div>
		</div>
		<?php  } ?>
		<?php  if($detailData['uid']) { ?>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">获得用户：</label>
			<div class="col-sm-2" style="width:180px">
				<span><?php  echo $detailData['uid'];?></span>
			</div>
		</div>
		<?php  } ?>
		<?php  if($detailData['got_time']) { ?>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">用户获得时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span><?php  echo date('Y-m-d H:i:s', $detailData['got_time'])?></span>
			</div>
		</div>
		<?php  } ?>
		<?php  if($detailData['order_id']) { ?>
		<div class="form-group" style="float:left;width:45%">
			<label class="col-xs-12 col-md-3 control-label">消费订单：</label>
			<div class="col-sm-2" style="width:180px">
				<span><a target="_blank" href="<?php  echo url('site/entry/order',array('op'=>'detail','m'=>'ewei_shopping','id'=>$detailData['order_id']))?>">查看订单详情 <?php  echo $detailData['order_id'];?></a></span>
			</div>
		</div>
		<?php  } ?>
	</div>
</div>
<?php  } else if($operation == 'handle') { ?>

<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
	<input type="hidden" name="c" value="shopping" />
	<input type="hidden" name="a" value="coupon" />
	<input type="hidden" name="do" value="manage" />
	<input type="hidden" name="m" value="ewei_shopping" />
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $id ? '修改' : '添加'?>优惠券</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券类型：</label>
				<div class="col-sm-5 col-xs-5">
					<input  class="form-control" value="<?php  echo $item['name'];?>" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券券号：</label>
				<div class="col-sm-3 col-xs-3">
					<input  class="form-control" value="<?php  echo $item['no'];?>" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券面额：</label>
				<div class="col-sm-2 col-xs-2">
					<input  class="form-control" value="<?php  echo $item['value'];?>" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">生成时间：</label>
				<div class="col-sm-2 col-xs-2">
					<input  class="form-control" value="<?php  echo date('Y-m-d H:i:s', $item['create_time'])?>" readonly="readonly">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券状态：</label>
				<div class="col-sm-9 col-xs-12">
					<?php  if(is_array($status)) { foreach($status as $statusKey => $statusStr) { ?>
					<label for="status" class="radio-inline"><input type="radio" name="status" value="<?php  echo $statusKey;?>" id="status" <?php  if(empty($item) || $item['status'] == $statusKey) { ?>checked="true"<?php  } ?> /> <?php  echo $statusStr;?></label>
					&nbsp;&nbsp;&nbsp;
					<?php  } } ?>
				</div>
			</div>
			<?php  if($item['got_time']) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">用户获取时间：</label>
				<div class="col-sm-2 col-xs-2">
					<input  class="form-control" value="<?php  echo date('Y-m-d H:i:s', $item['got_time'])?>" readonly="readonly">
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券有效期</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<?php  echo tpl_form_field_daterange('expire', array('begin'=>date('Y-m-d H:i:s', $item['expire_begin']),'end'=>date('Y-m-d H:i:s', $item['expire_end'])));?>
				</div>
			</div>
			<?php  if($item['threshold']) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单满额限制：</label>
				<div class="col-sm-4 col-xs-4">
					<input  class="form-control" value="<?php  echo $item['threshold'];?>" readonly="readonly">
					<span class="help-block"><strong style="color:red">提示: </strong>订单满足设定值才可以展示出来供客户选择抵、扣</span>
				</div>
			</div>
			<?php  } ?>
			<?php  if($item['goodsid']) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">指定商品限制：</label>
				<div class="col-sm-4 col-xs-4">
					<input class="form-control" value="<?php  echo $item['goodsid'];?>"readonly="readonly">
					<span class="help-block"><strong style="color:red">提示: </strong>订单商品与设定一样才可以展示出来供客户选择抵、扣</span>
				</div>
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">设置使者UID：</label>
				<div class="col-sm-2 col-xs-2">
					<input  class="form-control" name="uid" value="<?php  echo $item['uid'];?>">
					<span class="help-block"><strong style="color:red">提示: </strong>必须是整数</span>
    			</div>
			</div>
			<div class="form-group col-sm-6">
				<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
				<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
			</div>
		</div>
	</div>
</form>
<?php  } ?>

<!-- 批量添加ajax-dialog -->
<div id="modal-close" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:50px auto;">
	<div class="modal-dialog" id="batch-create-coupon">
		<div class="modal-content">
		<form id="batch-create-form">
			<div class="modal-header">
				<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
				<h4>批量生成优惠券</h4>
			</div>
			<div class="modal-body">
				<label class="control-label">类型类型：</label>
				<select name="typeid" class="form-control">
				<?php  if(is_array($list['type'])) { foreach($list['type'] as $typeone) { ?>
					<option value="<?php  echo $typeone['id'];?>"><?php  echo $typeone['name'];?></option>
				<?php  } } ?>
				</select>
				<label class="control-label">生成数量(取值范围1--300)：</label>
				<input  class="form-control" name="num" value="100">
				<label class="control-label">初始状态：</label>
				<select name="status" class="form-control">
					<option value="0">不激活</option>
					<option value="1">已激活</option>
					<option value="2">生效</option>
				</select>
				<label class="control-label">有效期开始：</label>
				<?php  echo tpl_form_field_date('starttime', date('Y-m-d H:i:s'), true);?>
				<label class="control-label">有效期结束：</label>
				<?php  echo tpl_form_field_date('endtime', date('Y-m-d H:i:s'), true);?>
				<div id="module-menus"></div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" name="close" value="yes" id="btn-batch-create-coupon" data-dismiss="modal" >确认生成</button>
				<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">取消操作</a>
			</div>
		</form>
		</div>
	</div>
</div>

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
	});

	//批量生成优惠券ajax请求
	$("#btn-batch-create-coupon").on('click',function () {
		var d=$("#batch-create-form").serialize();
		console.log(d);
		$.post("<?php  echo url('shopping/coupon/manage',array('op'=>'batch'))?>",d ,function(response){
			console.log(response);
			if (response.code=='200') {
				alert('生成成功！');
				//$("#modal-close").hide();
				$("#batch-create-form").reset();
			} else{
				alert('发生错误，稍后再试');
			}
		}, "json");
	});
})
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>