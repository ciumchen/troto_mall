<?php
/**
 * load()->model('core.paylog');
 * 付款日志
 *
 */

/**
 * 添加付款日志
 *
 */
function Paylog_handle($record){
	if(!empty($record)){
		$log = pdo_fetch('select plid from '.tablename('core_paylog').' where tid = :tid', array(':tid' => $record['tid']));
		if(!empty($log)){
			pdo_update('core_paylog', $record, array('plid' => $log['plid']));
			return true;
		}else{
			if(pdo_insert('core_paylog', $record)) {
				$cpid = pdo_insertid();
				return $cpid;
			}
		}
	}
	return false;
}