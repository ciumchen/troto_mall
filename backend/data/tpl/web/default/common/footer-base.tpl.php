<?php defined('IN_IA') or exit('Access Denied');?>	<script>require(['bootstrap']);</script>
	<div class="container-fluid footer" role="footer">
		<div class="page-header"></div>
		<span class="pull-left">
			<p><?php  if(empty($_W['setting']['copyright']['footerleft'])) { ?> <b><?php  echo $_W['config']['site']['name'];?></b>  &copy; <?php  echo date('Y')?> <?php  } else { ?><?php  echo $_W['setting']['copyright']['footerleft'];?><?php  } ?></p>
		</span>
		<!-- <span class="pull-right">
			<p><?php  if(empty($_W['setting']['copyright']['footerright'])) { ?><a href="http://www.we7.cc">关于微擎</a><a href="http://bbs.we7.cc">微擎帮助</a><?php  } else { ?><?php  echo $_W['setting']['copyright']['footerright'];?><?php  } ?><?php  if(!empty($_W['setting']['copyright']['statcode'])) { ?>&nbsp; &nbsp; <?php  echo $_W['setting']['copyright']['statcode'];?><?php  } ?></p>
		</span> -->
	</div>
</body>
</html>