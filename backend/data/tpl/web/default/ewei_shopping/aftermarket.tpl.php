<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<?php  if($operation == 'display') { ?>
<div class="main">
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal order-horizontal" role="form" id="form1">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="ewei_shopping" />
			<input type="hidden" name="do" value="aftermarket" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户UID</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="uid" id="" type="text" value="<?php  echo $_GPC['uid'];?>" placeholder="可查询用户UID">
				</div>
			</div> 
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">订单号</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="可查询订单号">
				</div>
			</div> 
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户信息</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="member" id="" type="text" value="<?php  echo $_GPC['member'];?>" placeholder="可查询收货联系方式 / 姓名">
				</div>
			</div> 
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户昵称</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="nickname" id="" type="text" value="<?php  echo $_GPC['nickname'];?>" placeholder="可查询用户昵称 / 真实姓名">
				</div>
			</div> 
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">支付方式</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<select name="paytype" class="form-control">
						<option value="">不限</option>
						<?php  if(is_array($ret['param']['paytype'])) { foreach($ret['param']['paytype'] as $key => $type) { ?>
						<option value="<?php  echo $key;?>" <?php  if($_GPC['paytype'] && $_GPC['paytype'] == $key) { ?> selected="selected" <?php  } ?>>
							<?php  echo $type['name'];?>
						</option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">下单时间</label>
					<div class="col-sm-7 col-lg-9 col-xs-12">
						<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $ret['param']['starttime']),'endtime'=>date('Y-m-d', $ret['param']['endtime'])));?>
					</div>
					<div class="col-sm-3 col-lg-2">
						<button class="btn btn-default">
							<i class="fa fa-search"></i> 搜索
						</button>
					</div>
				</div>
			<div class="form-group">
			</div>
		</form>
	</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-body table-responsive">
			<table class="table table-hover">
				<thead class="navbar-inner">
					<tr>
						<th width='80'>会员ID</th>
						<th width='100'>订单号</th>
						<th>售后商品</th>
						<th>购买的基本信息</th>
						<th width='80'>售后类型</th>
						<th width='100'>售后状态</th>
						<th>申请时间</th>
						<th>备注</th>
						<th>物流单号</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($ret['list'])) { foreach($ret['list'] as $item) { ?>
					<tr>
						<td>
							<a href="<?php  echo url('mc/member/post',array('uid'=>$item['uid']));?>">
								<?php  echo $item['uid'];?>
							</a>
						</td>
						<td><?php  echo $item['ordersn'];?></td>
						<td><?php  echo $item['title'];?></td>
						<td><?php  echo $item['price'];?> 元x<?php  echo $item['total'];?><?php  echo $item['unit'];?></td>
						<td><span class="label label-danger"><?php  echo AftermarketStatus($item['status'], $item['type'], true)?></span></td>
						<td><span class="label label-<?php  if($item['status'] == 3) { ?>success<?php  } else if($item['status'] == -1) { ?>default<?php  } else { ?>danger<?php  } ?>"><?php  echo AftermarketStatus($item['status'], $item['type'])?></span></td>
						<td title="<?php  echo date('Y-m-d H:i:s', $item['createtime'])?>"><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
						<td><?php  echo $item['desc'];?></td>
						<td><?php  echo $item['expresssn'];?></td>
						<td>
							<a href="<?php  echo $this->createWebUrl('aftermarket',array('op'=>'detail','id'=>$item['oaid']))?>" class="btn btn-success btn-sm">查看详情</a>
						</td>
					</tr>
					<?php  } } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php  echo $ret['pager'];?>
<script type="text/javascript">
	require(['daterangepicker'], function($){
		$('.daterange').on('apply.daterangepicker', function(ev, picker) {
			$('#form1')[0].submit();
		});
	});
