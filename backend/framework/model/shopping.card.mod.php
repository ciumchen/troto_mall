<?php
/**
 * load()->model('shopping.card');
 * 购物卡
 *
 */

/**
 * 获取红包活动
 *
 */
function shoppingcard_infoToManage(){
	global $_GPC, $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$condition = ' a.weid = '.$_W['uniacid'];	
	$res = array();
	$paras = array(':weid' => $_W['uniacid']);
	$condition = " a.weid = :weid";
	$res['param']['cid'] = $_GPC['cid'];
	$res['param']['token'] = $_GPC['token'];
	$res['param']['price'] = $_GPC['price'];
	$res['param']['activation'] = $_GPC['activation'] == '' ? (string) ('') : intval($_GPC['activation']);
	$res['param']['use'] = $_GPC['use'] == '' ? (string) ('') : intval($_GPC['use']);
	$res['param']['uid'] = intval($_GPC['uid']);
	$res['param']['receiptor'] = intval($_GPC['receiptor']);
	$res['param']['activation_token'] = $_GPC['activation_token'] == '' ? (string) ('') : (string) ($_GPC['activation_token']);
	$res['param']['use_token'] = $_GPC['use_token'] == '' ? (string) ('') : (string) ($_GPC['use_token']);
	//搜索功能
	if(!empty($res['param']['cid'])){
		$condition .= " AND a.cid = ".(int) ($_GPC['cid']);
	}

	if(!empty($res['param']['token']) OR $res['param']['token']!=null){
		$condition .= " AND a.token LIKE '%".$_GPC['token']."%'";
	}

	if(!empty($res['param']['price']) OR $res['param']['price'] > 0){
		$condition .= " AND a.price = ".(int) ($_GPC['price']);
	}

	if($res['param']['activation'] >= 0 AND $res['param']['activation'] !== ''){
		$condition .= " AND a.activation = ".(int) ($_GPC['activation']);
	}

	if($res['param']['use']  >= 0 AND $res['param']['use'] !== ''){
		$condition .= " AND a.use = ".(int) ($_GPC['use']);
	}

	if(!empty($res['param']['uid'])){
		$condition .= " AND a.uid = ".(int) ($_GPC['uid']);
	}

	if(!empty($res['param']['receiptor'])){
		$condition .= " AND a.receiptor = ".(int) ($_GPC['receiptor']);
	}

	if(!empty($res['param']['activation_token']) && $res['param']['activation_token'] !== ""){
		$condition .= " AND a.activationtoken LIKE '%".$res['param']['activation_token']."%'";
	}

	if(!empty($res['param']['use_token']) && $res['param']['use_token'] !== ""){
		$condition .= " AND a.usetoken LIKE '%".$res['param']['use_token']."%'";
	}

	 $res['list'] = pdo_fetchall("SELECT 
									a.cid, a.token, a.`price`, a.activation, a.activationtime, a.`use`, a.usetime, a.uid, a.status, 
									b.nickname, a.receiptor
						 		  FROM " . tablename('shopping_card') . 
									" a LEFT JOIN ".tablename('mc_members')." b on a.uid = b.uid
								  WHERE ".$condition." ORDER BY cid DESC LIMIT ". ($pindex - 1) * $psize . ',' . $psize,$paras);
	
	$res['total'] = pdo_fetchcolumn('SELECT COUNT(a.cid) FROM ' . tablename('shopping_card') . " a LEFT JOIN ".tablename('mc_members')." b on a.uid = b.uid WHERE {$condition}",$paras);
	$res['pager'] = pagination($res['total'], $pindex, $psize);

	return $res;
}

/**
 * 添加卡券
 * @param activationtoken 用于激活链接校验用的，安全校验防破解
 * @param usetoken 用于领取链接校验用的，安全校验防破解
 */
function shoppingcard_insertToManage($price=1000){
	global $_W;
	$token = strtoupper(random(32));
	$data = array(
				'token' => $token,
				'price' => $price,
				'activationtoken' => EncryptMd5($token,'1a'),
				'usetoken' => EncryptMd5($token,'2b'),
			);
	pdo_insert('shopping_card', $data);
	return array('id' => pdo_insertid(), 'token' => $token);
}

/**
 * 激活购物卡
 * @param int cid
 * @param int uid
 *
 */
function shoppingcard_activation($cid, $uid){
	if(!empty($cid) && !empty($uid)){
		if(is_numeric($cid)){
			$res = pdo_update('shopping_card', array('activation' => 1, 'activationtime' => TIMESTAMP, 'uid' => $uid), array('cid' => $cid, 'activation' => 0));
		}else{
			$res = pdo_update('shopping_card', array('activation' => 1, 'activationtime' => TIMESTAMP, 'uid' => $uid), array('token' => $cid, 'activation' => 0));
		}
		return $res;
	}
	return false;
}

/**
 * 取消激活购物卡
 * @param int cid
 * @param int uid
 *
 */
function shoppingcard_cancelActivation($cid){
	if(!empty($cid)){
		$res = pdo_update('shopping_card', array('activation' => 0, 'activationtime' => 0, 'uid' => 0), array('cid' => $cid, 'use' => 0));
		return $res;
	}
	return false;
}


/**
 * 前台信息显示
 * @param str tokencard
 */
function shoppingcard_getDetail($where = array()){
	global $_W;
	$condition = ' WHERE a.weid = '.$_W['uniacid'];
	if(isset($where['usetoken'])){
		$condition .= ' AND a.usetoken = :usetoken ';
		$pars = array(':usetoken' => $where['usetoken']);
	}else if(isset($where['activationtoken'])){
		$condition .= ' AND a.activationtoken = :activationtoken ';
		$pars = array(':activationtoken' => $where['activationtoken']);
	}else{
		return false;
	}

	$res = pdo_fetch("SELECT 
						a.cid, a.token, a.`price`, a.activation, a.activationtime, a.`use`, a.usetime, a.uid, 
						b.nickname, a.activationtoken, a.usetoken, a.thumb, a.status, a.receiptor, b.tag
			 		  FROM " . tablename('shopping_card') . " a left join ". tablename('mc_mapping_fans') ." b on a.uid = b.uid ".$condition, $pars);
	if($res){
		$res['display'] = false;	
		if(isset($where['usetoken'])){
			// 领取事件
			$checkuse = EncryptMd5($res['token'],'2b');
			if($checkuse == $where['usetoken']){
				$res['display'] = true;
			}
		}else if(isset($where['activationtoken'])){
			// 激活事件
			$checkactivation = EncryptMd5($res['token'],'1a');
			if($checkactivation == $where['activationtoken']){
				$res['display'] = true;
			}
		}
		if (!empty($res['tag']) && is_string($res['tag'])) {
			if (is_base64($res['tag'])){
				$res['tag'] = @base64_decode($res['tag']);
			}
			if (is_serialized($res['tag'])) {
				$res['tag'] = @iunserializer($res['tag']);
				if($res['tag']['nickname']){
					$res['nickname'] = $res['tag']['nickname'];
				}
			}
			
		}
		if(empty($res['nickname'])){
			$res['nickname'] = pdo_fetchcolumn('select nickname from '.tablename('mc_members').' where uid = :uid', array(':uid' => $res['uid']));
		}
	}
	return $res;
}

/**
 * 导出表格
 * @param int price
 */
function shoppingcard_exportCard($price){
	global $_W;
	$condition = ' WHERE a.weid = '.$_W['uniacid'] .' AND price = :price AND status = 0 AND cid > 6300';
	$pars = array(':price' => $price);
	$res = pdo_fetchall("SELECT 
						a.cid, a.token, a.`price`, a.activationtoken, a.usetoken
			 		  FROM " . tablename('shopping_card') . " a ".$condition.' order by cid asc', $pars);
	return $res;
}

/**
 * 导出表格无限制
 * @param int price
 */
function shoppingcard_exportCard2($price){
	global $_W;
	$condition = ' WHERE a.weid = '.$_W['uniacid'] .' AND price = :price';
	$pars = array(':price' => $price);
	$res = pdo_fetchall("SELECT 
						a.cid, a.token, a.`price`, a.activationtoken, a.usetoken
			 		  FROM " . tablename('shopping_card') . " a ".$condition.' order by cid asc', $pars);
	return $res;
}
/**
 * 用户激活
 * @param int cid
 * @param int uid
 */
function shoppingcard_userActivation($cid, $uid){
	global $_W;
	$res = pdo_update('shopping_card', array(
			'uid' => $uid, 
			'openid' => $_W['fans']['from_user'], 
			'activation' => 1, 
			'activationtime' => TIMESTAMP
		), array(
			'cid' => $cid,
			'status' => 1,
			'activation' => 0,
			'weid' => $_W['uniacid']
			)
		);
	return $res;
}

/**
 * 用户领取金额
 * @param int cid
 * @param int uid 领取人
 */
function shoppingcard_userUse($cid, $getuid, $fee){
	global $_W;
	load()->model('mc');

	$res = pdo_update('shopping_card', array(
			'use' => 1, 
			'usetime' => TIMESTAMP,
			'receiptor' => $getuid 
		), array(
			'cid' => $cid,
			'use' => 0,
			'weid' => $_W['uniacid'],
			'status' => 1,
			'activation' => 1,
		)
	);
	if($res){
		$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
		// 添加余额日志
		$result = mc_credit_update(
						$getuid, $setting['creditbehaviors']['currency'], $fee, 
						array($getuid, '领取礼品券 '.$setting['creditbehaviors']['currency'].':' . $fee.' 元')
						);
		return $result;
		// 返回的状态
		// if (is_error($result)) {
		// 	message($result['message'], '', 'error');
		// }
	}
	return false;
	
}

/**
 * 设置启动状态
 * @param int $price
 */

function shoppingcard_setStatic($cid,$type=false,$unclie='default'){
	if(empty($cid)){
		return false;
	}
	if($unclie == 'start'){
		return pdo_update('shopping_card',array('status' => 1),array('cid' => $cid));
	}elseif($unclie == 'stop'){
		$res = pdo_update('shopping_card',array('uid'=>NULL,'openid'=>NULL,'activation'=>'0','activationtime'=>NULL,'status' => 0),array('cid' => $cid));
		if($res){
			return true;
		}else {
			return false;
		}
	}else{
		if($type){
			$res = pdo_update('shopping_card',array('status' => 1),array('cid' => $cid));
			if($res){
				return true;
			}else {
				return false;
			}
		}
	}
	$res = pdo_update('shopping_card',array('status' => 0),array('cid' => $cid));
	return $res;

}

/**
 * 获取单条卡信息
 * @param global $_GPC
 */
function shoppingcard_onceInfoToManager(){
	global $_GPC,$_W;
	return pdo_fetch("SELECT * FROM ".tablename('shopping_card')." WHERE weid = :weid and cid = :cid"
					,array(':weid'=>$_W['weid'],'cid'=>$_GPC['cid']));
}

/**
 * 获取绑定人姓名
 * @param $int uid
 */
function shoppingcard_getCardUserName($uid){

	return pdo_fetch("SELECT nickname FROM ".tablename('mc_members')." WHERE uid = :uid",array(':uid' => $uid));
}
/**
 * 插入卡表uid和openid信息
 * @param int $uid && int cid
 * @param string $openid
 */
function shoppingcard_setCardUidInfo($cid,$uid){
	global $_W;
	if(!$cid and !$uid) return false;

	$openid = pdo_fetchcolumn("SELECT openid FROM ".tablename("mc_mapping_fans")." 
								WHERE uid = :uid",array(':uid'=>$uid));

	if(empty($openid)){
		$openid = (null);
	}
	return pdo_update('shopping_card', array('uid'=>$uid, 'openid'=>$openid,'activation'=>1,'activationtime'=>TIMESTAMP), array('weid'=>$_W['weid'],'cid'=>$cid));
}

/**
 * 会员中心我的礼品券
 * @param int $tpye 请求类型（个人卡券、领取卡券）
 * @param int $page 分页
 */
function shoppingcard_userList($type){
	global $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 4;
	if($type == 1){
		$condition = ' on b.uid = a.receiptor where a.uid = :uid order by usetime desc, activationtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	}else if($type == 2){
		$condition = ' on a.uid = b.uid where a.receiptor = :uid order by usetime desc, activationtime desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
	}else{
		return false;
	}

	$pars = array(':uid' => $_W['member']['uid']);
	$res = pdo_fetchall('SELECT a.title, a.price, a.activation, a.use, a.receiptor, b.nickname name 
						FROM '.tablename('shopping_card').' a LEFT JOIN '.tablename('mc_members').' b '.$condition, $pars);
	$list = array();
	if($res){
		foreach ($res as $key => $value) {
			$list[$key] = array();
			$list[$key]['title'] = $value['title'] ? $value['title'] : '云吉良品礼品券';
			$list[$key]['name'] = $value['name'] ? $value['name'] : '小吉鹿';
			if($type == 1){
				if($value['use'] && $value['receiptor']){
					$list[$key]['desc'] = '领取人';
				}else if($value['activation']){
					$list[$key]['name'] = '已激活';
					$list[$key]['desc'] = '';
				}
			}else{
				$list[$key]['desc'] = '赠送人';
			}
			$list[$key]['thumb'] = "/addons/ewei_shopping/images/img/scactivation/ticket-record-{$value['price']}.png";
		}
	}
	return $list;
}

/**
 * 会员中心我的礼品券（总数）
 * @param int $tpye 请求类型（个人卡券、领取卡券）
 */
function shoppingcard_getUserCardByType($type){
	global $_W;
	if($type == 1){
		$condition = ' where uid = :uid ';
	}else if($type == 2){
		$condition = ' where receiptor = :uid ' ;
	}else{
		return false;
	}
	$pars = array(':uid' => $_W['member']['uid']);
	return pdo_fetchcolumn('SELECT count(cid)
						FROM '.tablename('shopping_card').$condition, $pars);

}

/**
 * execl 表格导出未激活导出
 * 
 */
function shoppingcard_getInfoToExecl($price){
	global $_W, $_GPC;
	$sql = "SELECT token, price, cid FROM ims_shopping_card where weid = :weid and status = 0 and price = :price";	

	$re = pdo_fetchall($sql, array(':weid' => $_W['uniacid'], ':price' => $price));
	if($re){
		require_once('../framework/library/phpexcel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', 'ID')
					->setCellValue('B1', '编码')
					->setCellValue('C1', '状态');

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
		foreach($re as $key=>$val)
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.($key+2), ' '.$val['cid'])							//激活链接
						->setCellValue('B'.($key+2), substr($val['token'], 0, 16));
		}
		$title = $price.'元 Execl ';
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);

		$date = date('Y-m-d');
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$title.$date.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}else{
		message('操作失败，没有数据！', referer(), 'error');
	}
}

/**
 * 商品表格导入，并修改表格信息
 * @log 记录上传的文件名，operator，时间
 */
function shoppingcard_putByimport(){
	global $_GPC, $_W;
	if(!empty($_FILES['goodsoption']['name']) && !empty($_FILES['goodsoption']['size'])){
		$tmp_file = $_FILES['goodsoption']['tmp_name'];
		$file_types = explode ( ".", $_FILES ['goodsoption']['name']);
	    $file_type = $file_types [count ( $file_types ) - 1];
	     /*判别是不是.xls文件，判别是不是excel文件*/
	    if (strtolower ( $file_type ) != "xls" && strtolower ( $file_type ) != "xlsx"){
	    	message('不是Excel文件，重新上传！', referer(), 'error');
	    }
	    $savePath = '../'.$_W['config']['upload']['attachdir'].'/files/'; 
		$file_name =  'putGoodsOpionByimport'.date('YmdHis'). "." . $file_type;
		$savePath = $savePath . $file_name;
	    if(!copy ( $tmp_file, $savePath)){
	    	message('该文件上传失败！', referer(), 'error');
	    }
		if($file_type == 'xls'){
            //如果excel文件后缀名为.xls，导入这个类
            require_once('../framework/library/phpexcel/PHPExcel/Reader/Excel5.php');
            $PHPReader = new PHPExcel_Reader_Excel5();
        }else if($file_type == 'xlsx'){
            //如果excel文件后缀名为.xlsx，导入这下类
            require_once('../framework/library/phpexcel/PHPExcel/Reader/Excel2007.php');
            $PHPReader = new PHPExcel_Reader_Excel2007();
        }
		$PHPExcel = $PHPReader->load($savePath);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);
        //获取总列数
        $allColumn = $currentSheet->getHighestColumn();
        //获取总行数
        $allRow = $currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        $arr = array();
        for($currentRow = 1;$currentRow <= $allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn = 'A';$currentColumn <= $allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();    
            }
        }        
        unset($arr[1]);        
        foreach($arr as $v){
        	$status = intval($v['C']);
        	if($status == 1){
	        	$update = array(
	        		'status' => $status,
	        		);
	        	$condition = array(
	        		'cid' => intval($v['A']),
	        		);
	        	pdo_update('shopping_card', $update, $condition);
        	}
        }
        message('上传成功，请查看是否修改成功！', referer(), 'success');
	}else{
		message('该文件有异常！', referer(), 'error');
	}
}

function shoppingcard_settingMoney(){
	return array(100, 200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000);
}