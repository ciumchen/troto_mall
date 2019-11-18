<?php
/**
 * load()->func('WebConst');
 * 自定义状态
 *
 */


function Webconst($status = 0){
	$Webconst = array(
		0 		=> array('成功！', 'ERR_SUCC'),
		200		=> array('成功！', 'ERR_SUCC'),
		-100 	=> array('数据库异常，该操作不做出来！', 'ERR_DB'),		
		-200 	=> array('数据异常', 'ERR_DATA'),
		-201 	=> array('用户不存在！', 'ERR_USER'),
		-210 	=> array('您的金额不足！', 'ERR_MONEY'),
		-220 	=> array('您的红包不足！', 'ERR_RED'),
		-504	=> array('抱歉，该奖品过期了！', 'SIGN_TIMEOUT'),
		-510 	=> array('抱歉，该奖品已兑换完，请重新选择！', 'OUTNUMBER'),
		-501 	=> array('抱歉，您的签到次数不足兑换！', 'ERR_TIMES'),
		-530 	=> array('今日已签到过！', 'ERR_SIGN_TIMES'),
		-600 	=> array('活动不存在,去首页逛逛吧！', 'ERR_REDPACKET'),
		-601 	=> array('活动已关闭活动已关闭！', 'ERR_REDPACKET_STATUS'),
		-610 	=> array('红包数量已抢完', 'ERR_REDPACKET_NUM'),
		-611 	=> array('红包金额已抢完！', 'ERR_REDPACKET_MONEY'),
		-603 	=> array('活动尚未开始，请耐心等待！', 'ERR_REDPACKET_STATE'),
		-604 	=> array('活动已结束，请期待下次活动！', 'ERR_REDPACKET_END'),
		-801 	=> array('商品不存在！', 'ERR_GOODS'),
		-810 	=> array('当前商品不足', 'ERR_GOODS_NUM'),
		-811 	=> array('超过限制的购物数量！', 'ERR_GOODS_LIMIT'),
	);

	return isset($Webconst[$status]) ? $Webconst[$status][0] : '访问异常！';
}
/**
 * 1xx	数据库模块
 * 2xx	用户模块
 * 5xx	签到模块
 *
 */