</script>
<?php  } else if($operation == 'detail') { ?>
<style type="text/css">
	.main .form-horizontal .form-group{margin-bottom:5px;}
	.main .form-horizontal .modal .form-group{margin-bottom:15px;}
</style>
<div class="main">
	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data" onsubmit="return formcheck(this)">
		<div class="panel panel-default">
			<div class="panel-heading">
				售后 信息
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">申请日期 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></p>
					</div>
				</div>
				<?php  if($item['expresssn']) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">回寄物流 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static">
							<?php echo $item['express'] ? '物流：'.$item['express']:'';?><br>
							<?php echo $item['expresssn'] ? '物流单号：'.$item['expresssn']:'';?><br>
							<?php echo $item['expresstime'] ? '提交时间：'.date('Y-m-d H:i:s', $item['expresstime']):'';?>
						</p>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">售后类型 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static">
							<span class="label label-danger"><?php  echo AftermarketStatus($item['status'], $item['type'], true)?></span>
						</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">售后状态 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static">
						<?php  if($item['accomplish'] == 0) { ?>
							<?php  if($item['status'] == 3) { ?>
								<span class="label label-success">
							<?php  } else if($item['status'] == 2 ) { ?>
								<span class="label label-info">
							<?php  } else if($item['status'] == 1 ) { ?>
								<span class="label label-warning">
							<?php  } else { ?>
								<span class="label label-danger">
							<?php  } ?>
								<?php  echo AftermarketStatus($item['status'], $item['type'])?>
								</span>
						<?php  } else { ?>
							<span class="label label-success">售后完成</span>
						<?php  } ?>
						</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">申请说明 :</label>
					<div class="col-sm-9 col-xs-12"><textarea style="height:150px;" class="form-control" name="remark" cols="70"><?php  echo $item['desc'];?></textarea></div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">上传凭证：</label>
					<div class="col-sm-9 col-xs-12">
						<?php  echo tpl_form_field_multi_image('thumbs',$piclist,$options)?>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				订单 信息
			</div>
			<div class="panel-body">
				<?php  if($item['transid']) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">交易号 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><?php  echo $item['transid'];?></p>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单消费 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><?php  echo $item['price'];?> 元 （商品: <?php  echo $item['goodsprice'];?> 元 运费: <?php  echo $item['dispatchprice'];?> 元)</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">付款方式 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static">
							<?php  if($item['paytype'] == 1) { ?>余额支付<?php  } ?>
							<?php  if($item['paytype'] == 2) { ?>在线支付<?php  } ?>
							<?php  if($item['paytype'] == 3) { ?>货到付款<?php  } ?>
						</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单状态 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static">
						<?php  if($item['orderstatus'] == 1 || $item['orderstatus'] == 2) { ?>
							<span class="label label-info">
						<?php  } else if($item['ordercancelgoods'] == 1 ) { ?>
							<span class="label label-danger">
						<?php  } else { ?>
							<span class="label label-success">
						<?php  } ?>
							<?php  echo OrderType($item['orderstatus'], $item['ordercancelgoods'], $item['orderaccomplish'])?>
							</span>
						</p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">下单日期 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><?php  echo date('Y-m-d H:i:s', $item['ordercreatetime'])?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">备注 :</label>
					<div class="col-sm-9 col-xs-12"><textarea style="height:150px;" class="form-control" name="remark" cols="70"><?php  echo $item['remark'];?></textarea></div>
				</div>
			</div>
		</div>
		
		<div class="panel panel-default">
			<div class="panel-heading">
				换货 地址
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">姓名 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><?php  echo $item['user']['realname'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">手机 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><?php  echo $item['user']['mobile'];?></p>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">地址 :</label>
					<div class="col-sm-9 col-xs-12">
						<p class="form-control-static"><?php  echo $item['user']['province'];?><?php  echo $item['user']['city'];?><?php  echo $item['user']['area'];?><?php  echo $item['user']['address'];?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="panel panel-default">
			<!-- <div class="panel-heading">
				商品信息<span class="text-muted">2014年7月18号以前的订单商品的成交价为0正常，以后会记录购买时商品的价格，防止商品价格变动记录困难)</span>
			</div> -->
			<div class="panel-body table-responsive">
				<table class="table table-hover">
					<thead class="navbar-inner">
					<tr>
						<th>商品标题</th>
						<th>规格</th>
						<th style='color:red'>成交价</th>
						<th>购买数量</th>
						<th>售后数量</th>
						<th>售后</th>
						<th>当前状态</th>
						<th>操作</th>
					</tr>
					</thead>
					<tr>
						<td>
							<a href="<?php  echo $this->createWebUrl('goods', array('id' => $item['goodsid'], 'op' => 'post'))?>" class="btn" data-toggle="tooltip" data-placement="bottom" title="商品详细"><?php  echo $item['title'];?></a>
						</td>
						<td>
							<?php  echo $item['optionname'];?>
						</td>
						<td style='color:red'><?php  echo $item['optionprice'];?></td>
						<td><?php  echo $item['total'];?></td>
						<td><?php  echo $item['num'];?></td>
						<td><span class="label label-danger"><?php  echo AftermarketStatus($item['status'], $item['type'], true)?></span></td>
						<td>
							<?php  if($item['accomplish'] == 0) { ?>
								<?php  if($item['status'] == 3) { ?>
									<span class="label label-success">
								<?php  } else if($item['status'] == 2 ) { ?>
									<span class="label label-info">
								<?php  } else if($item['status'] == 1 ) { ?>
									<span class="label label-warning">
								<?php  } else { ?>
									<span class="label label-danger">
								<?php  } ?>
									<?php  echo AftermarketStatus($item['status'], $item['type'])?>
									</span>
							<?php  } else { ?>
								<span class="label label-success">售后完成</span>
							<?php  } ?>
						</td>
						<td>
							<?php  if($item['status'] == 0) { ?>
							<a class="btn btn-success" href="<?php  echo url('ma/suppliers/address', array('oaid'=>$item['oaid'],'ogid'=>$item['ogid']))?>">审核通过</a>
							<?php  } else if($item['status'] == 2) { ?>
							<a class="btn btn-info" href="<?php  echo $this->createWebUrl('aftermarket',array('oaid'=>$item['oaid'],'ogid'=>$item['ogid'],'orderid'=>$item['orderid'],'op'=>'accomplish'))?>">售后完成</a>
							<?php  } ?>

						</td>
					</tr>
					<tr>
						<td colspan="8" class="text-right">
							
							<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
