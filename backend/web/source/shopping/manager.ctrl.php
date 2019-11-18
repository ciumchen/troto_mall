<?php

defined('IN_IA') or exit('Access Denied');

// load()->model('mc');
/**
 * display: 基础统计
 * orderCount: 订单统计
 * expendCount: 支出统计
 * commCount: 佣金统计
 * incomeCount：收入统计
 */
$dos = array('display', 'exchange','checkexchange','incomelog','noexchange','exchangedownload');
$do = in_array($do, $dos) ? $do : 'display';
$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$condition =$ptr_title='';
$pars = array();
$type = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$module_type = !empty($_GPC['type']) ? $_GPC['type'] : 'display';
$status = $_GPC['status'] !== null ? $_GPC['status'] : '0';
$ptr_title='';
$module='';
$module_types = '用户管理';

if($do == 'exchange'){
	$module_type = '佣金管理';
	$module = '佣金管理';
	$ptr_title = '佣金审核';

	load()->model('mc.exchange');
	$res = Exchange_getListByStatus($status);
	if($res){
		$list = $res['list'];
		$total = $res['total'];
		$page = $res['page'];
	}
}else if($do == 'incomelog'){
	$module_type = '佣金管理';
	$module = '用户收入历史';
	$userid = $_GPC['userid'];
}

// switch ($do) {
// 	case 'exchange':
// 		/**
// 		 * 兑换
// 		 * $_GPC['op'] = money 分类
// 		 */
// 		if($type == 'money'){
// 			// exit('巴嘎雅路');
// 		}elseif($type == 'display' || $type == 'no'){
// 			$status = '0,-1';
// 			$_GPC['type'] = 'no';
// 		}elseif($type == 'yes'){
// 			$status = 1;
// 		}elseif($type == 'over'){
// 			$status = 2;
// 		}
// 		$module_type = '佣金管理';
// 		$module = '佣金管理';
// 		$ptr_title = '佣金审核';
// 		$condition = ' where a.uid = b.uid and a.uniacid = :uniacid and b.fstate in ('.$status.')';
		
// 		$pars[':uniacid'] = $_W['uniacid'];

// 		$total = pdo_fetchcolumn('select count(1) from ims_mc_members a,ims_mc_bank_rechange b '.$condition, $pars);
// 		$sql = 'select b.fid, a.nickname, b.uid, b.flog, b.fdesc, b.ftime, b.fmoney, b.fstate, b.fchecktime 
// 				from ims_mc_members a,ims_mc_bank_rechange b 
// 				'.$condition.'
// 				order by b.ftime desc LIMIT '.($pindex - 1) * $psize.','.$psize;;
// 		$list = pdo_fetchall($sql, $pars);
// 		$pager = pagination($total, $pindex, $psize);
// 		break;	
// 	case 'checkexchange':
// 		/**
// 		 * desc 	备注
// 		 * id 		兑换ID
// 		 */
// 		$desc = $_GPC['desc'];
// 		$id = $_GPC['id'];
// 		$state = $_GPC['state'];
// 		$operator = $_W['user']['username'];
// 		$rechange = pdo_fetch('select a.fmoney, a.uid, b.credit5, b.credit3 from ims_mc_bank_rechange a, ims_mc_members b where a.fid = :fid and a.uid = b.uid and a.fstate = :state', 
// 			array(':fid'=>$id,':state'=>$state));

// 		if(empty($rechange)){
// 			echo 2;
// 		}else{
// 			if($state == 0){
// 				pdo_update('mc_bank_rechange', array('fdesc' => '操作人:'.$operator.';'.$desc, 'fstate' => 1,'fchecktime'=>date('Y-m-d H:i:s')), array('fid' => $id));		
// 			}elseif($state == 1){
// 				pdo_update('mc_bank_rechange', array('fdesc' => '操作人:'.$operator.';'.$desc, 'fstate' => 2,'fchecktime'=>date('Y-m-d H:i:s')), array('fid' => $id));	
// 				/**
// 				 * credit5 已提现
// 				 * fmoney  审核金额
// 				 */
// 				pdo_update('mc_members', 
// 							array(
// 								'credit5' => floatval($rechange['credit5']) + floatval($rechange['fmoney']),
// 								'credit3' => floatval($rechange['credit3']) - floatval($rechange['fmoney'])
// 									),
// 							array('uid'=>$rechange['uid'])
// 							);
// 			}
// 			echo 1;
// 		}
// 		exit();
// 		break;
// 	case 'noexchange':
// 		$id = $_GPC['id'];
// 		$state = $_GPC['state'];
// 		pdo_update('mc_bank_rechange', array('fstate' => -1,'fchecktime'=>date('Y-m-d H:i:s')), array('fid' => $id));		
// 		echo 1;
// 		exit();
// 		break;
// 	case 'incomelog':
// 		$module_type = '佣金管理';
// 		$module = '用户收入历史';
// 		$ptr_title = $_GPC['nickname'];
// 		$userid = $_GPC['userid'];

