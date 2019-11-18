<?php
/**
 * load()->model('images.reply');
 * 回复规格之图片
 *
 */

function ImgReply_getMediaid($keyword = ''){
	global $_W;
	if(empty($keyword)){
		return false;
	}
	$condition = 'where a.`content` like "'.$keyword.'%" and a.uniacid = :uniacid';
	$pars = array(':uniacid' => $_W['uniacid']);
	$res = pdo_fetch('SELECT b.mediaid, b.pathfile, b.title FROM '.tablename('rule_keyword').' a left join '.tablename('images_reply').' b on a.rid = b.rid '.$condition, $pars);
	return $res;

}