</div>

<!-- 图片弹出框 begin -->
<div class="show-img-box"></div>
<div class="show-img-main">
	<div class="close">×</div>
	<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
	<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
	<img src="" />
</div>
<!-- 图片弹出框 end -->

<script language='javascript'>
$(function(){
	var $box = $('.show-img-box'),
		$imgMain = $('.show-img-main'),
		$imgList = $('.multi-img-details img'),
		$left = $('.glyphicon-chevron-left'),
		$right = $('.glyphicon-chevron-right'),
		$imgI;
	$(document).on('click', '.multi-img-details img', function() {
		var $this = $(this),
			src = $this.attr('src');
		$imgI = $this.parent('.multi-item');
		$imgMain.show().find('img').attr('src', src);
		$box.show();
	});
	$left.on('click', function() {
		if ($imgI.prev('div').hasClass('multi-item')) {
			var src = $imgI.prev('div').find('img').attr('src');
			$imgMain.find('img').attr('src', src);
			$imgI = $imgI.prev('div');
		}
	});
	$right.on('click', function() {
		if ($imgI.next('div').hasClass('multi-item')) {
			var src = $imgI.next('div').find('img').attr('src');
			$imgMain.find('img').attr('src', src);
			$imgI = $imgI.next('div');
		}
	});
	$imgMain.find('.close').on('click', function() {
		imgBoxHide();
	});
	$box.on('click', function() {
		imgBoxHide();
	});
	function imgBoxHide() {
		$imgMain.hide();
		$box.hide();
	}

})
</script>
<?php  } ?>
<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>