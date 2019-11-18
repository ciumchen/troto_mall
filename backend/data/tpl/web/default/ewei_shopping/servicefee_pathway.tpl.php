<?php defined('IN_IA') or exit('Access Denied');?><table class="table table-hover row" style="">
	<thead>
	<tr class="">
		<th width="150">市</th>
		<th width="110">区县</th>
		<th width="60">状态</th>
		<th width="60">服务费</th>
	</tr>
	</thead>
	<tbody id="param-items">
		<?php  if(is_array($cabinetPathwayList)) { foreach($cabinetPathwayList as $p) { ?>
		<tr>
			<td>
				<input name="pathwayid[]" type="hidden" class="form-control param_title" value="<?php  echo $p['pathwayid'];?>"/>
				<input name="pathway[]" type="text" class="form-control param_title" value="<?php  echo $p['pathway'];?>" readonly="readonly" />
			</td>
			<td>
				<select name="status[]" class='form-control pathway-status'>
					<option value="1"<?php  if($p['status']==1) { ?> selected="selected"<?php  } ?>>启用</option>
					<option value="0"<?php  if($p['status']==0) { ?> selected="selected"<?php  } ?>>未启用</option>
					<option value="-1"<?php  if($p['status']==-1) { ?> selected="selected"<?php  } ?>>维护</option>
				</select>
			</td>
			<td><input name="price[]" type="text" class="form-control param_value" value="<?php  echo $p['price'];?>"/></td>
			<td><input name="stock[]" type="text" class="form-control" value="<?php  echo $p['stock'];?>"/></td>
			<td>
				<select name="goodsid[]" class="pathway-goodsid" style="margin-right:15px; width:100%; height:35px;">
					<option value="0">请选择轨道绑定的商品</option>
					<?php  if(is_array($goodsList)) { foreach($goodsList as $row) { ?>
						<option value="<?php  echo $row['id'];?>" <?php  if($row['id']==$p['goodsid']) { ?> selected="selected"<?php  } ?>>【<?php  echo $parentCateList[$row['pcate']]['name'];?> - <?php  echo $row['brand'];?>】 <?php  echo $row['title'];?></option>
					<?php  } } ?>
				</select>
			</td>
			<td><input name="productsn[]" type="text" class="form-control param_value" value="<?php  echo $p['productsn'];?>"/></td>
		</tr>
		<?php  } } ?>
	</tbody>
</table>
<span class="help-block">提示：机柜大区位置需要修改直接找技术人员；轨道库存系统自动维护，人工更新请先确保数据准确对接</span>

<script>
$('.pathway-status').select2();
$('.pathway-goodsid').select2();
</script>