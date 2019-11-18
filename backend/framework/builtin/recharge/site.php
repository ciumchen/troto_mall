<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

class RechargeModuleSite extends WeModuleSite {
	
	public function doMobilePay() {
		global $_W, $_GPC;
		load()->model('shopping.cart');
		$CartTotal = Cart_getCartTotal();
		if (empty($_W['member']['uid'])) {
			checkauth();
		}
		if (checksubmit('submit', true) || !empty($_GPC['ajax'])) {
						$fee = floatval($_GPC['money']);
			if($fee <= 0) {
				message('支付错误, 金额小于0');
			}
			$chargerecord = pdo_fetch("SELECT * FROM ".tablename('mc_credits_recharge')." WHERE uniacid = :uniacid AND uid = :uid AND fee = :fee AND status = '0'", array(
				':uniacid' => $_W['uniacid'],
				':uid' => $_W['member']['uid'],
				':fee' => $fee,
			));
			if (empty($chargerecord)) {
				$chargerecord = array(
					'uid' => $_W['member']['uid'],
					'uniacid' => $_W['uniacid'],
					'tid' => randNumByOrder(5, 2, 9, 'mdH'),
					'fee' => $fee,
					'status' => 0,
					'createtime' => TIMESTAMP,
				);
				if (!pdo_insert('mc_credits_recharge', $chargerecord)) {
					message('创建充值订单失败，请重试！', url('entry', array('m' => 'recharge', 'do' => 'pay')), 'error');
				}
			}
			$params = array(
				'tid' => $chargerecord['tid'],
				'ordersn' => $chargerecord['tid'],
				'title' => '充值余额',
				'fee' => $chargerecord['fee'],
				'user' => $_W['member']['uid'],
				'goodsTitle' => array(array('title'=>'充值余额'))
			);
			$this->pay($params);
		} else {
			$profile = $_W['member'];
			include $this->template('recharge');
		}
	}
	

	public function payResult($params) {
		load()->model('mc');
		$status = pdo_fetchcolumn("SELECT status FROM ".tablename('mc_credits_recharge')." WHERE tid = :tid", array(':tid' => $params['tid']));
		if (empty($status)) {
			$fee = $params['fee'];
			$data = array('status' => $params['result'] == 'success' ? 1 : -1);
						if ($params['type'] == 'wechat') {
				$data['transid'] = $params['tag']['transaction_id'];
				$params['user'] = mc_openid2uid($params['user']);
			}
			pdo_update('mc_credits_recharge', $data, array('tid' => $params['tid']));

			if ($params['result'] == 'success' && $params['from'] == 'notify') {
				$setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
				$credit = $setting['creditbehaviors']['currency'];
				if(empty($credit)) {
					message('站点积分行为参数配置错误,请联系服务商', '', 'error');
				} else {
					$paydata = array('wechat' => '微信', 'alipay' => '支付宝');
					$record[] = $params['user'];
					// $record[] = '用户通过' . $paydata[$params['type']] . '充值' . $fee;
					//======================================================
					//充值赠送
					$bonus = 0;
					$desc = '';
					
					if($fee >= 500 && $fee < 1000){
						$bonus = 50;
					}else if($fee >= 1000 && $fee < 5000){
						$bonus = 100;
					}else if($fee >= 5000 && $fee < 10000){
						$bonus = 500;
					}else if($fee >= 10000){
						$bonus = 1000;
					}
					
					if($bonus > 0){
						$desc = ', 获取 '.$bonus.' 红包';
					}
					$record[] = '用户通过 ' . $paydata[$params['type']] . ' 充值' . $fee . $desc;
					//======================================================
					mc_credit_update($params['user'], $credit, $fee, $record, $bonus);
				}
			}
		}
		if ($params['from'] == 'return') {
			if ($params['result'] == 'success') {
				message('支付成功！', '../../app/' . url('mc/home'), 'success');
			} else {
				message('支付失败！', '../../app/' . url('mc/home'), 'error');
			}
		}
	}
	
	protected function pay($params = array()) {
		global $_W;
		$params['module'] = $this->module['name'];
		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid';
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		$pars[':module'] = $params['module'];
		$pars[':tid'] = $params['tid'];
		$log = pdo_fetch($sql, $pars);
		if(!empty($log) && $log['status'] == '1') {
			message('这个订单已经支付成功, 不需要重复支付.');
		}
		$setting = uni_setting($_W['uniacid'], array('payment', 'creditbehaviors'));
		if(!is_array($setting['payment'])) {
			message('没有有效的支付方式, 请联系网站管理员.');
		}
		$pay = $setting['payment'];
		$pay['credit']['switch'] = false;
		$pay['delivery']['switch'] = false;
		include $this->template('common/paycenter');
	}
}