<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'manage', 'modal', 'credit_record', 'stat','downloadExcel');
$do = in_array($do, $dos) ? $do : 'display';

$creditnames = uni_setting($_W['uniacid'], array('creditnames'));
$creditnames = $creditnames['creditnames'];
if($creditnames) {
	foreach($creditnames as $index => $creditname) {
		if(empty($creditname['enabled'])) {
			unset($creditnames[$index]);
		}
	}
	$select_credit = implode(', ', array_keys($creditnames));
} else {
	$select_credit = '';
}

if($do == 'display') {
	$_W['page']['title'] = '积分列表 - 会员积分管理 - 会员中心';
	
	$where = ' WHERE uniacid = :uniacid ';
	$params = array(':uniacid' => $_W['uniacid']);
	$type = intval($_GPC['type']);
	$keyword = trim($_GPC['keyword']);
	if($type == 1) {
		$keyword = intval($_GPC['keyword']);
		if ($keyword > 0) {
			$where .= ' AND uid = :uid';
			$params[':uid'] = $keyword;
		}
	} elseif($type == 2) {
		if ($keyword) {
			$where .= " AND mobile LIKE :mobile";
			$params[':mobile'] = "%{$keyword}%";
		}
	} elseif($type == 3) {
		if ($keyword) {
			$where .= " AND realname LIKE :realname";
			$params[':realname'] = "%{$keyword}%";
		}
	} elseif($type == 4) {
		if ($keyword) {
			$where .= " AND nickname LIKE :nickname";
			$params[':nickname'] = "%{$keyword}%";
		}
	}
	$pindex = max(1, intval($_GPC['page']));
	$psize = 20;
	
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('mc_members') . $where, $params);
	$list = pdo_fetchall("SELECT uid, uniacid, email, nickname, realname, mobile, {$select_credit} FROM " . tablename('mc_members') . $where . ' ORDER BY uid DESC LIMIT ' . ($pindex - 1) * $psize .',' . $psize, $params);
	$pager = pagination($total, $pindex, $psize);
	if(count($list) == 1 && $list[0]['uid'] && !empty($keyword)) {
		$status = 1;
		$uid = $list[0]['uid'];
	} else {
		foreach($list as &$li) {
			if(empty($li['email']) || (!empty($li['email']) && substr($li['email'], -6) == 'we7.cc' && strlen($li['email']) == 39)) {
				$li['email'] = '未完善';
			}
		}
		$status = 0;
	}
}

if($do == 'manage') {
	load()->model('mc');
	$uid = intval($_GPC['uid']);	
	$info = mc_fetch($_GPC['uid'], array('nickname'));
	$nickname = !empty($info['nickname']) ? $info['nickname'] : $_GPC['uid']; 
	$ret = "充值提示: \n\t管理员“{$_W['username']}” 给 “{$nickname}”";
	if($uid) {
		foreach($creditnames as $index => $creditname) {
			if(($_GPC[$index . '_type'] == 1 || $_GPC[$index . '_type'] == 2) && $_GPC[$index . '_value']) {
				$value = $_GPC[$index . '_type'] == 1 ? $_GPC[$index . '_value'] : - $_GPC[$index . '_value'];
				$ret .= (($index == 'credit2')?"\n\t余额充值：":"\n\t红包充值：").$value.'元';
				$return = mc_credit_update($uid, $index, $value, array($_W['uid'], trim($_GPC['remark'])));
				if(is_error($return)) {
					message($return['message']);
				}
			} else {
				continue;
			}
		}
		$ret .= "\n\t备注：".((empty($_GPC['remark'])) ? '该客服没有备注':$_GPC['remark']);
		// wechatPush('oGxhxt5W1FmpijcUnjOKAommyXxs', array('text'=>array('content'=>$ret)));
		if($_W['uniacid'] == 17){
			wechatPush('opZZjs3o2cO1w9ztqdF4WEUELbu4', array('text'=>array('content'=>$ret)));
		}
		message('会员积分操作成功', url('mc/creditmanage/display'));
	} else {
		message('未找到指定用户', url('mc/creditmanage/display'), 'error');
	}
}

if($do == 'modal') {
	if($_W['isajax']) {
		$uid = intval($_GPC['uid']);
		$data = pdo_fetch("SELECT uid, nickname, realname, email, mobile, uniacid, {$select_credit} FROM " . tablename('mc_members') . ' WHERE uid = :uid AND uniacid = :uniacid', array(':uniacid' => $_W['uniacid'], ':uid' => $uid));
		if(empty($data['email']) || (!empty($data['email']) && substr($data['email'], -6) == 'we7.cc' && strlen($data['email']) == 39)) {
			$data['email'] = '未完善';
		}
		$data ? template('mc/modal') : exit('dataerr');
		exit();
	}
}

if($do == 'credit_record') {
	$uid = intval($_GPC['uid']);
	$credits = array_keys($creditnames);
	$type = trim($_GPC['type']) ? trim($_GPC['type']) : $credits[0];
	
	$pindex = max(1, intval($_GPC['page']));
	$psize = 50;
	$total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('mc_credits_record') . ' WHERE uid = :uid AND uniacid = :uniacid AND credittype = :credittype ', array(':uniacid' => $_W['uniacid'], ':uid' => $uid, ':credittype' => $type));
	$data = pdo_fetchall("SELECT r.*, u.username FROM " . tablename('mc_credits_record') . ' AS r LEFT JOIN ' .tablename('users') . ' AS u ON r.operator = u.uid ' . ' WHERE r.uid = :uid AND r.uniacid = :uniacid AND r.credittype = :credittype ORDER BY id DESC LIMIT ' . ($pindex - 1) * $psize .',' . $psize, array(':uniacid' => $_W['uniacid'], ':uid' => $uid, 'credittype' => $type));
	$pager = pagination($total, $pindex, $psize);
	template('mc/credit_record');
	exit;
}