// 		$pars[':uid'] = $userid;
		
// 		if($type == 'display'){
// 			$total = pdo_fetchcolumn('select count(1) from ims_mc_comm_log where pid = :pid ', $pars);
// 			$sql = "select a.*,b.nickname from ims_mc_comm_log a,ims_mc_members b where a.pid = :pid and a.uid = b.uid LIMIT ".($pindex - 1) * $psize.','.$psize;;		
// 			$list = pdo_fetchall($sql,$pars);
// 			// echo '<pre>';
// 			if(!empty($list)){
// 				foreach ($list as $key => $value) {
// 					$list[$key]['info'] = pdo_fetchall('select b.title, a.price,a.total,b.comm'.$value['level'].',a.createtime 
// 								from ims_shopping_order_goods a,ims_shopping_goods b 
// 								where a.orderid = :orderid and a.goodsid = b.id',array(':orderid'=>$value['orderid']));
// 				}
// 			}
// 		}else{
// 			$proxys = array();
// 			$proxys[1] = array_column(pdo_fetchall('select uid from ims_mc_relation where uid1 = :uid ', $pars),'uid');
// 			$proxys[2] = array_column(pdo_fetchall('select uid from ims_mc_relation where uid2 = :uid ', $pars),'uid');
// 			$proxys[3] = array_column(pdo_fetchall('select uid from ims_mc_relation where uid3 = :uid ', $pars),'uid');
// 			$orderStatus = array();
// 			$orderComm = array();
			
// 			if(empty($proxys[1])){
// 				return $ret;
// 			}
// 			$status = $_GPC['status'];
// 			;
// 			if($status != null){
// 				if($status == 1){
// 					$condition = ' and b.status >= '.$status;
// 				}else{
// 					$condition = ' and b.status = '.$status;
// 				}
				
// 			}
// 			foreach ($proxys as $k=>$v) {
// 				$downidvalue = implode(',',$v);
// 				if(!empty($downidvalue)){
// 					$sql = "select b.uid,b.`status`, b.price ,c.comm{$k}*a.total money,{$k} level,b.transid,b.cancelgoods from ims_shopping_order_goods a,ims_shopping_order b,ims_shopping_goods c 
// 							where a.orderid = b.id and a.goodsid = c.id and b.uid in (".$downidvalue.") ".$condition;
// 					$temp = pdo_fetchAll($sql);
// 					$orderComm[$k] = $temp;	
// 				}
// 			}
// 		}
// 		foreach($orderComm as $k=>$v){
// 			foreach($v as $key=>$value){
				
// 				$orderComm[$k][$key]['nickname'] = pdo_fetchcolumn('select nickname,uid from ims_mc_members where uid = :uid',array(':uid'=>$orderComm[$k][$key]['uid']));
				
// 			}
// 		}
// 		break;
// 	case 'exchangedownload':
// 		require_once('../framework/library/phpexcel/PHPExcel.php');
// 		$objPHPExcel = new PHPExcel();
// 		$condition = ' where a.uid = b.uid and a.uniacid = :uniacid and b.fstate = 1';
		
// 		$pars[':uniacid'] = $_W['uniacid'];
// 		$sql = 'select a.nickname,b.flog, b.ftime, b.fmoney, b.fstate
// 				from ims_mc_members a,ims_mc_bank_rechange b 
// 				'.$condition.'
// 				order by b.ftime asc ';
// 		$list = pdo_fetchall($sql, $pars);

// 		// Add some data
// 		$objPHPExcel->setActiveSheetIndex(0)
// 					->setCellValue('A1', '用户昵称')
// 					->setCellValue('B1', '兑换账户信息')
// 					->setCellValue('C1', '金额')
// 					->setCellValue('D1', '时间')
// 					->setCellValue('E1', '状态');
// 		// //set width  
// 		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
// 		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
// 		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
// 		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
// 		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
// 		foreach($list as $key=>$val)
// 		{
// 			$objPHPExcel->setActiveSheetIndex(0)
// 						->setCellValue('A'.($key+2), $val['nickname'])
// 						->setCellValue('B'.($key+2), $val['flog'])
// 						->setCellValue('C'.($key+2), $val['fmoney'])
// 						->setCellValue('D'.($key+2), $val['ftime'])
// 						->setCellValue('E'.($key+2), $val['fstate']);
						
// 		}
// 		// Miscellaneous glyphs, UTF-8
		
// 		// Rename worksheet
// 		$objPHPExcel->getActiveSheet()->setTitle('用户提现');


// 		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
// 		$objPHPExcel->setActiveSheetIndex(0);

// 		$date = date('Y-m-d');
// 		// Redirect output to a client’s web browser (Excel5)
// 		ob_end_clean();
// 		header('Content-Type: application/vnd.ms-excel');
// 		header('Content-Disposition: attachment;filename="'.$date.'.xls"');
// 		header('Cache-Control: max-age=0');

// 		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
// 		$objWriter->save('php://output');

// 		exit;
// 	default:
// 		# code...
// 		break;
// }
template('ma/manager');