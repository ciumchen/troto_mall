<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>

<link href="./resource/components/select2/select2.min.css" rel="stylesheet" />
<script src="./resource/components/select2/select2.min.js"></script>
<script src="./resource/components/select2/jquery-3.2.1.min.js"></script>

<ul class="nav nav-tabs">
	<li <?php  if($operation == 'display') { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('cabinet',array('op' =>'display'))?>">货柜列表</a></li>
	<!-- <li<?php  if($operation == 'post' && empty($cabinet['id'])) { ?> class="active" <?php  } ?>><a href="<?php  echo $this->createWebUrl('cabinet',array('op' =>'option'))?>">添加货柜</a></li> -->
	<?php  if($operation== 'post') { ?> <li class="active"><a href="<?php  echo $this->createWebUrl('cabinet',array('op' =>'post','id'=>$cabinet['id']))?>">编辑货柜</a></li> <?php  } ?>
</ul>

<?php  if($operation == 'display') { ?>
<div class="main panel panel-default">
	<div class="panel-heading">
		货柜（当前搜索到 <label class="text text-danger"><?php  echo $totle;?></label> 条数据）
	</div>
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
			<tr>
				<th style="width:100px;">序号</th>
				<th style="width:180px;">货柜名称</th>
				<th style="width:80px;">货柜状态</th>
				<th style="width:100px;">货柜库存</th>
				<th style="width:180px;">纬度, 经度</th>
				<th style="width:150px;">省份 / 城市 / 区县</th>
				<th style="width:120px;">创建时间</th>
				<th style="width:120px;">更新时间</th>
				<th style="width:90px;">操  作</th>
			</tr>
			</thead>
			<tbody>
			<?php  if(is_array($list)) { foreach($list as $item) { ?>
			<tr>
				<td><?php  echo $item['cabinetid'];?></td>
				<td><?php  echo $item['name'];?></td>
				<td>
					<?php  if ($item['status'] == 0){?><label class="label label-success">启用</label><?php  }?>
					<?php  if ($item['status'] == 1){?><label class="label label-default">未启用</label><?php  }?>
					<?php  if ($item['status'] == -1){?><label class="label label-danger">维护</label><?php  }?>
				</td>
				<td><?php  echo $item['stock'];?></td>
				<td><?php  echo $item['lat'];?>, <?php  echo $item['lng'];?></td>
				<td><?php  echo $item['addr_prov'];?>/<?php  echo $item['addr_city'];?>/<?php  echo $item['addr_dist'];?></td>
				<td><?php  echo date("y-m-d H:i",$item['createdt']) ?></td>
				<td><?php  echo date("y-m-d H:i",$item['updatedt']) ?></td>
				<td style="text-align:left;">
					<a href="<?php  echo $this->createWebUrl('cabinet', array('op' => 'post', 'id' => $item['cabinetid']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="修改"><i class="fa fa-pencil"></i></a>
					<a href="<?php  echo $this->createWebUrl('cabinet', array('op' => 'delete', 'id' => $item['cabinetid']))?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="bottom" title="删除" "><i class="fa fa-times"></i></a>
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
				货柜详细设置
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>货柜名称</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="name" class="form-control" value="<?php  echo $cabinet['name'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label"><span style="color:red">*</span>货柜状态</label>
					<div class="col-sm-3 col-xs-6">
						<select name="status" class='form-control'>
							<option value="1" <?php  if(!empty($cabinet['status'])) { ?> selected<?php  } ?>>启用</option>
							<option value="0" <?php  if(!empty($cabinet['status'])) { ?> selected<?php  } ?>>未启用</option>
							<option value="-1" <?php  if(!empty($cabinet['status'])) { ?> selected<?php  } ?>>维护</option>
						</select>
					</div>
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">货柜库存</label>
					<div class="col-sm-3 col-xs-6">
						<input type="text" name="stock" class="form-control" value="<?php  echo $cabinet['stock'];?>" readonly="readonly" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">描述</label>
					<div class="col-sm-9 col-xs-12">
						<input type="text" name="address" class="form-control" value="<?php  echo $cabinet['address'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">投放地址</label>
					<div class="col-sm-3 col-xs-4">
						<input type="text" class="form-control" value="<?php  echo $cabinet['addr_prov'];?> <?php  echo $cabinet['addr_city'];?> <?php  echo $cabinet['addr_dist'];?>" readonly="readonly">
					</div>
					<div class="col-sm-4 col-xs-6">
						<input type="text" name="addr_mark" class="form-control" value="<?php  echo $cabinet['addr_mark'];?>" />
					</div>
				</div>
				<div class="col-sm-12 col-xs-12">
					<span class="help-block" style="margin: 0 0 0 240px;">提示：投放地址的省市区选项不允许修改，只能修改详细地址。</span>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">轨道管理</label>
					<div class="col-sm-9 col-xs-12">
					<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('cabinet_pathway', TEMPLATE_INCLUDEPATH)) : (include template('cabinet_pathway', TEMPLATE_INCLUDEPATH));?>
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

<script>
	// 货柜数量的增加
	(function() {
		$(".input-num").unbind("dbclick");
		window.inputNumber = function(el) {
			var min = el.attr('min') || false;
			var max = el.attr('max') || false;
			var els = {};
			els.dec = el.prev();
			els.inc = el.next();
			el.each(function() {
				init($(this));
			});
			function init(el) {
				els.dec.on('click', decrement);
				els.inc.on('click', increment);
				function decrement() {
					var value = el[0].value;
					value--;
					if(!min || value >= min) {
						el[0].value = value;
					}
				}
				function increment() {
					var value = el[0].value;
					value++;
					if(!max || value <= max) {
						el[0].value = value++;
					}
				}
			}
		}
	})();
	inputNumber($('.input-number'));
</script>

<script>
	// 产品数量的增加
	(function() {
		$(".input-goodsnum").unbind("dbclick");
		window.inputGoods = function(el) {
			var min = el.attr('min') || false;
			var max = el.attr('max') || false;
			var els = {};
			els.dec = el.prev();
			els.inc = el.next();
			el.each(function() {
				init($(this));
			});
			function init(el) {
				els.dec.on('click', decrement);
				els.inc.on('click', increment);
				function decrement() {
					var value = el[0].value;
					value--;
					if(!min || value >= min) {
						el[0].value = value;
					}
				}
				function increment() {
					var value = el[0].value;
					value++;
					if(!max || value <= max) {
						el[0].value = value++;
					}
				}
			}
		}
	})();
	inputGoods($('.input-goods'));
</script>

<script>
$(document).ready(function(){
  $.getJSON('./index.php?c=site&a=entry&op=post&parentid=0&do=AjaxCityData&m=ewei_shopping',function(res){
    $.each(res.data, function(i, area){
		$("#provItems").append('<option value="'+area['regionid']+'">'+area['name']+'</option>');
    });
  });

  $("#provItems").change(function(){
  		$("#cityItems").html('')
	  $.getJSON('./index.php?c=site&a=entry&op=post&parentid='+$("#provItems").val()+'&do=AjaxCityData&m=ewei_shopping',function(res){
	    $.each(res.data, function(i, area){
			$("#cityItems").append('<option value="'+area['regionid']+'">'+area['name']+'</option>');
	    });
	  });

  });

  $("#cityItems").change(function(){
  		$("#distItems").html('')
	    $.getJSON('./index.php?c=site&a=entry&op=post&parentid='+$("#cityItems").val()+'&do=AjaxCityData&m=ewei_shopping',function(res){
	    $.each(res.data, function(i, area){
			$("#distItems").append('<option value="'+area['regionid']+'">'+area['name']+'</option>');
	    });
	  });

  });

});
</script>

<script>
	$(document).ready(function(){
		$.getJSON('./index.php?c=site&a=entry&op=post&pcate=0&do=AjaxGoodsData&m=ewei_shopping',function(res){
			$.each(res.data, function(i, area){
				$("#pcateItems").append('<option value="'+area['pcate']+'">'+area['title']+'</option>');
			});
		});

		$("#pcateItems").change(function(){
			$("#goodsid").html('')
			$.getJSON('./index.php?c=site&a=entry&op=post&pcate='+$("#pcate").val()+'&do=AjaxGoodsData&m=ewei_shopping',function(res){
				$.each(res.data, function(i, area){
					$("#goodsid").append('<option value="'+area['id']+'">'+area['title']+'</option>');
				});
			});
		});
	});
</script>

<!--文件上传-->
<?php  } else if($operation == 'option') { ?>
<div class="main">
	<p class="text-danger">
		“批量导入货柜 EXCEL”的表格必须是使用点击最新的 --> “下载货柜 EXCEL”导出的表格的基础上修改<br><br>
		<a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('cabinet', array('token' => token()))?>">
			下载货柜 EXCEL
		</a>
	</p>
	<form action="./index.php" method="post" class="form-horizontal" role="form"  enctype="multipart/form-data">
		<input type="hidden" name="c" value="site" />
		<input type="hidden" name="a" value="entry" />
		<input type="hidden" name="m" value="ewei_shopping" />
		<input type="hidden" name="do" value="cabinetoption" />
		<input type="hidden" name="op" value="option" />
		<div class="panel panel-info">
			<div class="panel-heading">批量导入货柜 EXCEL</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 control-label">货柜 Execl导入</label>
					<div class="col-sm-6 col-xs-6">
						<input type="file" name="cabinet" class="form-control" >
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

<script>
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});
</script>

<script language='javascript'>
	function formcheck(){
		console.log(serialize(this));
		return false;
		if($("#cabinet_com").isEmpty()){
			Tip.focus("cabinet_com","请选择货柜状态!");
			return false;
		}
		return true;
	}

	$(function(){
		$("#common_corp").change(function(){
			var obj = $(this);
			var sel =obj.find("option:selected");
			$("#cabinet_com").val(sel.attr("status"));
		});
		<?php  if(!empty($cabinet['cabinetid'])) { ?>
		$("#common_corp").val(  "<?php  echo $cabinet['cabinet_url'];?>");
		<?php  } ?>

		})
</script>

<!-- 选择品牌 -->
<script>
$('#select2brand').select2();
</script>

<?php  } ?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>