if($do == 'stat') {
	load()->func('tpl');
	$uid = intval($_GPC['uid']);
	$credits = array_keys($creditnames);
	$count = 5 - count($creditnames);
	for($i = $count; $i > 0; $i--) {
		$creditnames[] = array('title' => '***');
	}
	$type = intval($_GPC['type']);
	$starttime = strtotime('-7 day');
	$endtime = strtotime('7 day');
	if($type == 1) {
		$starttime = strtotime(date('Y-m-d'));
		$endtime = TIMESTAMP;
	} elseif($type == -1) {
		$starttime = strtotime('-1 day');
		$endtime = strtotime(date('Y-m-d'));
	} else{
		$starttime = strtotime($_GPC['datelimit']['start']);
		$endtime = strtotime($_GPC['datelimit']['end']) + 86399;
	}
	if(!empty($credits)) {
		$data = array();
		foreach($credits as $li) {
			$data[$li]['add'] = round(pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('mc_credits_record') . ' WHERE uniacid = :id AND uid = :uid AND createtime > :start AND createtime < :end AND credittype = :type AND num > 0', array(':id' => $_W['uniacid'], ':uid' => $uid, ':start' => $starttime, ':end' => $endtime, ':type' => $li)),2);
			$data[$li]['del'] = abs(round(pdo_fetchcolumn('SELECT SUM(num) FROM ' . tablename('mc_credits_record') . ' WHERE uniacid = :id AND uid = :uid AND createtime > :start AND createtime < :end AND credittype = :type AND num < 0', array(':id' => $_W['uniacid'], ':uid' => $uid, ':start' => $starttime, ':end' => $endtime, ':type' => $li)),2));
			$data[$li]['end'] = $data[$key]['add'] - $data[$key]['del'];
		}
	}
	template('mc/credit_record');
	exit();
}

if($do == 'downloadExcel'){
	require_once('../framework/library/phpexcel/PHPExcel.php');
	$params = array(':uniacid' => $_W['uniacid']);
	$where = "uniacid = :uniacid";
	$objPHPExcel = new PHPExcel();
	$creditList = pdo_fetchall("SELECT id,uid, credittype, num, bonus,operator, createtime, remark FROM "
			 . tablename('mc_credits_record') . " WHERE ".$where,$params); 
	
	$creditListToPage = array();
		foreach ($creditList as $Key => $Index) {
			$creditListToPage[$Key]['id'] = $Index['id'];
			$creditListToPage[$Key]['uid'] = $Index['uid'];
			if($Index['credittype'] == 'credit1'){
				$creditListToPage[$Key]['credittype'] = '红包';
			}else{
				$creditListToPage[$Key]['credittype'] = '余额';
			}
			$creditListToPage[$Key]['num'] = $Index['num'];
			$creditListToPage[$Key]['bonus'] = $Index['bonus'];
			if($Index['uid'] == $Index['operator']){
				$creditListToPage[$Key]['operatorType'] .= "用户";
			} else {
				$creditListToPage[$Key]['operatorType'] .= "管理员";
			}
			
			$creditListToPage[$Key]['createtime'] = $Index['createtime'];
			if(stripos($Index['remark'],'credit1') == true){
				$creditListToPage[$Key]['remark'] = str_replace('credit1', '红包', $Index['remark']);
			} else if(stripos($Index['remark'],'credit2') == true){
				$creditListToPage[$Key]['remark'] = str_replace('credit2', '余额', $Index['remark']);
			} else{
				$creditListToPage[$Key]['remark'] = $Index['remark'];
			}
		}

		//var_dump($creditListToPage);
	$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', '编号')
					->setCellValue('B1', '会员ID')
					->setCellValue('C1', '金额类型')
					->setCellValue('D1', '余额')
					->setCellValue('E1', '奖金')
					->setCellValue('F1', '操作人')
					->setCellValue('G1', '创建时间')
					->setCellValue('H1', '备注');

	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(80);

	foreach ($creditListToPage as $key => $val) {
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.($key+2),$val['id'])		//编号
					->setCellValue('B'.($key+2), $val['uid'])								//会员ID
					->setCellValue('C'.($key+2), $val['credittype'])							//金额类型
					->setCellValue('D'.($key+2), $val['num'])							//余额
					->setCellValue('E'.($key+2), ' '.$val['bonus'])							//奖金
					->setCellValue('F'.($key+2), ' '.$val['operatorType'])									//操作人
					->setCellValue('G'.($key+2), ' '.$val['createtime'])									//创建时间
					->setCellValue('H'.($key+2), ' '.$val['remark']);							//备注
	}

	$objPHPExcel->getActiveSheet()->setTitle('积分列表');
	$objPHPExcel->setActiveSheetIndex(0);
	$date = date('Y-m-d');
	ob_end_clean();
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$date.'.xls"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}

template('mc/creditmanage');
