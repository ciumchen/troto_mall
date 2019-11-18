<?php
/**
 * count.ctrl.php
 * 统计
 *
 */

defined('IN_IA') or exit('Access Denied');
$dos = array('display','more', 'fromDetail');
$do = in_array($do, $dos) ? $do : 'display';
load()->model('goods.visit');
$op = $_GPC['op'];
if($do === 'display') {
	goods_getGoodsDetailsRecord();
} else if ($do == 'more'){
	if($_W['isajax']){
		$btype = $_GPC['times'][1];
		$manyDate = intval($_GPC['times'][0]);
		$arr = array();
		$arr['Date'] = $manyDate;
		$arr['btype'] = $btype;
		$msc = goods_AloneDateSelectToItems($arr);
		if($msc != null){
			$msc['status'] = 200;
		} else {
			$msc['status'] = -200;
		}
		ajaxReturn($msc);
	}
/*****************************/
	if($op == 'detail'){
		$title = "访问记录";
		$info_title = "访问的商品";
		$res = goods_getLogDetailFromList();
	} else if ($op == 'search'){
		$title = "搜索记录";
		$info_title = "搜索的名称";
		$res = goods_getLogDetailFromList();
	} else if ($op == 'class'){
		$title = "分类记录";
		$info_title = "访问的分类";
		$res = goods_getLogDetailFromList();
	} else if ($op == 'delete'){
		$id = intval($_GPC['fromid']);
		if(!empty($id)){
			pdo_delete("log_goods_visit",array('id' => $id));
			message("删除成功！",referer(),'success');
		} else {
			message("删除失败！参数有误！",referer(),'error');
		}
	} else if ($op == 'AloneDetail'){
		$pres = explode(',', $_GPC['date_time']);
		$pres['Date'] = $pres[0];
		$pres['btype'] = $pres[1];
		unset($pres[0]);
		unset($pres[1]);
		$res = goods_AloneDateSelectToItems($pres);
		//pre($res['items'][0]);
	}
}

template('census/count');
