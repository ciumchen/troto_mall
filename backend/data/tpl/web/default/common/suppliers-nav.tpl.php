<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<?php  if($do == 'suppliers') { ?>
		<li <?php  if($do == 'suppliers' && $operation == 'display') { ?> class="active" <?php  } ?>>
			<a href="<?php  echo url('ma/suppliers/suppliers')?>">供应商</a>
		</li>
		<li <?php  if($do == 'suppliers' && $operation == 'handle') { ?> class="active" <?php  } ?>>
			<a href="<?php  echo url('ma/suppliers/suppliers', array('op'=>'handle'))?>">
				<?php  echo $ptr_title;?>
			</a>
		</li>
	<?php  } ?>
	<?php  if($do == 'address') { ?>
		<li <?php  if($do == 'address' && $operation == 'display') { ?> class="active"<?php  } ?>>
			<a href="<?php  echo url('ma/suppliers/address')?>">供应商地址</a>
		</li>
		<li <?php  if($do == 'address' && $operation == 'handle') { ?> class="active" <?php  } ?>>
			<a href="<?php  echo url('ma/suppliers/address', array('op'=>'handle', 'sid' => $_GPC['sid']))?>">
				<?php  echo $ptr_title;?>
			</a>
		</li>
	<?php  } ?>
</ul>