<?php

defined('IN_IA') or exit('Access Denied');

// load()->model('mc');
/**
 * display: 基础统计
 * orderCount: 订单统计
 * expendCount: 支出统计
 * commCount: 佣金统计
 * incomeCount：收入统计
 */

$dos = array('display', 'orderCount', 'expendCount', 'commCount', 'incomeCount','exchange');
$do = in_array($do, $dos) ? $do : 'display';
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
$pindex = max(1, intval($_GPC['page']));
$psize = 15;
$condition='';
$pars = array();




template('ma/finance');