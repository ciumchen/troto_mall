<?php
defined('IN_IA') or exit('Access Denied');

$dos = array('display', 'creditExchange');
$do = in_array($do, $dos) ? $do : 'display';

if($do == 'display'){
	load()->model('mc.comm.log');
	$res = getOrderCommList();
} else if ($do=='creditExchange') {
	load()->model('mc.bank.rechange');
	$res = Rechange_getList();
}

load()->func('tpl');
template('mc/commlog');