<?php
/**
 * load()->model('images.reply.expand');
 * 用户个人二维码
 */

/**
 * 获取用户当前的图片
 * @param $uid
 * @param $keytitle 图片标识
 * @param $type 类型，默认 1
 */
function Rexpand_getUserQrcode($uid, $keytitle = '二维码', $type = 1){
	if($uid){
		$pars = array();
		$sql = 'SELECT * FROM ' . tablename('images_reply_expand') . ' WHERE  `uid`=:uid and title = :title and ftype = :type limit 1';
		$pars[':uid'] =$uid;
		$pars[':title'] = $keytitle;
		$pars[':type'] = $type;
		return pdo_fetch($sql, $pars);
	}
	return false;
}

/**
 * 用户二维码添加
 *
 */
function Rexpand_postUserQrcode($data){
	pdo_insert('images_reply_expand', $data);
	return pdo_insertid();
}


function Rexpand_delUserQrcode($id){
	pdo_delete('images_reply_expand', array('fid' => $id));
}