<?php
/**
 * sercate.ctrl.php
 * 社区服务类型
 *	mod
 */

function sercate_getCateToInfoMation(){
	global $_GPC;

	return pdo_fetchall("SELECT * FROM ".tablename("community_service_category")." WHERE 1");
}

function sercate_getAloneCateToInfo($cateid = 0){
	$cateid = !empty($cateid) ? intval($cateid) : 0;

	return pdo_fetch("SELECT * FROM ".tablename("community_service_category")." WHERE sercateid = :cateid",array(":cateid" => $cateid));
}

function sercate_selectCateName(){
	global $_W,$_GPC;
	$sercateid = intval($_GPC['sercateid']);
	if(!$sercateid) return false;

	return pdo_fetchall("SELECT sercateid,title FROM ".tablename("community_service_category")." WHERE parentid = :parentid",array(":parentid"=>$sercateid));
}

