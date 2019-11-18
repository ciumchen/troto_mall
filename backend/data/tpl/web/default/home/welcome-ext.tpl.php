<?php defined('IN_IA') or exit('Access Denied');?><?php  if($moudles) { ?>
<?php  if($enable_modules && ($_W['user']['power'] & ADMINISTRATOR)) { ?>
<div class="page-header">
	<h4><i class="fa fa-cubes"></i> 已启用的模块</h4>
</div>
<div class="panel panel-default row">
	<div class="table-responsive panel-body">
	<table class="table">
		<tr>
			<th style="width:10%"></th>
			<th style="width:15%">模块名称</th>
			<th style="width:10%;">标识</th>
			<th style="width:40%">描述</th>
		</tr>
		<?php  if(is_array($enable_modules)) { foreach($enable_modules as $key => $row) { ?>
		<tr>
			<td>
				<?php  if(file_exists($buildinpath.$key.'/icon.jpg')) { ?>
				<img alt="<?php  echo $row['title'];?>" src="../framework/builtin/<?php  echo $key;?>/icon.jpg" class="img-rounded">
				<?php  } else if(file_exists($path.$key.'/icon.jpg')) { ?>
				<img alt="<?php  echo $row['title'];?>" src="../addons/<?php  echo $key;?>/icon.jpg" width="48" height="48" class="img-rounded">
				<?php  } else { ?>
				<img alt="<?php  echo $row['title'];?>" src="./resource/images/nopic-small.jpg" width="48" height="48" class="img-rounded">
				<?php  } ?>
			</td>
			<td>
				<?php  echo $row['title'];?>
				<?php  if($row['official']) { ?>
				<i class="official"><img src="resource/images/module/official.png"/></i>
				<?php  } ?>
			</td>
			<td>
				<?php  echo $row['name'];?>
			</td>
			<td>
				<?php  echo $row['ability'];?>
			</td>
			<td>
				
			</td>
		</tr>
		<?php  } } ?>
	</table>
</div>
</div>
<?php  } ?>
<?php  if($unenable_modules && ($_W['user']['power'] & ADMINISTRATOR)) { ?>
<div class="page-header">
	<h4><i class="fa fa-cubes"></i> 未启用的模块</h4>
</div>
<div class="panel panel-default row">
	<div class="table-responsive panel-body">
	<table class="table">
		<tr>
			<th style="width:10%"></th>
			<th style="width:15%">模块名称</th>
			<th style="width:10%;">标识</th>
			<th style="width:40%">描述</th>
			<th>
				
			</th>
		</tr>
		<?php  if(is_array($unenable_modules)) { foreach($unenable_modules as $key => $row) { ?>
		<tr>
			<td>
				<?php  if(file_exists($path.$key.'/icon.jpg')) { ?>
				<img alt="<?php  echo $row['title'];?>" src="../addons/<?php  echo $key;?>/icon.jpg" width="48" height="48" class="img-rounded">
				<?php  } else { ?>
				<img alt="<?php  echo $row['title'];?>" src="./resource/images/nopic-small.jpg" width="48" height="48" class="img-rounded">
				<?php  } ?>
			</td>
			<td>
				<?php  echo $row['title'];?>
				<?php  if($row['official']) { ?>
				<i class="official"><img src="resource/images/module/official.png"/></i>
				<?php  } ?>
			</td>
			<td>
				<?php  echo $row['name'];?>
			</td>
			<td>
				<?php  echo $row['ability'];?>
			</td>
			<td>
				&nbsp;
			</td>
		</tr>
		<?php  } } ?>
	</table>
	</div>
</div>

<?php  } ?>
<?php  } else { ?>
<!--
	<?php  if($_W['user']['power'] & PRODUCT && !($_W['user']['power'] & ORDER) || $_W['user']['power'] & ADMINISTRATOR) { ?>
	<div class="page-header">
		<h4><i class="fa fa-plane"></i> 核心功能设置</h4>
	</div>
	
	<div class="shortcut clearfix">
		<?php  if($entries['cover']) { ?>
			<?php  if(is_array($entries['cover'])) { foreach($entries['cover'] as $cover) { ?>
			<a href="<?php  echo $cover['url'];?>" title="<?php  echo $cover['title'];?>">
				<i class="fa fa-external-link-square"></i>
				<span><?php  echo $cover['title'];?></span>
			</a>
			<?php  } } ?>
		<?php  } ?>
		<?php  if($module['isrulefields']) { ?>
			<a href="<?php  echo url('platform/reply', array('m' => $m))?>">
				<i class="fa fa-comments"></i>
				<span>回复规则列表</span>
			</a>
		<?php  } ?>
		<?php  if($entries['home'] || $entries['profile'] || $entries['shortcut']) { ?>
			<?php  if($entries['home']) { ?>
				<a href="<?php  echo url('site/nav/home', array('m' => $m))?>">
					<i class="fa fa-home"></i>
					<span>微站首页导航</span>
				</a>
			<?php  } ?>
			<?php  if($entries['profile']) { ?>
				<a href="<?php  echo url('site/nav/profile', array('m' => $m))?>">
					<i class="fa fa-user"></i>
					<span>个人中心导航</span>
				</a>
			<?php  } ?>
			<?php  if($entries['shortcut']) { ?>
				<a href="<?php  echo url('site/nav/shortcut', array('m' => $m))?>">
					<i class="fa fa-plane"></i>
					<span>快捷菜单</span>
				</a>
			<?php  } ?>
		<?php  } ?>
		<?php  if($module['settings']) { ?>
			<a href="<?php  echo url('profile/module/setting', array('m' => $m));?>">
				<i class="fa fa-cog"></i>
				<span title="参数设置">参数设置</span>
			</a>
		<?php  } ?>
	</div>
	<?php  } ?>
	<?php  if(empty($module['issolution'])) { ?>
	<?php  if($entries['menu']) { ?>
		<div class="page-header">
			<h4><i class="fa fa-plane"></i> 业务功能菜单</h4>
		</div>
		<div class="shortcut clearfix">
			<?php  if($_W['user']['power'] & FINANCE) { ?>
			<a href="<?php  echo url('ma/manager/exchange');?>">
				<i class="fa fa-puzzle-piece"></i>
				<span>佣金兑换管理</span>
			</a>
			<?php  } ?>
			<?php  if($_W['user']['power'] & PRODUCT) { ?>
			<?php  if(is_array($entries['menu'])) { foreach($entries['menu'] as $menu) { ?>
				<?php  if(($_W['user']['power'] & ADMINISTRATOR)) { ?>
					<a href="<?php  echo $menu['url'];?>" title="<?php  echo $menu['title'];?>">
						<i class="fa fa-puzzle-piece"></i>
						<span><?php  echo $menu['title'];?></span>
					</a>
				<?php  } else if($_W['user']['power'] & PRODUCT && ($_W['user']['power'] & ORDER)) { ?>
					<?php  if($menu['title'] == '订单管理') { ?>
					<a href="<?php  echo $menu['url'];?>" title="<?php  echo $menu['title'];?>">
						<i class="fa fa-puzzle-piece"></i>
						<span><?php  echo $menu['title'];?></span>
					</a>
					<?php  } ?>
				<?php  } else { ?>
					<?php  if($_W['user']['power'] & PRODUCT && !($_W['user']['power'] & ORDER)) { ?>
					<a href="<?php  echo $menu['url'];?>" title="<?php  echo $menu['title'];?>">
						<i class="fa fa-puzzle-piece"></i>
						<span><?php  echo $menu['title'];?></span>
					</a>
					<?php  } ?>
				<?php  } ?>
			<?php  } } ?>
			<?php  } ?>
		</div>
	<?php  } ?>
	<?php  } else { ?>
		<div class="page-header">
			<h4><i class="fa fa-plane"></i> 功能分权 (仅限行业模块)</h4>
		</div>
		<div class="shortcut clearfix">
			<a href="<?php  echo url('profile/worker', array('m' => $m, 'reference' => 'solution'));?>">
				<i class="fa fa-users"></i>
				<span>设置操作用户</span>
			</a>
			<a href="<?php  echo url('home/welcome/solution', array('m' => $m));?>">
				<i class="fa fa-cogs"></i>
				<span>进入管理后台</span>
			</a>
		</div>
	<?php  } ?>
-->
<?php  } ?>
<?php  if(($_W['user']['power'] & PRODUCT || $_W['user']['power'] & ADMINISTRATOR || $_W['user']['power'] & FINANCE)) { ?>
<div class="page-header">
	<h4><i class="fa fa-money"></i> 商城功能</h4>
</div>
<div class="shortcut clearfix">
	<a href="<?php  echo url('site/entry/order', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-strikethrough"></i>
		<span>订单管理</span>
	</a>
	<a href="<?php  echo url('site/entry/goods', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-puzzle-piece"></i>
		<span>商品管理</span>
	</a>
	<a href="<?php  echo url('site/entry/category', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-share-alt"></i>
		<span>商品分类管理</span>
	</a>
	<a href="<?php  echo url('shopping/topic', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paper-plane-o"></i>
		<span>销售专题管理</span>
	</a>
	<a href="<?php  echo url('shopping/ads', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-diamond"></i>
		<span>广告幻灯片管理</span>
	</a>
	<!-- <a href="<?php  echo url('ma/finance/display',array('type'=>'order'))?>">
		<i class="fa fa-puzzle-piece"></i>
		<span>基础统计</span>
	</a>
	<a href="<?php  echo url('ma/finance/orderCount',array('status'=>'all'))?>">
		<i class="fa fa-puzzle-piece"></i>
		<span>订单统计</span>
	</a>
	<a href="<?php  echo url('ma/finance/commCount',array('type'=>'all'))?>">
		<i class="fa fa-puzzle-piece"></i>
		<span>佣金兑换统计</span>
	</a>
	<a href="<?php  echo url('ma/finance/incomeCount',array('type'=>'ok'))?>">
		<i class="fa fa-puzzle-piece"></i>
		<span>收入统计</span>
	</a> -->
</div>
<div class="page-header">
	<h4><i class="fa fa-plane"></i> 运营及活动</h4>
</div>
<div class="shortcut clearfix">
	<a href="<?php  echo url('profile/worker', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-user-plus"></i>
		<span>操作人员列表</span>
	</a>
	<!-- <a href="<?php  echo url('ma/suppliers/suppliers')?>">
		<i class="fa fa-strikethrough"></i>
		<span>佣金历史记录</span>
	</a>
	<a href="<?php  echo url('shopping/reward/manage', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-puzzle-piece"></i>
		<span>佣金申请处理</span>
	</a>-->
	<a href="<?php  echo url('shopping/coupon/manage', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-gift"></i>
		<span>优惠券管理</span>
	</a>
	<a href="<?php  echo url('shopping/coupon/type', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-gift"></i>
		<span>优惠券类型</span>
	</a>
	<a href="<?php  echo url('shopping/redpacket/type', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-gift"></i>
		<span>红包管理</span>
	</a>
	<a href="<?php  echo url('shopping/news/news', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-diamond"></i>
		<span>新闻管理</span>
	</a>
	<!-- <a href="<?php  echo url('ma/suppliers/suppliers')?>">
		<i class="fa fa-strikethrough"></i>
		<span>佣金历史记录</span>
	</a>
	<a href="<?php  echo url('shopping/reward/manage', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-puzzle-piece"></i>
		<span>佣金申请处理</span>
	</a> -->
</div>
<div class="page-header">
	<h4><i class="fa fa-barcode"></i> 业务支撑配置</h4>
</div>
<div class="shortcut clearfix">
	<a href="<?php  echo url('site/entry/branding', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paragraph"></i>
		<span>品牌管理</span>
	</a>
	<a href="<?php  echo url('ma/suppliers/suppliers')?>">
		<i class="fa fa-paragraph"></i>
		<span>供应商管理</span>
	</a>
	<a href="<?php  echo url('site/entry/warehouse', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paragraph"></i>
		<span>仓库管理</span>
	</a>
	<!-- <a href="<?php  echo url('site/entry/express', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paragraph"></i>
		<span>物流管理</span>
	</a>
	<a href="<?php  echo url('site/entry/orderexpress', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paragraph"></i>
		<span>发货管理</span>
	</a> -->
	<a href="<?php  echo url('site/entry/cabinet', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paragraph"></i>
		<span>货柜管理</span>
	</a>

	<a href="<?php  echo url('site/entry/servicefee', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paragraph"></i>
		<span>服务费管理</span>
	</a>
	<?php  if(($_W['user']['power'] & ADMINISTRATOR || $_W['user']['power'] & FINANCE)) { ?>
	<a href="<?php  echo url('site/entry/corpteam', array('m'=>'ewei_shopping'))?>">
		<i class="fa fa-paragraph"></i>
		<span>主副卡管理</span>
	</a>
	<?php  } ?>
</div>
<?php  } ?>
<script type="text/javascript">
<!--
<?php  if($_W['isfounder']) { ?>
	function checkupgradeModule() {
		require(['util'], function(util) {
			if (util.cookie.get('checkupgrade_<?php  echo $m;?>')) {
				return;
			}
			$.getJSON("<?php  echo url('utility/checkupgrade/module', array('m' => $m));?>", function(ret){
				if (ret && ret.message && ret.message.upgrade == '1') {
					$('body').prepend('<div id="upgrade-tips-module" class="upgrade-tips"><a class="module" href="./index.php?c=cloud&a=upgrade&">【'+ret.message.name+'】检测到新版本'+ret.message.version+'，请尽快更新！</a><span class="tips-close" onclick="checkupgradeModule_hide()"><i class="fa fa-times-circle"></i></span></div>');
					if ($('#upgrade-tips').size()) {
						$('#upgrade-tips-module').css('top', '25px');
					}
				}
			});
		});
	}
	function checkupgradeModule_hide() {
		require(['util'], function(util) {
			util.cookie.set('checkupgrade_<?php  echo $m;?>', 1, 3600);
			$('#upgrade-tips-module').hide();
		});
	}
	$(function(){// checkupgradeModule();});
<?php  } ?>
//-->
</script>
