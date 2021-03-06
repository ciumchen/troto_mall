<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display' && $_GPC['status'] == '' && $_GPC['cancelgoods'] == '' && $_GPC['accomplish'] == '') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display'))?>">
			全部订单
		</a>
	</li>
	<?php  if(!$_W['user']['sid']) { ?>
	<li <?php  if($operation == 'display' && $_GPC['status'] == '0') { ?>class="active"<?php  } ?>>
	<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => 0))?>">待付款</a>
	</li>
	<?php  } ?>
	<li <?php  if($operation == 'display' && $_GPC['status'] == '1' && $_GPC['sendtype'] != 2) { ?> class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => 1))?>">待发货</a>
	</li>
	<li <?php  if($operation == 'display' && $_GPC['status']=='2') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => 2))?>">已发货</a>
	</li>
	<li <?php  if($operation == 'display' && $_GPC['status']=='3') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => 3))?>">已收货</a>
	</li>
	<li <?php  if($operation == 'display' && $_GPC['accomplish'] == '1') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'accomplish' => 1))?>">已完成</a>
	</li>
	<li <?php  if($operation == 'display' && $_GPC['status'] == '-1') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => -1))?>">已取消</a>
	</li>
	<li <?php  if($operation == 'display' && $_GPC['status'] == '-2') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'status' => -2))?>">已删除</a>
	</li>
	<li <?php  if($operation == 'display' && $_GPC['cancelgoods'] == '1') { ?>class="active"<?php  } ?>>
		<a href="<?php  echo $this->createWebUrl('order', array('op' => 'display', 'cancelgoods' => 1))?>">售后申请</a>
	</li>
	<?php  if(!$_W['user']['sid']){?>
	<li>
		<a href="<?php  echo $this->createWebUrl('aftermarket', array('op' => 'display'))?>">售后订单(供应商)</a>
	</li>
	<?php  }?>
	<?php  if($operation == 'detail') { ?>
	<li class="active">
		<a href="#">
			订单详情
		</a>
	</li>   
	<?php  } else if($operation == 'editor') { ?>
	<li class="active">
		<a href="#">
			修改订单
		</a>
	</li>
	<?php  } ?>
</ul>
<p class="text-danger">
	&nbsp;&nbsp;已取消：当订单生成成功后两天内「未支付」或者「取消」的订单！<br>
	&nbsp;&nbsp;已删除：当订单取消超过三天被系统删除的订单，用户端是看不到的！<br><br>
	<?php  if($_W['user']['power'] & ADMINISTRATOR) { ?>增加“供应商归属订单查询” 功能,供应商管理者访问默认搜索该供应商的订单！<br><br><?php  } ?>
	<a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('orderex',array('status'=>1))?>">
		下载我的EXCEL未发货订单</a>
	<?php  if($_W['user']['power'] & PRODUCT && !($_W['user']['power'] & ORDER) || $_W['user']['power'] & ADMINISTRATOR) { ?>
	<!-- 管理员权限操作 -->
	<a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('orderex',array('status'=>2))?>">
		下载EXCEL已发货订单
	</a>
	<?php  } ?>
</p>
	
