<?php
/**
 * activity.goods.mod.php
 * 签到活动
 */

function Sign_saveSignRule(){
	global $_GPC, $_W;
	$actType = intval($_GPC['actType']);
	$type = intval($_GPC['type']);
	$enabled = intval($_GPC['enabled']);
	$pro = is_array($_GPC['proArr']) ? $_GPC['proArr'] : false;
	$cou = is_array($_GPC['couArr']) ? $_GPC['couArr'] : false;
	$insert = array();	
	if(!empty($pro)){
		foreach($pro as $value){
			$rule = $value[2];
			if(!empty($rule)){
				foreach($rule as $k=>$v){
					$insert[] = array(
							'awardid' => $k,
							'awardtitle' => $v,
							'actiontype' => $actType,
							'exchangetype' => $type,
							'exchangevalue' => intval($value[0]),
							'total' => intval($value[1]),
							'createtime' => TIMESTAMP,
							'status' => $enabled,
							'uniacid' => $_W['uniacid']
						);
				}
			}
		}
	}
	if(!empty($cou)){
		foreach($cou as $value){
			if(!empty($value)){
				$insert[] = array(
						'awardid' => 0,
						'awardtitle' => $value[2],
						'actiontype' => $actType,
						'exchangetype' => $type,
						'exchangevalue' =>  intval($value[0]),
						'awardvalue' => floatval($value[3]),
						'total' => intval($value[1]),
						'createtime' => TIMESTAMP,
						'status' => $enabled,
						'uniacid' => $_W['uniacid']
					);
			}
		}
	}
	if(!empty($insert)){
		foreach($insert as $v){
			pdo_insert('activity_goods', $v);
		}
	}
}

function Sign_getSignAwardRule(){
	global $_W;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 15;
	$list = pdo_fetchall('SELECT a.awardtitle,a.awardid, a.awardvalue, a.actiontype, a.exchangetype, a.exchangevalue, a.total, a.exchangetimes, a.createtime, a.`status` 
						  FROM ims_activity_goods a where a.uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	if(empty($list)){
		return false;
	}
	$total = pdo_fetchcolumn('SELECT count(a.fid) FROM ims_activity_goods a where a.uniacid = :uniacid', array(':uniacid' => $_W['uniacid']));
	$pager = pagination($total, $pindex, $psize);
	return array('list' => $list, 'total' => $total, 'pager' => $pager);
}

