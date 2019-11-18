<?php defined('IN_IA') or exit('Access Denied');?><?php  $newUI = true;?>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<style>
	.selectbox{width:500px;height:220px;margin:40px auto 0 auto;}
	.selectbox div{float:left;}
	.selectbox .select-bar{padding:0 20px;}
	.selectbox .select-bar select{width:150px;height:200px;border:4px #A0A0A4 outset;padding:4px;}
	.selectbox .btn{width:50px; height:30px; margin-top:10px; cursor:pointer;}
</style>
<ol class="breadcrumb" style="padding:5px 0;">
	<li><a href="<?php  echo url('home/welcome/ext');?>"><i class="fa fa-cogs"></i> &nbsp; 微商城</a></li>
	<li>商城功能</li>
	<li>新闻管理</li>
</ol>
<ul class="nav nav-tabs">
	<li <?php  if($operation=='display') { ?> class="active" <?php  } ?>>
		<a href="<?php  echo url('shopping/news/news', array('m'=>'ewei_shopping'))?>">新闻管理</a>
	</li>
	<li <?php  if($operation == 'handle') { ?> class="active"<?php  } ?>>
		<a href="<?php  echo url('shopping/news/news', array('op'=>'handle'))?>"><?php echo $typeid ? '修改' : '添加'?>新闻</a>
	</li>
	<?php  if($operation=='detail') { ?><li  class="active"><a href="##">新闻详细</a></li>
	<?php  } ?>
</ul>
<?php  if($operation=='display') { ?>
<p class="text-danger">
	状态：展示使用逆序
</p>
<div class="panel panel-default">
	<div class="panel-body table-responsive" >
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th>类型ID</th>
					<th>新闻标题</th>
					<th>创建时间</th>
					<th>新闻来源</th>
					<th>浏览pv</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr class="list" data-sid='<?php  echo $item["id"];?>'>
					<td><?php  echo $item['id'];?></td>
					<td><?php  echo $item['title'];?></td>
					<td><?php  echo date('Y-m-d H:i:s', $item['createdt'])?></td>
					<td><?php  if(empty($item['outlink'])) { ?><span class="label label-warning">消息录入</span><?php  } else { ?><span class="label label-info">微信转载</span><?php  } ?></td>
					<td><?php  echo $item['pv'];?></td>
					<td>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/news/news',array('id'=>$item['id'], 'op'=>'handle'))?>">编辑</a>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/news/news',array('id'=>$item['id'],'op'=>'detail'))?>">详情</a>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/news/news',array('id'=>$item['id'],'op'=>'del'))?>">删除</a>
					</td>
				</tr>

				<?php  } } ?>
			</tbody>
		</table>
	</div>
</div>
<?php  echo $pager;?>
<?php  } else if($operation=='detail') { ?>
<div class="panel panel-default">
	<div class="panel-heading"><?php  echo $op_type;?>新闻详情</div>
	<div class="panel-body">
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">类型ID：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['id'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">创建时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['createdt'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">标题：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['title'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:100%">
			<label class="col-xs-12 col-md-3 control-label" style="width:10%">外部链接：</label>
			<div class="col-sm-2" style="width:60%">
				<span style="color:red"><?php  echo $detailData['outlink'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:100%">
			<label class="col-xs-12 col-md-3 control-label" style="width:10%">内容简介：</label>
			<div class="col-sm-2" style="width:60%">
				<span style="color:red"><?php  echo $detailData['intro'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:100%">
			<label class="col-xs-12 col-md-3 control-label" style="width:10%">详细内容：</label>
			<div class="col-sm-2" style="width:60%">
				<span style="color:red"><?php  echo $detailData['content'];?></span>
			</div>
		</div>
	</div>
</div>
<?php  } else if($operation == 'handle') { ?>
<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $typeid ? '修改' : '添加'?>新闻</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">新闻标题：</label>
				<div class="col-sm-3 col-xs-3">
					<input  class="form-control" name="title" value="<?php  echo $detailData['title'];?>">
					<span class="help-block"><strong style="color:red">提示: </strong>不能为空</span>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">新闻类型：</label>
				<div class="col-sm-7 col-lg-9 col-xs-12">
					<select class="form-control" style="margin-right:15px;width:120px;" name="cateid" >
						<option value="1"<?php  if($detailData['cateid']=='1'||!$detailData['cateid']) { ?> selected="selected"<?php  } ?>>公司新闻</option>
						<option value="2"<?php  if($detailData['cateid']=='2') { ?> selected="selected"<?php  } ?>>行业资讯</option>
						<option value="3"<?php  if($detailData['cateid']=='3') { ?> selected="selected"<?php  } ?>>健康频道</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">生成时间：</label>
				<div class="col-sm-3 col-xs-3">
					<?php echo tpl_form_field_date('createdt', !empty($detailData['createdt']) ? date('Y-m-d H:i',$detailData['createdt']) : date('Y-m-d H:i'), 1)?>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">新闻简介：</label>
				<div class="col-sm-3 col-xs-3">
					<input  class="form-control" name="intro" value="<?php  echo $detailData['intro'];?>">
					<span class="help-block"><strong style="color:red">提示: </strong>新闻内容简介</span>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">新闻外链：</label>
				<div class="col-sm-3 col-xs-3">
					<input  class="form-control" name="outlink" value="<?php  echo $detailData['outlink'];?>">
					<span class="help-block"><strong style="color:red">提示: </strong>此处填写外部新闻链接</span>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">新闻详情：</label>
				<div class="col-sm-9 col-xs-12">
					<textarea name="content" class="form-control richtext" cols="10" rows="30"><?php  echo $detailData['content'];?></textarea>
				</div>
			</div>
<script language='javascript'>
	require(['jquery', 'util'], function($, u){
		$(function(){
			u.editor($('.richtext')[0]);
		});
	});
</script>
    		-->
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
<script type="text/javascript">
$(function(){
	window.typeids=[];
    //移到右边
    $('#add').click(function(){
        //获取选中的选项，删除并追加给对方
        var id=$('#select1 option:selected').val();
        $('#select1 option:selected').appendTo('#select2');
        $(".selected-type").prepend("<input type='hidden' name='typeids[]' value='"+id+"'>");

    });
    //移到左边
    $('#remove').click(function(){
    	var id=$("#select2 option:selected").val();
        $('#select2 option:selected').appendTo('#select1');
        //删除typeid
        $(".selected-type input[value="+id+"]").remove();

    });
    
    //全部移到右边
    $('#add_all').click(function(){
        //获取全部的选项,删除并追加给对方
        jQuery('#select1 option').each(function(key,value){
        	var id=$(this).val();
        	$(".selected-type").prepend("<input type='hidden' name='typeids[]' value='"+id+"'>");
        });
        $('#select1 option').appendTo('#select2');
    });
    
    //全部移到左边
    $('#remove_all').click(function(){
        $('#select2 option').appendTo('#select1');
        $(".selected-type input").remove();
    });
    
    //双击选项
    $('#select1').dblclick(function(){ //绑定双击事件
        //获取全部的选项,删除并追加给对方
        var id=$('#select1 option:selected').val();
        $(".selected-type").prepend("<input type='hidden' name='typeids[]' value='"+id+"'>");
        $("option:selected",this).appendTo('#select2'); //追加给对方
    });
    
    //双击选项
    $('#select2').dblclick(function(){
    	var id=$("#select2 option:selected").val();
        $("option:selected",this).appendTo('#select1');
        $(".selected-type input[value="+id+"]").remove();
    });
    
});
</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 0) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>