<?php  if($operation == 'display') { ?>
<div class="main">
<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal order-horizontal" role="form" id="form1">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="ewei_shopping" />
			<input type="hidden" name="do" value="order" />
			<?php  if(isset($_GPC['status'])){?>
			<input type="hidden" name="status" value="<?php  echo $_GPC['status'];?>" />
			<?php  }?>
			<?php  if(isset($_GPC['cancelgoods'])){?>
			<input type="hidden" name="cancelgoods" value="<?php  echo $_GPC['cancelgoods'];?>" />
			<?php  }?>
			<?php  if(isset($_GPC['accomplish'])){?>
			<input type="hidden" name="accomplish" value="<?php  echo $_GPC['accomplish'];?>" />
			<?php  }?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户UID</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="uid" id="" type="text" value="<?php  echo $_GPC['uid'];?>" placeholder="可查询 用户UID">
				</div>
			</div> 
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">订单号</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="可查询 订单号">
				</div>
			</div> 
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">用户信息</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<input class="form-control" name="member" id="" type="text" value="<?php  echo $_GPC['member'];?>" placeholder="可查询 收货电话/收货人姓名 / 用户昵称">
				</div>
			</div> 
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">支付方式</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<select name="paytype" class="form-control">
						<option value="">不限</option>
						<?php  if(is_array($ret['param']['paytype'])) { foreach($ret['param']['paytype'] as $key => $type) { ?>
						<option value="<?php  echo $key;?>" <?php  if(isset($_GPC['paytype']) && $_GPC['paytype'] != '' && $_GPC['paytype'] == $key) { ?> selected="selected" <?php  } ?>>
							<?php  echo $type['name'];?>
						</option>
						<?php  } } ?>
					</select>
				</div>
			</div>
			<?php  if($supp) { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">供应商</label>
				<div class="col-sm-7 col-lg-9 col-xs-12">
					
					<select class="form-control" style="margin-right:15px;" name="sid" >
						<option value="0">请选择供应商</option>
						<?php  if(is_array($supp)) { foreach($supp as $row) { ?>
							<option value="<?php  echo $row['sid'];?>" <?php  if($row['sid'] == $_GPC['sid'] || $row['sid'] == $item['sid']) { ?> selected="selected"<?php  } ?>><?php  echo $row['linkman'];?> -- <?php  echo $row['company'];?></option>
						<?php  } } ?>
					</select>
				</div>
				
			</div>
			<?php  } ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">下单时间</label>
				<div class="col-sm-8 col-lg-9 col-xs-12 order-col-xs-5">
					<?php  echo tpl_form_field_daterange('time', array('starttime'=>date('Y-m-d', $ret['param']['starttime']),'endtime'=>date('Y-m-d', $ret['param']['endtime'])));?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">单页数量</label>
				<div class="col-sm-7 col-lg-9 col-xs-12 order-col-xs-5"">
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
			订单（当前搜索到 <label class="text text-danger"><?php  echo $ret['total'];?></label> 条数据）
		</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover">
				<thead class="navbar-inner">
					<tr>
						<th style="width:30px;text-align:center;">&nbsp;</th>
						<th style="width:60px;">编号</th>
						<th style="width:200px;">订单号</th>
						<th style="width:100px;text-align:center;">会员名</th>
						<th style="width:80px;">订单来源</th>
						<th style="width:80px;">支付方式</th>
						<th style="width:100px;">订单总价</th>
						<th style="width:100px;">实际付款</th>
						<th style="width:80px;">状态</th>
						<th style="width:150px;">所属货柜</th>
						<th style="width:150px;">所属轨道</th>
						<th style="width:150px;">下单时间</th>
						<th style="width:150px;">支付时间</th>
						<th style="width:180px;">操作</th>
					</tr>
				</thead>
				<tbody>
					<?php  if(is_array($ret['list'])) { foreach($ret['list'] as $item) { ?>
					<tr>
						<td><input type="checkbox"></td>
						<td><?php  echo $item['id'];?></td>
						<td class="order-sn" data-sn="<?php  echo $item['ordersn'];?>"><?php  echo $item['ordersn'];?></td>
						<td align=center>
							<a href="<?php  echo url('mc/member/post',array('uid'=>$item['uid']));?>">
								<?php echo $item['nickname'] ? $item['nickname'] : '[UID: '.$item['uid'].']'?> 
							</a>
						</td>
						<td>
							<?php  if($item['source']=='WX') { ?><span class="label label-info">微信商城</span>
							<?php  } else if($item['source']=='CC') { ?><span class="label label-success">礼品商城</span>
							<?php  } else if($item['source']=='WEB') { ?><span class="label label-primary">PC站</span>
							<?php  } else { ?><span class="label label-warning">未知来源</span>
							<?php  } ?>
						</td>
						<td><?php  if($item['transid']) { ?><span class="label label-warning">微信支付</span><?php  } ?></td>
						<td>￥<?php  echo number_format($item['price'], 2)?></td>
						<td>￥<?php  echo number_format($item['price'], 2)?></td>
						<td><span class="label label-<?php  echo $item['statuscss'];?>"><?php echo $item['deleted'] ? '已删除' : ShoppingOrder::$status[$item['status']];?></span></td>
						<td><?php  echo $item['name'];?></td>
<!--
 						<td>
							<?php  if($item['creditsettle']=='0' && $item['status']>0) { ?>
							<a href="<?php  echo $this->createWebUrl('fixOrderBalanceCredit', array('orderid' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作用于处理分成失败订单，不可恢复，确认处理？');return false;" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="补分成（待完成）"><i class="fa fa-bolt"></i></a>
							<?php  } else if($item['creditsettle']=='1') { ?>
							<span class="label label-info">成功</span>
							<?php  } else { ?>&nbsp;
							<?php  } ?>
						</td>
-->
						<td><?php  echo $item['pathwayid'];?></td>
						<!--
						<td><?php  if(!empty($item['goods'])) { ?><?php  echo $warehouseList[$item['goods'][0]['wid']];?><?php  } ?></td>
						-->
						<td><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></td>
						<td><?php echo $item['paymenttime'] ? date('Y-m-d H:i:s', $item['paymenttime']) : ''?></td>
						<td class="order-oper" style="text-align:left;">
							<ul class="order-goods-list">
								<?php  if(is_array($item['goods'])) { foreach($item['goods'] as $itemgoods) { ?>
								<li class="a-hover" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="<?php  echo $itemgoods['title'];?>"><?php  echo $itemgoods['title'];?></li>
								<?php  } } ?>
							</ul>
							<a href="javascript:;" class="btn btn-info btn-sm order-oper-btn">查看商品</a>
							<a href="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id']))?>" class="btn btn-info btn-sm">查看订单</a>
							<?php  if($_W['user']['power'] & PRODUCT && !($_W['user']['power'] & ORDER) || $_W['user']['power'] & ADMINISTRATOR) { ?>
								<?php  if($item['status'] == -1 || $item['status'] == -2) { ?>
									<a href="<?php  echo $this->createWebUrl('order', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="删除"><i class="fa fa-times"></i></a>
								<?php  } ?>
							<?php  } ?>
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
	var $goodsBtn = $('.order-oper-btn'),
		$goodsList = $('.order-goods-list');
	$goodsBtn.on('mouseover', function() {
		$(this).prev('ul').show();
	});
	$goodsBtn.on('mouseleave', function() {
		$(this).prev('ul').hide();
	});

function ajaxDown(orderid) {
	var form=$('<form id="form-down-customs-clearance">');
	form.attr("style","display:none");
	form.attr("target","");
	form.attr("method","post");
	form.attr("action","<?php  echo $this->createWebUrl('downCustomsClearance')?>");
	var input1=$("<input>");
	input1.attr("type","hidden");
	input1.attr("name","orderid");
	input1.attr("value", orderid);
	$("body").append(form); //将表单放置在web中
	form.append(input1);
	form.submit();
	$('<form id="form-down-customs-clearance">').remove();
	return false;
}
</script>

<?php  } else if($operation == 'detail') { ?>
<style type="text/css">
	.main .form-horizontal .form-group{margin-bottom:0;}
	.main .form-horizontal .modal .form-group{margin-bottom:15px;}
	#modal-confirmsend .control-label{margin-top:0;}
</style>
<script type="text/javascript" src="/resource/js/lib/jquery.PrintArea.js"></script>
<div class="main">
	<!-- 确认发货 -->
	<form action="" method="post" class="form-horizontal batch-form" enctype="multipart/form-data" onsubmit="return formcheck(this);">
		<div id="modal-confirmsend" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
						<h3>快递信息</h3>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label class="col-xs-10 col-sm-3 col-md-3 control-label">是否需要快递</label>
							<div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
								<label for="radio_1" class="radio-inline">
									<input type="radio" name="isexpress" id="radio_1" value="1" onclick="$('.expresspanel').show();" checked> 是
								</label>
								<label for="radio_2" class="radio-inline">
									<input type="radio" name="isexpress" id="radio_2" value="2" onclick="$('.expresspanel').hide();"> 否
								</label>
							</div>
						</div>
						<div class="form-group expresspanel">
							<label class="col-xs-10 col-sm-3 col-md-3 control-label">快递公司</label>
							<div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
								<select class="form-control" name="express" id="express">
									<?php  if(is_array($expresscom)) { foreach($expresscom as $cval) { ?>
										<option value="<?php  echo $cval['express'];?>" data-name="<?php  echo $cval['express_name'];?>"><?php  echo $cval['express_name'];?></option>
									<?php  } } ?>
								</select>
								<input type='hidden' name='expresscom' id='expresscom' />
							</div>
						</div>
						<div class="form-group expresspanel">
							<label class="col-xs-10 col-sm-3 col-md-3 control-label">快递单号</label>
							<div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
								<input type="text" name="expresssn" class="form-control" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-xs-10 col-sm-3 col-md-3 control-label">推送消息给客户</label>
							<div class="col-xs-12 col-sm-9 col-md-8 col-lg-8">
								<label for="radio_msg_1" class="radio-inline" id="radio_msg_1">
									<input type="radio" name="sendNoticeMsg" value="0" checked> 否
								</label>
								<label for="radio_msg_2" class="radio-inline">
									<input type="radio" name="sendNoticeMsg" id="radio_msg_2" value="1"> 是
								</label>
							</div>
						</div>
						<div id="module-menus"></div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary span2" name="confirmsend" value="yes">确认发货</button>
						<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
					</div>
				</div>
			</div>
		</div>
	<form>
<!--确认发货结束-->

	<div class="panel panel-default">
		<div class="panel-heading">温馨提示 </div>
		<div class="panel-body">
			<span class="text-warning">退款</span>：退余额，退分成！并且订单状态最终完成，不可操作状态！<br>
			<span class="text-warning">确认付款：</span>手动支付，并且扣除用户余额<br>
			<span class="text-warning">完成订单：</span>直接完成整个订单流程<br>
			<span class="text-warning">取消订单：</span>已支付情况下可以无偿取消订单，并且不退款，用于微信支付的订单</td>
		</div>
	</div>

	<?php $list = $item['pid'] == -1 ? $item['child'] : array($item);?>
	<?php  if(is_array($list)) { foreach($list as $_item) { ?>
	<?php 
		 if($_W['user']['sid'] && $_W['user']['sid'] != $_item['sid']){
		 	continue;
		 }
	 ?>
	<div class="panel panel-default">
		<!-- <div class="panel-heading">
			商品信息<span class="text-muted">2014年7月18号以前的订单商品的成交价为0正常，以后会记录购买时商品的价格，防止商品价格变动记录困难)</span>
		</div> -->
		<div class="panel-heading">
			<div class="row show-grid">
				<div class="col-md-3"><?php echo $item['parent_ordersn']!='' ? '子订单号：' . $item['ordersn'] . '-' .$_item['id'] : '订单号：' . $item['ordersn'];?></div>
				<div class="col-md-3">供应商：<?php  $supplier = suppliers_getDetailToManage($_item['sid']); echo $supplier['company'] ."(" .$supplier['linkman']. ":" .$supplier['tel'] . " " . $supplier['mobile'] . ")"; ?></div>
				<div class="col-md-3">合计：<b style="color: #FF0000;"><?php  echo $_item['price'];?></b></div>
				<div class="col-md-3">&nbsp;<?php  if($item['paymenttime']) { ?>支付时间：<?php  echo date('Y-m-d H:i:s', $item['paymenttime'])?><?php  } ?></div>

				<div class="col-md-3">收货地址：<?php  echo $item['province'];?> <?php  echo $item['city'];?> <?php  echo $item['area'];?> <?php  echo $item['address'];?></div>
				<div class="col-md-3">收货人：<?php  echo $item['realname'];?></div>
				<div class="col-md-3">联系电话：<?php  echo $item['mobile'];?></div>
				<div class="col-md-3">&nbsp;<?php  if($item['expresssn']) { ?>发货时间：<?php  echo date('Y-m-d H:i:s', $item['sendexpress'])?><?php  } ?></div>
			</div>
		</div>
		<div class="panel-body table-responsive">
			<table class="table table-hover">
				<thead class="navbar-inner">
				<tr>
					<th width="60">商品ID</th>
					<th width="200">商品标题</th>
					<th width="150">商品属性</th>
					<th>成交价</th>
					<th width="80">购买数量</th>
					<!--<th>状态</th>-->
					<th>订单状态</th>
					<th>快递公司</th>
					<th>快递单号</th>
					<th>发货时间</th>
					<!--<th>是否删除</th>-->
					<th width="220">操作</th>
				</tr>
				</thead>
				<?php  $i = 0;?>
				<?php  if(is_array($_item['goods'])) { foreach($_item['goods'] as $goods) { ?>
				<?php  $i++;?>
				<tr>
					<td><!--<input type="checkbox"  name="return-check" value="<?php  echo $goods['id'];?>" <?php  if(!empty($goods['express'] && $goods['expresssn'] && $goods['expresstime'])) { ?>disabled<?php  } ?>/>--><?php  echo $goods['id'];?></td>
					<td>
						<a href="<?php  echo $this->createWebUrl('goods', array('id' => $goods['id'], 'op' => 'post'))?>" class="a-hover" data-toggle="tooltip" data-placement="bottom" title="<?php  echo $goods['title'];?>[ <?php  echo $goods['optionname'];?> ]"><?php  echo $goods['title'];?> [ <?php  echo $goods['optionname'];?> ]</a>
					</td>
					<td>
						<?php  if($goods['status']==1) { ?>
						<span class="label label-success">上架</span>
						<?php  } else { ?>
						<span class="label label-warning">下架</span>
						<?php  } ?>
						<span class="label label-info"><?php echo ($goods['type'] == 1)?'实体商品':'虚拟商品';?></span></td>
					<td><b style="color: #FF0000;"><?php  echo $goods['price'];?></b></td>
					<td><?php  echo $goods['total'];?></td>
					<!--<td><?php echo ($goods['cancelgoods'] == 0) ? '正常': '<span class="label label-danger">'.OrderGoodState($item['cancelgoods'], $goods['state']).'</span>';?></td>-->
					<td><code><?php  echo ShoppingOrder::$status[$_item['status']] ?></code></code></td>
					<!--<td><?php echo ($goods['deleted'] == 0) ? '正常': '<span class="label label-danger">移出订单</span>';?></td>-->
					<td><?php  echo $_item['expresscom'];?></td>
					<td><a href="/logistics.html?sn=<?php  echo $_item['expresssn'];?>" target="_blank"><?php  echo $_item['expresssn'];?></a></td>
					<td><?php echo $_item['sendexpress'] ? date('Y-m-d H:i',$_item['sendexpress']) : '';?></td>
					<td orderid="<?php  echo $_item['id'];?>">
						<?php  if($i == 1):?>
							<?php  if($_item['status'] == 2) { ?>
								<button type="button" class="btn btn-danger cancel-btn" name="cancelsend" onclick="$('#orderid').val($(this).parents('td').attr('orderid'));$('#modal-cancelsend').modal();" value="yes">取消发货</button>
								&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-info" id="btnPrint">打印出库单</button>
							<?php  } ?>
							<?php  if($_item['status'] == 1) { ?>
								<?php  if($artstatus!=1) { ?>
									<button type="button" class="btn btn-primary it-ems" id="confirm-send" onclick="$('#orderid').val($(this).parents('td').attr('orderid'));" data-toggle="modal" data-target="#modal-confirmsend">确认发货</button>
									<button type="button" class="btn btn-danger cancel-btn" name="cancelsend" onclick="$('#orderid').val($(this).parents('td').attr('orderid'));$('#modal-customs').modal();" value="yes">报关</button>
								<?php  } else { ?>
									<button type="button" class="btn btn-danger cancel-btn" name="cancelsend" onclick="$('#orderid').val($(this).parents('td').attr('orderid'));$('#modal-customs').modal();" value="yes">报关</button>
								<?php  } ?>
							<?php  } ?>
							<?php  if((!empty($goods['express'] && $goods['expresssn'] && $goods['expresstime']))) { ?>
							<a href="javascript:;" role-path="<?php  echo $this->createWebUrl('order', array('op' => 'detail', 'id' => $item['id'],'goodsid' => $goods['id']))?>" class="btn btn-default btn-sm cancel-send">取消发货</a>
							<?php  } ?>
							<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
						<?php  endif;?>
					</td>
				</tr>
				<?php  } } ?>
			</table>
		</div>
	</div>
	<?php  } } ?>

	<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data" onsubmit="return formcheck(this)">
		<input type="hidden" name="dispatchid" value="<?php  echo $dispatch['id'];?>" />
		<?php  if(!$_W['user']['sid']):?>
			<div class="panel panel-default">
				<div class="panel-heading">订单信息</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">订单来源:</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static" style="font-size:large">
								<?php  if($item['source']=='WX') { ?><span class="label label-info">微信商城</span>
								<?php  } else if($item['source']=='CC') { ?><span class="label label-success">礼品商城</span>
								<?php  } else if($item['source']=='WEB') { ?><span class="label label-primary">PC站</span>
								<?php  } else { ?><span class="label label-warning">未知来源</span>
								<?php  } ?>
							</p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">父订单号:</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static"><?php  if($item['parent_ordersn']) { ?><?php  echo $item['parent_ordersn'];?><?php  } else { ?>无父单号<?php  } ?></p>
						</div>
					</div>
					<?php  if($item['transid']) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">支付流水号 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static"><?php  echo $item['transid'];?></p>
						</div>
					</div>
					<?php  } ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">金额详情 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static">总价<b style="color: #FF0000;"><?php  echo sprintf("%.2f",$item['price'])?></b> 元，实付<b style="color: #FF0000;"><?php  echo sprintf("%.2f",($item['price']-$item['coupon']))?></b> 元
							（商品: <?php  echo sprintf("%.2f",$item['goodsprice'])?> <?php  if($item['couponid']) { ?>/ 优惠券优惠: <?php  echo sprintf("%.2f",$item['coupon'])?> 元<?php  } ?> / 运费: <?php  echo sprintf("%.2f",$item['dispatch'])?> 元 )</p>
						</div>
					</div>
					<!--
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">配送方式 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static"><?php  if(empty($dispatch)) { ?>自提<?php  } else { ?><?php  echo $dispatch['dispatchname'];?><?php  } ?></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">付款方式 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static">
								<?php  if($item['paytype'] == 0) { ?><span class="label label-danger">未支付</span><?php  } ?>
								<?php  if($item['paytype'] == 1) { ?>余额支付<?php  } ?>
								<?php  if($item['paytype'] == 2) { ?>在线支付<?php  } ?>
								<?php  if($item['paytype'] == 3) { ?>货到付款<?php  } ?>
							</p>
						</div>
					</div>
					-->
					<?php  if($item['cancelgoods'] == 1) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">售后状态 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static">
								<span class="label label-danger"><?php  echo OrderType($item['status'], $item['cancelgoods'], $item['accomplish'])?></span>
							</p>
						</div>
					</div>
					<?php  } ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">下单日期 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static"><?php  echo date('Y-m-d H:i:s', $item['createtime'])?></p>
						</div>
					</div>
					<?php  if($item['status']) { ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">付款时间 :</label>
						<div class="col-sm-9 col-xs-12">
						<?php  if($item['paymenttime']) { ?>
							<p class="form-control-static"><?php  echo date('Y-m-d H:i:s', $item['paymenttime'])?></p>
						<?php  } else { ?>
							<p class="form-control-static" style="font-weight: bold;color: #f00">未付款异常订单，请联系技术</p>
						<?php  } ?>
						</div>
					</div>
					<?php  } ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">备注 :</label>
						<div class="col-sm-9 col-xs-12"><textarea style="height:60px;" class="form-control" name="remark" cols="70"><?php  echo $item['remark'];?></textarea></div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">个人信息</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">姓名 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static"><a href="<?php  echo url('mc/member/post',array('uid'=>$item['uid']))?>">
								<?php echo $item['nickname'] ? $item['nickname'] : '[用户ID: '.$item['uid'].']'?>
							</a>
						</p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">当前余额： :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static text-danger"><?php  echo $item['info']['credit2'];?> 元</p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">联系信息 :</label>
						<div class="col-sm-9 col-xs-12">
							<?php  if($item['info']['mobile']) { ?><p class="form-control-static">手机 <?php  echo $item['info']['mobile'];?></p><?php  } ?>
							<?php  if($item['info']['qq']) { ?><p class="form-control-static">QQ <?php  echo $item['info']['qq'];?></p><?php  } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">关注时间 :</label>
						<div class="col-sm-9 col-xs-12">
							<p class="form-control-static"><?php  echo date('Y-m-d H:i:s',$item['info']['createtime'])?></p>
						</div>
					</div>
				</div>
			</div>
		<?php  endif;?>


		<!-- 关闭原因 -->
		<div id="modal-close" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
						<h3>退货</h3>
					</div>
					<div class="modal-body">
						<label>退款金额</label>
						<input class="form-control" name="refundMoney" style="width:200px;" type="text"  placeholder="需要退款的金额">
						<label>退货原因</label>
						<textarea style="height:70px;" class="form-control" name="reson" autocomplete="off"></textarea>
						<div id="module-menus"></div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary" name="closeRefund" value="yes">确认退款</button>
						<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
					</div>
				</div>
			</div>
		</div>
	
		
		<!-- 取消发货 -->
		<div id="modal-cancelsend" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
						<h3>取消发货</h3>
					</div>
					<div class="modal-body">
						<label>取消发货原因</label>
						<textarea style="height:150px;" class="form-control" name="cancelresons" autocomplete="off"></textarea>
						<div id="module-menus"></div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary span2" name="cancelsend" value="yes">取消发货</button>
						<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
					</div>
				</div>
			</div>
		</div>

		<!-- 海关报关 -->
		<div id="modal-customs" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
						<h3>一般贸易商品报关</h3>
					</div>
					<div class="modal-body">
						<label>三单报关</label>
						<div style="margin-top: 2%;">
							<button type="button" class="btn btn-primary" id="customs_pay" data-url-name="<?php  echo $this->createWebUrl('submitpayorder');?>">支付单上传</button>
							<button type="button" class="btn btn-primary" id="customs_order" data-url-name="<?php  echo $this->createWebUrl('downcustomsclearance');?>">订单上传</button>
							<button type="button" class="btn btn-primary" id="customs_waybill" data-url-name="<?php  echo $this->createWebUrl('submiteorderdate');?>">运单上传</button>
						</div>
						<div id="module-menus" style="margin-top: 2%;">备注：三单上传顺序，订单、支付单、运单，运单必须最后上传。<br>
									<span style="color:red;margin-left:6%;">（如若失败需重新下单重新报关!）<span/>
						</div>
					</div>
					<div class="modal-footer">
						<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">取消报关</a>
					</div>
				</div>
			</div>
		</div>
		<script>
			$("#customs_pay").on('click',function(){
				url  = $(this).attr('data-url-name');
				orderid = $("#orderid").val();
				$.ajax({
					type: 'post',
					url: url,
					data: {orderid:orderid},
					dataType:'json',
					success: function(data) {
						if(data.rsg=='SUCCESS'){
							alert(data.content);
						}else{
							alert(data.content);
						}
					}
				});
			});
			$("#customs_order").on('click',function(){
				url  = $(this).attr('data-url-name');
				orderid = $("#orderid").val();
				$.ajax({
					type: 'post',
					url: url,
					data: {orderid:orderid},
					dataType:'json',
					success: function(data) {
						alert(data.content);
					}
				});
			});
			$("#customs_waybill").on('click',function(){
				url  = $(this).attr('data-url-name');
				orderid = $("#orderid").val();
				$.ajax({
					type: 'post',
					url: url,
					data: {orderid:orderid},
					dataType:'json',
					success: function(data) {
						if(data.status=='true'){
							alert("运单上传成功！");
						}else{
							alert(data.notes);
						}
					}
				});
			});
		</script>
		<!-- 退货 -->
		<div id="modal-cancelgoods" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
						<h3>取消订单</h3>
					</div>
					<div class="modal-body">
						<label>取消订单原因</label>
						<textarea style="height:150px;" class="form-control" name="cancelreson" autocomplete="off"></textarea>
						<div id="module-menus"></div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary span2" name="cancelgoods" value="yes">取消订单</button>
						<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">关闭</a>
					</div>
				</div>
			</div>
		</div>

		<!--完成订单-->
		<div id="modal-cancelgoods" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" style="width:600px;margin:0px auto;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
						<h3>完成订单</h3>
					</div>
					<div class="modal-body">
						<label>是否完成此订单数据！</label>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary span2" name="" value="yes">完成</button>
						<a href="#" class="btn btn-default" data-dismiss="modal" aria-hidden="true">取消</a>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" name="orderid" id="orderid" value="" />
	</form>


