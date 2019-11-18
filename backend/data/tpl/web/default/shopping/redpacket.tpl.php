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
	<li>红包管理</li>
</ol>
<ul class="nav nav-tabs">
	<li <?php  if($operation=='display') { ?> class="active" <?php  } ?>>
		<a href="<?php  echo url('shopping/redpacket/type', array('m'=>'ewei_shopping'))?>">红包管理</a>
	</li>
	<li <?php  if($operation == 'handle') { ?> class="active"<?php  } ?>>
		<a href="<?php  echo url('shopping/redpacket/type', array('op'=>'handle'))?>"><?php echo $typeid ? '修改' : '添加'?>红包</a>
	</li>
	<?php  if($operation=='detail') { ?><li  class="active"><a href="##">红包详细</a></li>
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
					<th>红包总数</th>
					<th>领取总数</th>
					<th>开始时间</th>
					<th>结束时间</th>
					<th>分享标题</th>
					<th>分享描述</th>
					<th>分享图标</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr class="list" data-sid='<?php  echo $item["id"];?>'>
					<td><?php  echo $item['id'];?></td>
					<td><?php  echo $item['total'];?></td>
					<td><?php  echo $item['use_total'];?></td>
					<td><?php  echo date('Y-m-d H:i',$item['startdt'])?></td>
					<td><?php  echo date('Y-m-d H:i',$item['endingdt'])?></td>
					<td><?php  echo $item['sharetitle'];?></td>
					<td><?php  echo $item['sharedesc'];?></td>
					<td><img style="width: 25%;" src="<?php  echo $item['sharethumb'];?>"/></td>
					<td>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/redpacket/type',array('id'=>$item['id'], 'op'=>'handle'))?>">编辑</a>
						<a class="btn btn-info btn-sm" href="<?php  echo url('shopping/redpacket/type',array('id'=>$item['id'],'op'=>'detail'))?>">详情</a>

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
	<div class="panel-heading"><?php  echo $op_type;?>红包详情</div>
	<div class="panel-body">
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">类型ID：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['id'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">红包编号SN：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['sn'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">红包数量:(个)</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['total'];?></span>
			</div>
		</div>

		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">已领取数:(个)</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['use_total']?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">创建时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['createdt'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">开始时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['startdt'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">结束时间：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo date('Y-m-d H:i:s', $detailData['endingdt'])?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">分享标题：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['sharetitle'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">分享详细：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><?php  echo $detailData['sharedesc'];?></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">分享图标：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><img style="width: 30%;" src="<?php  echo $detailData['sharethumb'];?>"/></span>
			</div>
		</div>
		<div class="form-group" style="float:left;width:32%">
			<label class="col-xs-12 col-md-3 control-label">红包二维码：</label>
			<div class="col-sm-2" style="width:180px">
				<span style="color:red"><img style="width: 50%;" src="http://pan.baidu.com/share/qrcode?w=150&h=150&url=http://www.baidu.com/coupon/group-redpacket?sn=<?php  echo $detailData['sn'];?>"/></span>
			</div>
		</div>
	</div>
</div>
<?php  } else if($operation == 'handle') { ?>
<form action="" method="post" class="form-horizontal form" enctype="multipart/form-data">
	<div class="panel panel-default">
		<div class="panel-heading"><?php echo $typeid ? '修改' : '添加'?>红包类型</div>
		<div class="panel-body">
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">红包数量：</label>
				<div class="col-sm-3 col-xs-3">
					<input  class="form-control" name="total" value="<?php  echo $detailData['total'];?>" <?php echo $typeid ? 'disabled' :'' ?>>
					<span class="help-block"><strong style="color:red">提示: </strong>必须是整数</span>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">开始时间：</label>
				<div class="col-sm-3 col-xs-3">
					<?php echo tpl_form_field_date('startdt', !empty($detailData['startdt']) ? date('Y-m-d H:i',$detailData['startdt']) : date('Y-m-d H:i'), 1)?>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">结束时间：</label>
				<div class="col-sm-3 col-xs-3">
					<?php echo tpl_form_field_date('endingdt', !empty($detailData['endingdt']) ? date('Y-m-d H:i',$detailData['endingdt']) : date('Y-m-d H:i'), 1)?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享标题：</label>
				<div class="col-sm-3 col-xs-3">
					<input  class="form-control" name="sharetitle" value="<?php  echo $detailData['sharetitle'];?>">
					<span class="help-block"><strong style="color:red">提示: </strong>微信分享标题</span>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享详情：</label>
				<div class="col-sm-3 col-xs-3">
					<input  class="form-control" name="sharedesc" value="<?php  echo $detailData['sharedesc'];?>">
					<span class="help-block"><strong style="color:red">提示: </strong>微信分享描述</span>
    			</div>
			</div>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">分享图标：</label>
				<div class="col-sm-3 col-xs-3">
					<?php  echo tpl_form_field_image('sharethumb', $detailData['sharethumb'], '', array('extras' => array('text' => 'readonly')))?>
    			</div>
			</div>
			<?php  if($typeid=='') { ?>
			<div class="form-group">
				<label class="col-xs-12 col-sm-3 col-md-2 control-label">优惠券类型选择：</label>
				<div class="col-sm-4 col-xs-4">
					<div class="selectbox">
					    <div class="select-bar">
					        <select multiple="multiple" id="select1">
					        	<?php  if(is_array($couponList)) { foreach($couponList as $key => $item) { ?>
					            <option value="<?php  echo $item['id'];?>"><?php  echo $item['name'];?></option>
					            <?php  } } ?>
					        </select>
					    </div>
					    
					    <div class="btn-bar">
					        <span id="add"><input type="button" class="btn" value=">"/></span><br />
					        <span id="add_all"><input type="button" class="btn" value=">>"/></span><br />
					        <span id="remove"><input type="button" class="btn" value="<"/></span><br />
					        <span id="remove_all"><input type="button" class="btn" value="<<"/></span>
					    </div>
					    
					    <div class="select-bar"><select multiple="multiple" id="select2"></select></div>
					</div>
					<div class="selected-type">
						
					</div>
				</div>
			</div>
			<?php  } ?>
    		<!-- <textarea class="form-control" id="description" name="description" rows="2"><?php  echo $item['description'];?></textarea> -->
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