<?php defined('IN_IA') or exit('Access Denied');?><?php  $newUI = true;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ol class="breadcrumb" style="padding:5px 0;">
	<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; 微商城</a></li>
	<li>商城功能</li>
	<li>销售专题管理</li>
</ol>

<ul class="nav nav-tabs">
	<li <?php  if($do == 'display' && $operation == 'display') { ?> class="active" <?php  } ?>>
		<a href="<?php  echo url('shopping/topic',array( 'm'=>'ewei_shopping'))?>">销售专题管理</a>
	</li>
	<li <?php  if($do == 'add') { ?> class="active"<?php  } ?>>
		<a href="<?php  echo url('shopping/topic/add', array('op'=>'handle', 'm'=>'ewei_shopping'))?>"><?php  if($topicid) { ?>修改<?php  } else { ?>添加<?php  } ?>专题</a>
	</li>
	<?php  if($do == 'detail' && $operation == 'display') { ?><li  class="active"><a href="##">销售专题详情</a></li>
	<?php  } ?>
	<li <?php  if($do == 'address' && $operation == 'handle') { ?> class="active" <?php  } ?>>
		<a href="<?php  echo url('shopping/topic/address', array('op'=>'handle', 'sid' => $_GPC['sid']))?>">
			<?php  echo $ptr_title;?>
		</a>
	</li>
</ul>
<?php  if($do=='display' && $operation=='display') { ?>
<p class="text-danger">
	状态：首页展示使用逆序；&nbsp;&nbsp;&nbsp;只有在展示日期内、且状态为启用的专题才会被显示
</p>
<div class="panel panel-default">
	<div class="panel-body table-responsive" >
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:80px;">专题ID</th>
					<th style="width:450px;">专题名称</th>
					<th style="width:320px;">展示日期(开始时间--结束时间)</th>
					<th>显示顺序</th>
					<th>直接跳转</th>
					<th>焦点轮播展示</th>
					<th>状态</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr class="list" data-sid='<?php  echo $item["topicid"];?>' data-state='<?php  echo $item["status"];?>'>
					<td><?php  echo $item['topicid'];?></td>
					<td><?php  echo $item['title'];?></td>
					<td><?php  echo date('Y-m-d H:i:s', $item['starttime'])?> -- <?php  echo date('Y-m-d H:i:s', $item['endtime'])?></td>
					<td><?php  echo $item['displayorder'];?></td>
					<td><?php echo $item['isjump']?'是':'否'?></td>
					<td>
						<?php  if($item['isfocus'] == '1') { ?>
						<span class="btn btn-success btn-sm">是</span>
						<?php  } else if($item['isfocus'] == '0') { ?>
						<span class="btn btn-danger btn-sm">否</span>
						<?php  } ?>
					</td>
					<td>
						<?php  if($item['enabled'] == '1') { ?>
						<span class="btn btn-success btn-sm">启用</span>
						<?php  } else if($item['enabled'] == '0') { ?>
						<span class="btn btn-danger btn-sm">禁用</span>
						<?php  } ?>
					</td>
					<td>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/topic',array('topicid'=>$item['topicid'],'do'=>'add','op'=>'handle', 'm'=>'ewei_shopping'))?>">编辑</a>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/topic',array('topicid'=>$item['topicid'],'do'=>'detail', 'm'=>'ewei_shopping'))?>">详情</a>
					</td>
				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $pager;?>
<?php  } else if($do=='detail' && $operation=='display') { ?>
<div class="panel panel-default">
	<div class="panel-heading"><?php  echo $op_type;?>销售专题详情</div>
	<div class="panel-body">
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">专题ID：</label>
			<div class="col-sm-2" >
				<span style="color:red"><?php  echo $detailData['topicid'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">专题链接：</label>
			<div class="col-sm-5" >
				<span style="color:red">http://mall.troto.com.cn/goods/topic?id=<?php  echo $detailData['topicid'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">专题名称：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['title'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">创建时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['createtime'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">启用状态：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php echo $detailData['enabled']?'启用':'禁用'?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">焦点轮播：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php echo $detailData['isfocus']?'是':'否'?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">开始时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['starttime'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">结束时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['endtime'])?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">显示顺序：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['displayorder'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:49%">
			<label class="col-xs-12 col-md-2 control-label">跳转链接：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  if($detailData['isjump']) { ?><?php  echo $detailData['link'];?><?php  } else { ?>否<?php  } ?></span>
			</div>
		</div>
	</div>
	<div style="width:100%;border-bottom:1px dashed #ddd"></div>
	<div class="panel-body"><?php  echo html_entity_decode($detailData['content']) ?></div>
</div>
<?php  } else if($operation == 'handle') { ?>
<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading"><?php  echo $op_type;?> 销售专题信息</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">专题名称：</label>
				<div class="col-sm-9 col-xs-12">
					<input  class="form-control" name="title" value="<?php  echo $item['title'];?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">专题简介：</label>
				<div class="col-sm-9 col-xs-12">
    				<textarea class="form-control" id="description" name="description" rows="2"><?php  echo $item['description'];?></textarea>
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
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">焦点轮播展示</label>
				<div class="col-sm-9 col-xs-12">
					<label for="isfocus" class="radio-inline"><input type="radio" name="isfocus" value="1" id="enabledenabled"  <?php  if(!empty($item) && $item['isfocus'] == 1) { ?>checked="true"<?php  } ?> /> 是</label>
					&nbsp;&nbsp;&nbsp;
					<label for="isfocus" class="radio-inline"><input type="radio" name="isfocus" value="0" id="isfocus" <?php  if(empty($item) || $item['isfocus'] == 0) { ?>checked="true"<?php  } ?> /> 否</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">展示顺序：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="displayorder" value="<?php  echo $item['displayorder'];?>" style="width:65px">
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">专题图片：</label>
				<div class="col-sm-9 col-xs-12">
					<?php  echo tpl_form_field_image('thumb', $item['thumb'], '', array('extras' => array('text' => 'readonly')))?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">有效时间</label>
				<div class="col-sm-4 col-xs-6"><?php echo tpl_form_field_date('starttime', !empty($item['starttime']) ? date('Y-m-d H:i',$item['starttime']) : date('Y-m-d H:i'), 1)?></div>
				<div class="col-sm-4 col-xs-6"><?php echo tpl_form_field_date('endtime', !empty($item['endtime']) ? date('Y-m-d H:i',$item['endtime']) : date('Y-m-d H:i', strtotime('+5 days')), 1)?></div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">跳转链接：</label>
				<div class="col-sm-9 col-xs-12">
					<input class="form-control" name="link" value="<?php  echo $item['link'];?>">
					<span class="help-block"><strong style="color:red">特别注意: </strong>如果填写的是频道页面URL，详情内容可为空；否则，请置空并填写详情内容</span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">专题详情</label>
				<div class="col-sm-9 col-xs-12">
					<textarea name="content" class="form-control richtext" cols="30" rows="30"><?php  echo $item['content'];?></textarea>
				</div>
			</div>
			<script language='javascript'>
			require(['jquery', 'util'], function($, u){ $(function(){ u.editor($('.richtext')[0]); }); });
			</script>
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