<div class="panel panel-default">
		<div class="panel-heading">操作日志</div>
		<div class="panel-body">
			<table class="table table-hover">
				<tr>
					<td colspan="12">
						<?php 
						$_order =$order;
						$i = 0;
						$_order->ext['process'] || $_order->ext['process'] = array();
						if($_order->paymenttime){
						array_unshift($_order->ext['process'], array(
						'status' => ShoppingOrder::STAUTS_SUBMIT,
						'user' => $item['user']['realname'],
						'action' => '支付订单',
						'time' => $_order->paymenttime,
						));
						}
						if($_order->createtime){
						array_unshift($_order->ext['process'], array(
						'status' => ShoppingOrder::STAUTS_SUBMIT,
						'user' => $item['user']['realname'],
						'action' => '订单生成',
						'time' => $_order->createtime,
						));
						}
						$count = count($_order->ext['process']);
						foreach($_order->ext['process'] as $process):?>
						<?php  $i++;?>
						<div class="form-group">
							<label class="col-xs-12 col-sm-3 col-md-2 control-label"><?php  echo date('Y-m-d H:i:s', $process['time']);?></label>
							<div class="col-sm-9 col-xs-12">
								<p class="form-control-static<?php  if($i == $count){echo ' text-danger';}?>"><?php  echo $process['action'];?>(<?php  echo $process['status'];?>) &nbsp; &nbsp; &nbsp; &nbsp;<b><?php  echo $process['user'];?></b>&nbsp; &nbsp; &nbsp; &nbsp;<?php  echo $process['remark'];?></p>
							</div>
						</div>
						<?php  endforeach;?>
					</td>
				</tr>
				<tr>
					<td colspan="12" class="text-right">
						<!--<button type="button" style="float:left" class="btn btn-primary batch-manage">进入批量管理</button>-->
