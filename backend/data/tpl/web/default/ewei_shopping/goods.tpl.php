<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common', TEMPLATE_INCLUDEPATH)) : (include template('common', TEMPLATE_INCLUDEPATH));?>
<script type="text/javascript" src="resource/js/lib/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="resource/js/lib/jquery-ui-1.10.3.min.js"></script>

<ul class="nav nav-tabs">
	<li <?php  if($operation == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods', array('op' => 'post'))?>">添加商品</a></li>
	<li <?php  if($operation == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods', array('op' => 'display'))?>">管理商品</a></li>
	<li <?php  if($operation == 'import') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods', array('op' => 'import'))?>">导入商品</a></li>
	<li <?php  if($operation == 'option') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods', array('op' => 'option'))?>">规格Execl管理</a></li>
</ul>
<?php  if($operation == 'post') { ?>
<link type="text/css" rel="stylesheet" href="./resource/css/uploadify_t.css" />
<style type='text/css'>
	.tab-pane {padding:20px 0 20px 0;}
</style>
<div class="main">
	<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data" id="form1">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php  if(empty($item['id'])) { ?>添加商品<?php  } else { ?>编辑商品<?php  } ?>
			</div>
			<div class="panel-body">
				<ul class="nav nav-tabs" id="myTab">
					<li class="active" ><a href="#tab_basic">基本信息</a></li>
					<li><a href="#tab_des">商品描述</a></li>
					<li><a href="#tab_param">自定义属性</a></li>
					<li><a href="#tab_option">商品规格</a></li>
					<li><a href="#tab_other">其他设置</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane  active" id="tab_basic"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_basic', TEMPLATE_INCLUDEPATH)) : (include template('goods_basic', TEMPLATE_INCLUDEPATH));?></div>
					<div class="tab-pane" id="tab_des"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_des', TEMPLATE_INCLUDEPATH)) : (include template('goods_des', TEMPLATE_INCLUDEPATH));?></div>
					<div class="tab-pane" id="tab_param"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_param', TEMPLATE_INCLUDEPATH)) : (include template('goods_param', TEMPLATE_INCLUDEPATH));?></div>
					<div class="tab-pane" id="tab_option"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_option', TEMPLATE_INCLUDEPATH)) : (include template('goods_option', TEMPLATE_INCLUDEPATH));?></div>
					<div class="tab-pane" id="tab_other"><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('goods_other', TEMPLATE_INCLUDEPATH)) : (include template('goods_other', TEMPLATE_INCLUDEPATH));?></div>
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<input type="submit" name="submit" value="提交" class="btn btn-primary col-lg-1" />
			<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
		</div>
	</form>
</div>

<script type="text/javascript">
	var category = <?php  echo json_encode($children)?>;

	$(function () {
		window.optionchanged = false;
		$('#myTab a').click(function (e) {
			e.preventDefault();//阻止a链接的跳转行为
			$(this).tab('show');//显示当前选中的链接及关联的content
		})
	});

	function formcheck(){
		if($("#pcate").val()=='0'){
			Tip.focus("pcate","请选择商品分类!","right");
			return false;
		}
		if($("#goodsname").isEmpty()) {
			$('#myTab a[href="#tab_basic"]').tab('show');
			Tip.focus("goodsname","请输入商品名称!","right");
			return false;
		}
		<?php  if(empty($id)) { ?>
		if ($.trim($(':file[name="thumb"]').val()) == '') {
			$('#myTab a[href="#tab_basic"]').tab('show');
			$('#myTab a[href="#tab_basic"]').tab('show');
			Tip.focus('thumb_div', '请上传缩略图.', 'right');
			return false;
		}
		<?php  } ?>
		if($("#goodsname").isEmpty()) {
			$('#myTab a[href="#tab_basic"]').tab('show');
			Tip.focus("goodsname","请输入商品名称!","right");
			return false;
		}
		var full = checkoption();
		if(!full){return false;}
		if(optionchanged){
			$('#myTab a[href="#tab_option"]').tab('show');
			message('规格数据有变动，请重新点击 [刷新规格项目表] 按钮!','','error');
			return false;
		}
		return true;
	}
	
	function checkoption(){
		
		var full = true;
		if( $("#hasoption").get(0).checked){
			$(".spec_title").each(function(i){
				if( $(this).isEmpty()) {
					$('#myTab a[href="#tab_option"]').tab('show');
					Tip.focus(".spec_title:eq(" + i + ")","请输入规格名称!","top");
					full =false;
					return false;
				}
			});
			$(".spec_item_title").each(function(i){
				if( $(this).isEmpty()) {
					$('#myTab a[href="#tab_option"]').tab('show');
					Tip.focus(".spec_item_title:eq(" + i + ")","请输入规格项名称!","top");
					full =false;
					return false;
				}
			});
		}
		if(!full) { return false; }
		return full;
	}

</script>

<?php  } else if($operation == 'display') { ?>
<p class="text-danger text-right">
	<?php  if($_W['user']['power'] & PRODUCT && !($_W['user']['power'] & ORDER) || $_W['user']['power'] & ADMINISTRATOR) { ?>
	<!-- 管理员权限操作 -->
	<a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('goodsex',array('status'=>1))?>">下载上架商品EXCEL</a>
	<a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('goodsex',array('status'=>0))?>">下载下架商品EXCEL</a>
	<?php  } ?>
</p>

<div class="main">
	<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="site" />
			<input type="hidden" name="a" value="entry" />
			<input type="hidden" name="m" value="ewei_shopping" />
			<input type="hidden" name="do" value="goods" />
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">商品关键字</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
				<div class="col-xs-12 col-sm-8 col-lg-9">
					<select name="status" class='form-control'>
							<option value="1" <?php  if(!empty($_GPC['status'])) { ?> selected<?php  } ?>>上架</option>
							<option value="0" <?php  if(empty($_GPC['status'])) { ?> selected<?php  } ?>>下架</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
				<div class="col-sm-9 col-xs-12" >
					<label for="isrecommand" class="checkbox-inline">
						<input type="checkbox" name="isrecommand" value="1" id="isrecommand" <?php  if($_GPC['isrecommand'] == 1) { ?>checked="true"<?php  } ?> /> 首页推荐
					</label>
					<label for="isnew" class="checkbox-inline">
						<input type="checkbox" name="isnew" value="1" id="isnew" <?php  if($_GPC['isnew'] == 1) { ?>checked="true"<?php  } ?> /> 新品推荐
					</label>
					<label for="ishot" class="checkbox-inline">
						<input type="checkbox" name="ishot" value="1" id="ishot" <?php  if($_GPC['ishot'] == 1) { ?>checked="true"<?php  } ?> /> 热卖推荐
					</label>
					<label for="isdiscount" class="checkbox-inline">
						<input type="checkbox" name="isdiscount" value="1" id="isdiscount" <?php  if($_GPC['isdiscount'] == 1) { ?>checked="true"<?php  } ?> /> 必买商品
					</label>
					<label for="isbrush" class="checkbox-inline">
						<input type="checkbox" name="isbrush" value="1" id="isbrush" <?php  if($_GPC['isbrush'] == 1) { ?>checked="true"<?php  } ?> /> 天天特价
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">分类</label>
				<div class="col-xs-6 col-sm-4">
					<select class="form-control" style="margin-right:15px;" name="cate_1" onchange="fetchChildCategory(this.options[this.selectedIndex].value)">
						<option value="0">请选择一级分类</option>
						<?php  if(is_array($category)) { foreach($category as $row) { ?>
						<?php  if($row['parentid'] == 0) { ?>
						<option value="<?php  echo $row['id'];?>" <?php  if($row['id'] == $_GPC['cate_1']) { ?> selected="selected"<?php  } ?>><?php  echo $row['name'];?></option>
						<?php  } ?>
						<?php  } } ?>
					</select>
				</div>
				<div class="col-xs-6 col-sm-4">
					<select class="form-control input-medium" id="cate_2" name="cate_2">
						<option value="0">请选择二级分类</option>
						<?php  if(!empty($_GPC['cate_1']) && !empty($children[$_GPC['cate_1']])) { ?>
						<?php  if(is_array($children[$_GPC['cate_1']])) { foreach($children[$_GPC['cate_1']] as $row) { ?>
						<option value="<?php  echo $row['0'];?>" <?php  if($row['0'] == $_GPC['cate_2']) { ?> selected="selected"<?php  } ?>><?php  echo $row['1'];?></option>
						<?php  } } ?>
						<?php  } ?>
					</select>
				</div>
				<div class=" col-xs-12 col-sm-2 col-lg-2">
					<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
				</div>
			</div>
			<div class="form-group">
			</div>
		</form>
	</div>
</div>
<style>
.label{cursor:pointer;}
</style>
<div class="panel panel-default">
	<div class="panel-heading">
		商品（当前搜索到 <label class="text text-danger"><?php  echo $res['total'];?></label> 条数据）
	</div>
	<div class="panel-body table-responsive">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:50px;">ID</th>
					<th style="width:650px;">商品标题</th>
					<th style="width:60px;">排序</th>
					<th style="width:250px;">商品属性(点击可修改)</th>
					<th style="width:150px;">型号编码</th>
					<th style="width:80px;">价格</th>
					<th style="width:80px;">库存</th>
					<th style="width:110px;">状态(点击修改)</th>
					<th style="text-align:center; width:120px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($res['list'])) { foreach($res['list'] as $item) { ?>
				<tr>
					<td><?php  echo $item['id'];?></td>
					<td style="min-width:380px;"><?php  if($item['isflash']==1) { ?><label class='label label-default label-info'>闪</label><?php  } ?><?php  if(!empty($category[$item['pcate']])) { ?><span class="text-error">[<?php  echo $category[$item['pcate']]['name'];?></span><?php  } ?><?php  if(!empty($children[$item['pcate']])) { ?><span class="text-info">&gt;&gt;<?php  echo $children[$item['pcate']][$item['ccate']]['1'];?>]</span><?php  } ?><?php  echo $item['title'];?></td>
					<td><?php  echo $item['displayorder'];?> </td>
					<td>
						<label data='<?php  echo $item['isnew'];?>' class='label label-default <?php  if($item['isnew']==1) { ?>label-info<?php  } else { ?><?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'new')">新品</label>
						<label data='<?php  echo $item['ishot'];?>' class='label label-default <?php  if($item['ishot']==1) { ?>label-info<?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'hot')">热卖</label>
						<label data='<?php  echo $item['isrecommand'];?>' class='label label-default <?php  if($item['isrecommand']==1) { ?>label-info<?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'recommand')">首页</label>
						<label data='<?php  echo $item['isdiscount'];?>' class='label label-default <?php  if($item['isdiscount']==1) { ?>label-info<?php  } ?>' onclick="
						setProperty(this,<?php  echo $item['id'];?>,'discount')">必买</label>
						<label data='<?php  echo $item['isbrush'];?>' class='label label-default <?php  if($item['isbrush']==1) { ?>label-info<?php  } ?>' onclick="
						setProperty(this,<?php  echo $item['id'];?>,'brush')">特价</label>
					</td>
					<td><?php  echo $item['goodssn'];?></td>
					<td><?php  echo $item['marketprice'];?></td>
					<td><?php  echo $item['total'];?></td>
					<td>
						<label data='<?php  echo $item['status'];?>' class='label  label-default <?php  if($item['status']==1) { ?>label-info<?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'status')"><?php  if($item['status']==1) { ?>上架<?php  } else { ?>下架<?php  } ?></label>
						<label data='<?php  echo $item['type'];?>' class='label  label-default <?php  if($item['type']==1) { ?>label-info<?php  } ?>' onclick="setProperty(this,<?php  echo $item['id'];?>,'type')"><?php  if($item['type']==1) { ?>实物<?php  } else { ?>虚物<?php  } ?></label>
					</td>
					<td style="text-align:right;">
						<a href="<?php  echo $this->createWebUrl('goods', array('id' => $item['id'], 'op' => 'post','page'=>$pindex))?>"class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="编辑"><i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
						<a href="<?php  echo $this->createWebUrl('goods', array('id' => $item['id'], 'op' => 'delete'))?>" onclick="return confirm('此操作不可恢复，确认删除？');return false;" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="删除"><i class="fa fa-times"></i></a>
					</td>
				</tr>
				<?php  } } ?>
			</tbody>
			<tr>
				<td></td>
				<td colspan="6">
					<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
					<input type="submit" class="btn btn-primary" name="submit" value="提交" />
				</td>
			</tr>
		</table>
		<?php  echo $res['pager'];?>
	</div>
	</div>
