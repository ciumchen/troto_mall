<?php
/**
 * load()->model('site.registration');
 * 用户信息收集
 */

/**
 * 登记信息插入(用于报名)
 * @param str $username
 * @param str $mobile
 */
function registration_addUserInfo($username, $mobile){
	global $_W;
	$data = array(
			'uniacid' => $_W['uniacid'],
			'uid' => $_W['member']['uid'],
			'fusername' => $username,
			'fmobile' => $mobile,
			'fip' => getip()
		);
	pdo_insert('site_registration', $data);
	return pdo_insertid();
}