<!---->
						<!--<button type="button" style="float:left;margin-left:12px;display:none" class="btn btn-primary batch-checkall">全选</button>-->
						<?php  if($item['status'] == -2 ) { ?>
						<span type="button" class="label label-danger">已退货</span>
						<?php  } else if($item['cancelgoods'] == 1 && $item['status'] >= 3) { ?>
						<!--<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-cancelgoods">确认退货</button>-->
						<?php  } else if($item['cancelgoods'] == 1 && $item['status'] == 1) { ?>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-cancelgoods">确认退款</button>
						<?php  } else if($item['cancelgoods'] == 0 && $item['status'] != -1) { ?>
						<button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-cancelgoods">取消订单</button>
						<?php  } ?>

						<?php  if($item['status'] == 0) { ?>
						<?php  if($_W['user']['power'] & PRODUCT && !($_W['user']['power'] & ORDER) || $_W['user']['power'] & ADMINISTRATOR) { ?>
						<button type="submit" class="btn btn-primary" onclick="return confirm('确认付款此订单吗？'); return false;"
								name="confrimpay" value="yes">确认付款</button>
						<button type="button" class="btn btn-primary btn-wx" name="wechatsend" value="yes">微信推送</button>
						<?php  } ?>
						<?php  } else if($item['status'] == 1) { ?>
						<?php  if(!empty($dispatch)) { ?>
						<!--<button type="button" class="btn btn-primary" id="confirm-send">确认发货</button>-->
						<?php  } else { ?>
						<!--<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-cancelsend">取消发货</button>-->
						<?php  } ?>
						<?php  } else if($item['status'] == 2) { ?>
						<?php  if(!empty($dispatch)) { ?>
						<!--<button type="button" class="btn btn-danger cancel-btn" name="cancelsend" onclick="$('#modal-cancelsend').modal();" value="yes">取消发货</button>-->
						<!--<button type="button" class="btn btn-primary it-ems" id="confirm-send" style="display:none">确认发货</button>-->
						<?php  } ?>
						<?php  } ?>
						<?php  if($item['status'] != 3 && $item['status'] != 4 && $item['status'] != -2 ) { ?>
						<?php  if($_W['user']['power'] & PRODUCT && !($_W['user']['power'] & ORDER) || $_W['user']['power'] & ADMINISTRATOR) { ?>
						<button type="submit" class="btn btn-success" onclick="return confirm('确认完成此订单吗？'); return false;" name="finish" value="yes">完成订单</button>

						<?php  if($item['status'] >= 0) { ?>
						<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal-close">退款</button>
						<?php  } else { ?>
						<button type="submit" class="btn btn-default" onclick="return confirm('确认开启此订单吗？'); return false;" name="open" value="yes">开启订单</button>
						<?php  } ?>
						<?php  } ?>
						<?php  } ?>
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
					</td>
				</tr>
			</table>
		</div>
	</div>

