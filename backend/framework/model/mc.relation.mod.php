<?php
/**
 * 用户关系链记录
 * load()->model('mc.relation');
 *
 */

/**
 * 获取上家的关系链
 * @return {uid1 : 上家, uid2 : 上家+, uid3 : 上家++}
 */
function relation_getPid($pid){
	$relation = getPid($pid, 3);
	$insert = array();
	$insert['uid1'] = $pid ? $pid : 0;
	$insert['uid2'] = $relation[1] ? $relation[1] : 0;
	$insert['uid3'] = $relation[2] ? $relation[2] : 0;
	return $insert;
}

/**
 * 添加用户关系链
 * @param int uid 用户UID
 * @param int pid 上家ID
 */
function relation_insert($uid, $pid){
	global $_W;
	$insert = relation_getPid($pid, 3);
	$insert['uid'] = $uid;
	pdo_insert('mc_relation',$insert);
	return pdo_insertid();
}

/**
 * 没有上家的情况下更新关系链
 * @param int $uid 
 * @param int $pid
 * @param bool $state 跳过第一层并执行
 */
function relation_update($uid, $pid, $state = true){
	global $_W;
	# 更新我的上家
	$relation = relation_getPid($pid, 3);		# {uid1, uid2 , uid3}
	if($relation['uid1'] == 0){
		# 上家为 0 返回 false		
		return false;
	}
	if($state){
		# 修改用户上家ID，并修改的时候多一层 uid1 条件作判断
		$res = pdo_update('mc_relation', $relation, array('uid' => $uid, 'uid1' => 0));
	}else{
		$res = true;		
	}

	# 更新我的下家的上家
	$relation = array_rebound($relation);		# {uid2 , uid3}
	if($res && $relation){
		$res = pdo_update('mc_relation', $relation, array('uid1' => $uid));
		# 更新我的下家的上家上上家
		$relation = array_rebound($relation);	# {uid3}
		if($res && $relation){
			$res = pdo_update('mc_relation', $relation, array('uid2' => $uid));
		}
	}
	return true;
}

/**
 * 查看关系链记录
 * 
 */
function relation_check($uid, $field = 'uid'){
	return pdo_fetchcolumn('SELECT count(fid) FROM ims_mc_relation WHERE '.$field.' = :uid', array(
								':uid' => $uid 
								)
							);
}

/**
 * 关系链处理(添加新用户或者修改关系链时调用)
 * @param int $uid 
 * @param int $pid
 */
function relation_handle($uid, $pid){
	# 校验上家ID存在{新增，修改}
	# 校验上家ID不存在{新增}
	if($uid == $pid || $uid == 0)
		return false;

	// echo '# fix 上家存在，执行逻辑代码<br>';
	if($pid != 0){
		// echo "# $uid, $pid<br>";
		if(QueryPid($uid, $pid)){
			# 检查第一层关系链是否存在，存在就是“已有用户”，但是避免数据漏洞，我们会在“新用户处理”上检查第二层关系链是否存在
			if(relation_check($uid)){
				# echo '# upd 已存在用户，只需改用户关系链<br>';
				return relation_update($uid, $pid);
			}else{
				# echo '# add 新用户，只需加用户关系链<br>';
				$res = relation_insert($uid, $pid);
				# 检查 uid1 是否存在，存在 relation_update 执行跳转第一层关系链修改，直接执行第二层关系链修改
				if($res && relation_check($uid, 'uid1')){
					# echo '# 执行第二层<br>';
					return relation_update($uid, $pid, false);
				}
				# 算是坑了。。
				return true;
			}
			# echo '# 运行失败！<br>';
		}
		# echo '# 该上家存在我的关系链<br>';
	}else{
		// echo '# add 新用户，只需加用户关系链<br>';
		return relation_insert($uid, $pid);
	}
	return false;
}

/**
 * 获取我的所有下家
 *
 */
function relation_getMyrelation($uid){
	$res = array(1 => 0, 2 => 0, 3 => 0);
	$res[1] = pdo_fetchcolumn('select count(*) from ims_mc_relation where uid1 = :uid', array(':uid'=>$uid));
	$res[2] = pdo_fetchcolumn('select count(*) from ims_mc_relation where uid2 = :uid', array(':uid'=>$uid));
	$res[3] = pdo_fetchcolumn('select count(*) from ims_mc_relation where uid3 = :uid', array(':uid'=>$uid));
	return $res;
}
// load()->model('mc.relation');
// $uid = 1;
// $pid = 0;
// if($_W['member']['pid'] == 0){
// 	if(relation_handle($uid, $pid)){
// 		$res = pdo_update('mc_members', array('pid' => $pid), array('uid' => $uid));
// 		pre($res);
// 	}
// }