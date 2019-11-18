<?php
/**
 * 用户管理地址
 * shopping.address.mod.php
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 注册用户和运营用户的openid 为正常 uid
 * @param uid
 */
function getUserDefaultAddressId2($uid = '',$type = true){
	global $_W;
	if($uid == ''){
		return false;
	}
	$condition = ' WHERE isdefault = 1 ';
	if($type){
		$condition .= ' and openid = :openid AND weid = :weid';
	}else{
		$condition = ' ORDER BY RAND() ';
	}
	$addressid =  pdo_fetchcolumn("SELECT id FROM " . tablename('shopping_address') . "  {$condition}",
							array(':weid'=>$_W['uniacid'], ':openid' => $uid));

	if($addressid){
		return $addressid;
	}
	return getUserDefaultAddressId($uid, false);
}

function getUserDefaultAddress($uid){
	return pdo_fetchall("SELECT * FROM " . tablename('shopping_address') . " WHERE uid=:uid", array(':uid'=>$uid));
}

/**
 * 根据用户ID获取地址信息
 * PS.因为限制了每个用户添加的收货地址信息，直接拉取全部
 * @param intval $uid
 */
function Address_getUserAddressListByUid($uid){
	$condition = 'uid=:uid';
	$params = array(':uid'=>$uid);
	$list = pdo_fetchall("SELECT * FROM ".tablename('shopping_address')." 
						  WHERE $condition", $params);
	$total = pdo_fetchcolumn("SELECT COUNT(id)  FROM ".tablename('shopping_address')." WHERE $condition", $params);
	return array('list' => $list, 'total' => $total);
}


/**
 * 保存用户信息
 */
function Address_saveUser($id = 0){
	global $_W, $_GPC;

	if(!empty($_GPC['realname']) && !empty($_GPC['mobile']) && !empty($_GPC['province']) && !empty($_GPC['city']) && !empty($_GPC['area']) && !empty($_W['fans']['from_user'])){
		$data = array(
			'weid' => $_W['uniacid'],
			'openid' => $_W['fans']['from_user'],
			'realname' => $_GPC['realname'],
			'mobile' => $_GPC['mobile'],
			'province' => $_GPC['province'],
			'city' => $_GPC['city'],
			'area' => $_GPC['area'],
			'address' => $_GPC['address'],
			'uid' => $_W['member']['uid']
		);
		
		if (!empty($id)) {
			unset($data['weid']);
			unset($data['openid']);
			return pdo_update('shopping_address', $data, array('id' => $id));
		} else {
			$count = pdo_fetchcolumn('select count(id) from '. tablename('shopping_address').' where openid = :openid', array(':openid'=>$_W['fans']['from_user']));
			if($count == 0){
				$data['isdefault'] = 1;
			}
			pdo_insert('shopping_address', $data);
			return pdo_insertid();
		}
		return true;
	}
	return false;
}

/**
 * 获取详细地址
 */
function Address_getDetailById($id){
	global $_W, $_GPC;
	return pdo_fetch("SELECT  id, realname, mobile, province, city, area, address FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $id));
}

/**
 * 用户地址列表
 *
 */
function Address_getList(){
	global $_W, $_GPC;
	return pdo_fetchall("SELECT * FROM " . tablename('shopping_address') . " 
						 WHERE deleted = 0 and openid = :openid order by isdefault desc", array(':openid' => $_W['fans']['from_user']));
}

/**
 * 设置默认地址
 * @param int id 地址ID
 */
function Address_setDefault(){
	global $_W, $_GPC;
	$id = intval($_GPC['id']);
	$res = pdo_fetch("select isdefault from " . tablename('shopping_address') . " 
								  where id= :id and weid= :weid and openid= :openid", 
								  array(':id' => $id, ':weid' => $_W['uniacid'], ':openid' => $_W['fans']['from_user']));
	if(!empty($res) && empty($res['isdefault'])){
		pdo_update('shopping_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user'], 'isdefault' => 1));
		pdo_update('shopping_address', array('isdefault' => 1), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user'], 'id' => $id));
	}
	message(1, '', 'ajax');
}

/**
 * 获取地址信息
 * @param addrid
 *
 */
function Address_getInfoToManage(){
	global $_W, $_GPC;
	$psize = 20;
	$pindex = max(1, intval($_GPC['page']));
	$addrid = intval($_GPC['addrid']);
	$uid = intval($_GPC['uid']);
	$nickname = trim($_GPC['nickname']);
	
	$condition = ' weid = :uniacid';
	$pars = array(':uniacid' => $_W['uniacid']);
	if($addrid){
		$condition .= ' and id = :id ';
		$pars[':id'] = $addrid;
	}
	if($uid){
		$openid = pdo_fetchcolumn('select openid from '.tablename('mc_mapping_fans').' where uid = :uid', array(':uid' => $uid));
		$condition .= ' and (openid = :openid or openid = "'.$uid.'")';
		$pars[':openid'] = $openid;
	}
	if(!empty($nickname)){
		$condition .= " and realname LIKE '%{$nickname}%'";
	}

	$list = pdo_fetchall("SELECT id, openid, realname, mobile, province, city, area, address, isdefault, deleted 
						  FROM ".tablename('shopping_address')." 
						  WHERE $condition
						  LIMIT ".($pindex - 1) * $psize.','.$psize, $pars);

	$total = pdo_fetchcolumn("SELECT COUNT(*)  FROM ".tablename('shopping_address')." WHERE $condition", $pars);
	$pager = pagination($total, $pindex, $psize);
	return array('list' => $list, 'total' => $total, 'pager' => $pager);
}