</div>

<div class="wx-info-box">
	<h3>请输入推送的用户ID</h3>
	<button aria-hidden="true" data-dismiss="modal" class="close wx-info-close" type="button">×</button>
	<form action="./index.php?c=site&a=entry&m=ewei_shopping&do=order&op=detail&id=<?php  echo $_GPC['id'];?>" method="post" class="form-horizontal order-horizontal" role="form" id="form1">
		<p>
			<label class="control-label" for="">用户ID：</label>
			<input type="text" class="form-control" name="userid" value="" />
		</p>
		<p><button type="submit" class="btn btn-primary" name="wechatsend" value="yes">推送</button></p>
	</form>
</div>

<div id="modal-cancelsend" class="modal hide fade" tabindex="-1" role="dialog" aria-hidden="true" style=" width:600px;">
	<div class="modal-header"><button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button><h3>取消发货</h3></div>
	<div class="modal-body">
		<table class="tb">
			<tr>
				<th><label for="">取消发货原因</label></th>
				<td>
					<textarea style="height:150px;" class="span5" name="cancelreson" cols="70" autocomplete="off"></textarea>
				</td>
			</tr>
		</table>
		<div id="module-menus"></div>
	</div>
	<div class="modal-footer"><button type="submit" class="btn btn-primary span2" name="cancelsend" value="yes">取消发货</button><a href="#" class="btn" data-dismiss="modal" aria-hidden="true">关闭</a></div>