</div>
<script type="text/javascript">
	require(['bootstrap'],function($){
		$('.btn').hover(function(){
			$(this).tooltip('show');
		},function(){
			$(this).tooltip('hide');
		});
	});

	var category = <?php  echo json_encode($children)?>;
	function setProperty(obj,id,type){
		$(obj).html($(obj).html() + "...");
		$.post("<?php  echo $this->createWebUrl('setgoodsproperty')?>"
			,{id:id,type:type, data: obj.getAttribute("data")}
			,function(d){
				$(obj).html($(obj).html().replace("...",""));
				if(type=='type'){
				 $(obj).html( d.data=='1'?'实物':'虚物');
				}
				if(type=='status'){
				 $(obj).html( d.data=='1'?'上架':'下架');
				}
				$(obj).attr("data",d.data);
				if(d.result==1){
					$(obj).toggleClass("label-info");
				}
			}
			,"json"
		);
	}

</script>

<?php  } else if($operation == 'import') { ?>

<div class="main">
<form action="./index.php?c=site&a=entry&op=import&do=goods&m=ewei_shopping" method="post" class="form-horizontal" role="form">
	<div class="panel panel-info">
		<div class="panel-heading">批量导入商品</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">请输入文件位于<br />站点根目录位置</label>
				<div class="col-sm-6 col-xs-6">
					<input type="text" name="file" id="file" class="form-control" value="">
					<span class="help-block">如：/attachment/files/new_goods200.xlsx</span>
				</div>
			</div>
		</div>
		<div class="panel-body"><strong>注：</strong>导入之前，请将数据文件上传直根目录下某处。模板文件下载(不需要登录)：<a href="/attachment/files/GoodsImportTemplate.xlsx" target="_blank">GoodsImportTemplate.xlsx</a></div>
	</div>
<style>
.label{cursor:pointer;}
</style>
<div class="panel panel-default">
	<div class="panel-body table-responsive">
		<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
		<input type="submit" class="btn btn-primary" name="submit" value="立即导入" />
	</div>
	</div>
</form>
</div>
<?php  } else if($operation == 'option') { ?>
<div class="main">
	<p class="text-danger">
		“批量导入商品规格 EXCEL”的表格必须是使用点击最新的 --> “下载商品规格 EXCEL”导出的表格的基础上修改<br><br>
		<a class="btn btn-info btn-sm" href="<?php  echo $this->createWebUrl('goodsoption', array('token' => token()))?>">
		下载商品规格 EXCEL
		</a>
	</p>
<form action="./index.php" method="post" class="form-horizontal" role="form"  enctype="multipart/form-data">
	<input type="hidden" name="c" value="site" />
	<input type="hidden" name="a" value="entry" />
	<input type="hidden" name="m" value="ewei_shopping" />
	<input type="hidden" name="do" value="goodsoption" />
	<input type="hidden" name="op" value="import" />
	<div class="panel panel-info">
		<div class="panel-heading">批量导入商品规格 EXCEL</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">商品规格 Execl导入</label>
				<div class="col-sm-6 col-xs-6">
					<input type="file" name="goodsoption" class="form-control" >
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

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>
