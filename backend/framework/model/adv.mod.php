<?php
/**
 * wayne
 * 幻灯片
 */
defined('IN_IA') or exit('Access Denied');


/**
 * 首页幻灯片
 *
 */
function getAdvInfo(){
	global $_W;
	$advs = pdo_fetchall("SELECT * FROM " . tablename('shopping_adv') . " WHERE enabled = 1 AND weid= :weid ORDER BY displayorder DESC", 
						array(':weid' => $_W['uniacid']));
		foreach ($advs as &$adv) {
			if (!empty($adv['link']) && substr($adv['link'], 0, 5) != 'http:') {
				$adv['link'] = "http://" . $adv['link'];
			}
		}
	return $advs;
}

