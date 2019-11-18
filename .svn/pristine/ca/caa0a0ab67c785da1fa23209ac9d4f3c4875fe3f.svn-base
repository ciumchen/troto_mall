<?php
/**
 * credits.record.mod.php
 * 用户金额日志
 *
 */

/**
 * 获取充值日志
 *
 */
function Credit_getUserCreditRecord(){
	global $_W;
	return pdo_fetchall('SELECT 
							id,num,bonus,createtime 
						  FROM '.tablename('mc_credits_record').
						' WHERE uid = :uid and uniacid = :uniacid and operator = :uid and num > 0 
						  order by createtime desc limit 15', 
				array(':uid' => $_W['member']['uid'], ':uniacid' => $_W['uniacid']));

}