</div>

<div id="printContent" class="printContent" style="display: none;">
<table border=1 cellspacing="0" cellpadding="5" width="820" class="table" style="table-layout:initial; border-collapse:inherit; border: 1px solid #ddd;">
    <tr>
    <td colspan="7">
    	<table cellpadding="0" width="100%">
        <tr>
          <th rowspan="4"><img src="data:image/jpeg;base64,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"></th>
        </tr>
        <tr>
          <th colspan=2><h2>深圳前海十点一刻电子商务有限公司出库清单</h2></th>
        </tr>
        <tr>
          <td>订单号：<?php  echo $item['ordersn'];?></td>
          <td>出库类型：10D15</td>
        </tr>

        <tr>
          <td>收货单位：<?php  echo $item['province'];?> <?php  echo $item['city'];?> <?php  echo $item['area'];?> <?php  echo $item['address'];?></td>
          <td>收货联系：<?php  echo $item['realname'];?>, <?php  echo $item['mobile'];?></td>
        </tr>
      </table>
    </td>
    </tr>
  <tbody>
    <tr>
      <td width="5%">编号</td>
      <td>商品名称</td>
      <td>规格</td>
      <td>产品代码</td>
      <td>数量</td>
      <td>单价(含税)</td>
      <td>金额</td>
    </tr>
    <?php  if(is_array($list)) { foreach($list as $_item) { ?>
		<?php  if(is_array($_item['goods'])) { foreach($_item['goods'] as $kk => $goods) { ?>
	    <tr>
	      <td><?php  echo $kk+1?></td>
	      <td><?php  echo $goods['title'];?></td>
	      <td></td>
	      <td><?php  echo $goods['productsn'];?></td>
	      <td><?php  echo $goods['total'];?></td>
	      <?php  $goodsTaxPrice = $goods['price']+($goods['price']*$goods['taxrate']/100); ?>
	      <td>￥<?php  echo number_format($goodsTaxPrice, 2)?></td>
	      <td>￥<?php  echo number_format($goodsTaxPrice*$goods['total'], 2)?></td>
	    </tr>
    	<?php  } } ?>
    <?php  } } ?>
  </tbody>
  <tfoot>
    <tr>
      <td>订单备注</td>
      <td colspan="6"><?php  echo $item['remark'];?></td>
    </tr>
    <tr>
      <td colspan="6">订单总额</td>
      <td>￥<?php  echo sprintf("%.2f",$item['price'])?></td>
    </tr>
    <tr>
      <td colspan="6">优惠总额</td>
      <td>￥<?php  echo $item['coupon'];?></td>
    </tr>
    <tr>
      <td colspan="6">支付总额</td>
      <td>￥<?php  echo sprintf("%.2f",($item['price']-$item['coupon']))?></td>
    </tr>
    <tr>
      <td>出库人</td>
      <td>Lc.</td>
      <td>审核人</td>
      <td>Alice</td>
      <td>日期</td>
      <td colspan="2"><?php  echo date('Y-m-d')?></td>
    </tr>
  </tfoot>
</table>
</div>
<script type="text/javascript">
$(function(){
	//打印出库单
	$("#btnPrint").click(function(){
		$("#printContent").css('display', 'block');
		$("div.printContent").printArea();
		$("#printContent").css('display', 'none');
	});

	var batchManage = $(".batch-manage")
		flag = false,
		confirmSend = $("#confirm-send"),
		input_check = $("input[name=return-check]"),
		checkAll = $(".batch-checkall");
	batchManage.on("click",function(){
		if(!flag){
			input_check.show();
			checkAll.show();
			if($(".cancel-btn").css("display") === 'inline-block'){
				console.log('111')
				$(".cancel-btn").css("display","none").after(function(){$(".it-ems").show()});
			}
			$(this).text("退出批量管理").removeClass("btn-primary").addClass("btn-danger");
			flag = true;
		} else {
			checkAll.hide();
			input_check.hide();
			if($(".cancel-btn").css("display") === 'none'){
				$(".it-ems").hide().prev().show();
			}
			$(this).text("进入批量管理").removeClass("btn-danger").addClass("btn-primary");
			flag = false;
		}
		
	});

	var btnHTml = function(status){
		var shtml;
		if(status){
			shtml = '<button type="button" class="btn btn-primary" id="confirm-send">确认发货</button>';
			return shtml;
		} else {
			shtml = '<button type="button" class="btn btn-danger" name="cancelsend" onclick="$(\'#modal-cancelsend\').modal();" value="yes">取消发货</button>';
			return shtml;
		}
	}

	confirmSend.on("click",function(){
		var arr = [];
//		if(input_check.is(":checked")){
		if(1){
			var ids = [];
			input_check.each(function(){
				var $$this = $(this);
				if($$this.get(0).checked === true){
					arr.push($$this.val());
				}
			})
			$(this).attr("data-toggle","modal").attr("data-target","#modal-confirmsend");
			for (var i=0,iLen=arr.length;i<iLen;i++){
				ids.push(arr[i]);
			}
			$(".modal-footer").append("<input type='hidden' name='goodsidIds' value='"+ids.join(',')+"'/>");
			return true;
		} else {
			alert("请选择商品！");
			return false;
		}
	});

	checkAll.click(function(){
		if ($(this).hasClass('btn-primary')) {
			input_check.each(function(){
				if(!$(this).attr("disabled")){
					$(this).prop("checked", true);
				}
			})
			$(this).text("反选").removeClass("btn-primary").addClass("btn-danger");	
		} else {
			input_check.prop("checked", false);
			$(this).text("全选").removeClass("btn-danger").addClass("btn-primary");
		}
	});

	$(".cancel-send").on("click",function(){
		var $this = $(this);
		var path = $(this).attr("role-path");
		if(confirm("真的要取消发货吗？")){
			$.get(path,function(data){
				if(data.status == 200 && data.msc === true){
					location.reload(location.href);
				} else {
					alert(data.msc);
					return false;
				}
			},'json');
		} else {
			return false;
		}
	});

	<?php  if(!empty($express)) { ?>
	$("#express").find("option[data-name='<?php  echo $express['express_name'];?>']").attr("selected",true);
	$("#expresscom").val($("#express").find("option:selected").attr("data-name"));
	<?php  } ?>
	$("#express").change(function(){
		var obj = $(this);  
		var sel = obj.find("option:selected").attr("data-name");
		$("#expresscom").val(sel);
	});
	$('.btn-wx').on('click', function(e) {
		e.stopPropagation();
		$('.wx-info-box').fadeIn(200);
	});
	$('.wx-info-close').on('click', function() {
		$('.wx-info-box').fadeOut(200);
	});
})
</script>
<?php  } ?>
<?php  if($operation == 'editor') { ?>
	<div class="main">
		<form action="" method="post" class="form-horizontal" enctype="multipart/form-data" >
			<div class="panel panel-default">
				<div class="panel-heading">
					修改订单
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品ID</label>
						<div class="col-sm-9 col-xs-12">
							<input class="form-control" name="goodsid" type="text" readonly="readonly" value="<?php  echo $_GPC['gid'];?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品标题</label>
						<div class="col-sm-1">
							<p class="form-control-static">洒大地</p>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
<?php  } ?>
 

<script>
require(['bootstrap'],function($){
	$('.btn').hover(function(){
		$(this).tooltip('show');
	},function(){
		$(this).tooltip('hide');
	});
	$('.a-hover').hover(function(){
		$(this).tooltip('show');
	},function(){
		$(this).tooltip('hide');
	});
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>