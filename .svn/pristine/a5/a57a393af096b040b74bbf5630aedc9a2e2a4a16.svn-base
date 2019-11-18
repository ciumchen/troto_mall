<?php
defined('IN_IA') or exit('Access Denied');

$ms = array();
$ms['platform'][] =  array(
	'title' => '基本功能',
	'items' => array(
		array(
			'title' => '文字回复',
			'url' => url('platform/reply', array('m' => 'basic')),
			'append' => array(
				'title' => '<i class="fa fa-plus"></i>', 
				'url' => url('platform/reply/post', array('m' => 'basic'))
			)
		),
		array(
			'title' => '图文回复',
			'url' => url('platform/reply', array('m' => 'news')),
			'append' => array(
				'title' => '<i class="fa fa-plus"></i>', 
				'url' => url('platform/reply/post', array('m' => 'news')),
			),
		),
		array(
			'title' => '音乐回复',
			'url' => url('platform/reply', array('m' => 'music')),
			'append' => array(
				'title' => '<i class="fa fa-plus"></i>', 
				'url' => url('platform/reply/post', array('m' => 'music'))
			),
		),
		array(
			'title' => '图片回复',
			'url' => url('platform/reply', array('m' => 'images')),
			'append' => array(
				'title' => '<i class="fa fa-plus"></i>',
				'url' => url('platform/reply/post', array('m' => 'images'))
			),
		),
		array(
			'title' => '语音回复',
			'url' => url('platform/reply', array('m' => 'voice')),
			'append' => array(
				'title' => '<i class="fa fa-plus"></i>',
				'url' => url('platform/reply/post', array('m' => 'voice'))
			),
		),
		array(
			'title' => '视频回复',
			'url' => url('platform/reply', array('m' => 'video')),
			'append' => array(
				'title' => '<i class="fa fa-plus"></i>',
				'url' => url('platform/reply/post', array('m' => 'video'))
			),
		),
		array(
			'title' => '自定义接口回复',
			'url' => url('platform/reply', array('m' => 'userapi')),
			'append' => array(
				'title' => '<i class="fa fa-plus"></i>', 
				'url' => url('platform/reply/post', array('m' => 'userapi')),
			),
		),
	)
);
$ms['platform'][] =  array(
	'title' => '高级功能',
	'items' => array(
		array('title' => '常用服务接入', 	'url' => url('platform/service/switch')),
		array('title' => '自定义菜单', 	'url' => url('platform/menu')),
		array('title' => '特殊回复', 		'url' => url('platform/special')),
		array('title' => '二维码管理', 	'url' => url('platform/qr')),
		array('title' => '多客服接入', 	'url' => url('platform/reply', array('m' => 'custom'))),
		array('title' => '长链接二维码', 	'url' => url('platform/url2qr'))
	)
);
$ms['platform'][] =  array(
	'title' => '数据统计',
	'items' => array(
		array('title' => '聊天记录', 			'url' => url('platform/stat/history')),
		array('title' => '回复规则使用情况', 	'url' => url('platform/stat/rule')),
		array('title' => '关键字命中情况', 	'url' => url('platform/stat/keyword')),
		array('title' => '参数', 			'url' => url('platform/stat/setting'))
	)
);
if($_W['usability']['hidden'] == 1 || $_W['usability']['developer'] == $_W['user']['uid']){
	$ms['site'][] =  array(
		'title' => '风格管理',
		'items' => array(
			array('title' => '微站模板管理', 		'url' => url('site/style/template')),
			array('title' => '模块扩展模板说明', 'url' => url('site/style/module')),
		)
	);
	$ms['site'][] =  array(
			'title' => '微站管理',
			'items' => array(
				array('title' => '微站管理',		'url' => url('site/multi/display')),
				array('title' => '添加微站',		'url' => url('site/multi/post')),
			)
	);
	$ms['site'][] =  array(
		'title' => '导航及菜单',
		'items' => array(
			array('title' => '微站首页导航图标', 	'url' => url('site/nav/home')),
			array('title' => '个人中心功能条目', 	'url' => url('site/nav/profile')),
			array('title' => '快捷菜单',			'url' => url('site/nav/shortcut')),
		)
	);
}

//微商城左侧菜单
$ms['shopping'][] = array(
	'title' => '商城功能',
	'items' => array(
		array('title' => '订单管理', 	'url' => url('site/entry/order', array('m'=>'ewei_shopping'))),
		array('title' => '商品管理', 	'url' => url('site/entry/goods', array('m'=>'ewei_shopping'))),
		array('title' => '分类管理', 	'url' => url('site/entry/category', array('m'=>'ewei_shopping'))),
		array('title' => '销售专题管理', 	'url' => url('shopping/topic', array('m'=>'ewei_shopping'))),
		array('title' => '广告幻灯片管理', 	'url' => url('shopping/ads', array('m'=>'ewei_shopping'))),
	)
);
$ms['shopping'][] = array(
	'title' => '运营及活动',
	'items' => array(
		array('title' => '运营管理', 	'url' => url('site/entry/operations', array('m'=>'ewei_shopping'))),
		array('title' => '操作人员列表 <sup style="color:#f00;font-size:8px;">待</sup>', 	'url' => url('profile/worker', array('m'=>'ewei_shopping'))),
		array('title' => '佣金提现审核', 	'url' => url('shopping/reward/manage', array('m'=>'ewei_shopping'))),
		array('title' => '优惠券类型', 	'url' => url('shopping/coupon/type', array('m'=>'ewei_shopping'))),
		array('title' => '优惠券管理', 	'url' => url('shopping/coupon/manage', array('m'=>'ewei_shopping'))),
	)
);
$ms['shopping'][] = array(
	'title' => '业务支撑配置',
	'items' => array(
		array('title' => '品牌管理',	'url' => url('site/entry/branding', array('m'=>'ewei_shopping'))),
		array('title' => '供应商管理', 	'url' => url('ma/suppliers/suppliers')),
		array('title' => '仓库管理',	'url' => url('site/entry/warehouse', array('m'=>'ewei_shopping'))),
		array('title' => '物流管理',	'url' => url('site/entry/express', array('m'=>'ewei_shopping'))),
		array('title' => '发货管理',	'url' => url('site/entry/orderexpress', array('m'=>'ewei_shopping'))),
		array('title' => '配送方式管理','url' => url('site/entry/dispatch', array('m'=>'ewei_shopping'))),
	)
);

$ms['mc'][] = array(
	'title' => '微信用户管理',
	'items' => array(
		array('title' => '微信分组', 		'url' => url('mc/fangroup')),
		array('title' => '微信用户信息', 	'url' => url('mc/fans')),
	)
);
/*
$ms['community'][] = array(
	'title' => '社区管理',
	'items' => array(
		array('title' => '物业申请', 		'url' => url('community/application')),
		array('title' => '小区群', 			'url' => url('community/group')),
		array('title' => '小区专家', 		'url' => url('community/expert')),
		array('title' => '小区商家', 		'url' => url('community/vendors')),
		array('title' => '小区服务类型',	'url' => url('community/sercate')),
		array('title' => '客服专员',		'url' => url('community/customservice')),
	)
);
*/
$ms['mc'][] = array(
	'title' => '会员中心',
	'items' => array(
		array('title' => '会员运营', 			'url' => url('mc/huiyuan')),
		array('title' => '会员组', 				'url' => url('mc/group')),
		array('title' => '运营管理', 			'url' => url('mc/operations')),
		array('title' => '会员签到管理',		'url' => url('mc/sign')),
		array('title' => '会员积分管理', 		'url' => url('mc/creditmanage')),
		array('title' => '会员字段管理', 		'url' => url('mc/fields')),
		array('title' => '会员中心访问入口', 	'url' => url('platform/cover/mc')),
	)
);

if($_W['usability']['hidden'] == 1 || $_W['usability']['developer'] == $_W['user']['uid']){
	$ms['mc'][] = array(
		'title' => '会员卡管理',
		'items' => array(
			array('title' => '会员卡访问入口', 	'url' => url('platform/cover/card')),
			array('title' => '会员卡管理', 	'url' => url('mc/card')),
			array('title' => '商家设置',	'url' =>url('mc/business')),
			array('title' => '操作店员管理', 		'url' => url('activity/offline'))
		)
	);

	$ms['mc'][] = array(
		'title' => '积分兑换',
		'items' => array(
			array('title' => '折扣券', 		'url' => url('activity/coupon')),
			array('title' => '代金券', 		'url' => url('activity/token')),
			array('title' => '真实物品',	'url' => url('activity/goods')),
				)
	);
}
$ms['mc'][] = array(
	'title' => '通知中心',
	'items' => array(
		array('title' => '群发消息&通知', 	'url' => url('mc/broadcast')),
		array('title' => '微信群发', 		'url' => url('mc/mass')),
		array('title' => '通知参数', 		'url' => url('profile/notify')),
	)
);
$ms['mc'][] = array(
	'title' => '记录中心',
	'items' => array(
		array('title' => '充值记录', 	'url' => url('mc/credits')),
		array('title' => '付款记录', 	'url' => url('mc/paylog')),
		array('title' => '提成记录', 	'url' => url('mc/commlog')),
	)
);



if(($_W['user']['power'] & ADMINISTRATOR)){
	$ms['setting'][] = array(
		'title' => '公众号选项',
		'items' => array(
			array('title' => '支付参数', 'url' => url('profile/payment')),
			array('title' => '借用 oAuth 权限', 'url' => url('mc/passport/oauth')),
			array('title' => '借用 JS 分享权限', 'url' => url('profile/jsauth')),
		)
	);
}

$ms['setting'][] = array(
	'title' => '功能组件',
	'items' => array(
		array('title' => '操作人员列表', 	'url' => url('profile/worker', array('m'=>'ewei_shopping'))),
		array('title' => '管理操作人员', 	'url' => url('profile/worker')),
		array('title' => '幻灯片设置', 		'url' => url('site/slide')),
		array('title' => '文章管理', 		'url' => url('site/article')),
		array('title' => '用户反馈', 		'url' => url('site/feedback')),
		array('title' => '图片上传', 		'url' => url('site/upload')),
		// array('title' => '分类设置', 			'url' => url('site/category')),
	)
);
$ms['setting'][] = array(
	'title' => '会员及粉丝选项',
	'items' => array(
		array('title' => '积分设置', 'url' => url('mc/credit')),
		array('title' => '注册设置', 'url' => url('mc/passport/passport')),
		array('title' => '粉丝同步设置', 'url' => url('mc/passport/sync')),
		array('title' => 'UC站点整合', 'url' => url('mc/uc')),
	)
);
$ms['setting'][] =  array(
	'title' => '报名',
	'items' => array(
		array('title' => '报名列表', 		'url' => url('site/sign')),
	)
);


if(($_W['user']['power'] & UPD) == 0 || ($_W['user']['power'] & ADMINISTRATOR) == 0){
	foreach ($ms['setting'] as $key => $value) {
		if($value['title'] == '会员及粉丝选项'){
			unset($ms['setting'][$key]['items'][0]);
		}
	}
}
if($_W['usability']['hidden'] == 1 || $_W['usability']['developer'] == $_W['user']['uid']){
	$ms['ext'][] = array(
		'title' => '管理',
		'items' => array(
			array('title' => '扩展功能管理', 'url' => url('profile/module'))
		)
	);
}
$ms['census'][] = array(
	'title' => '图表统计',
	'items' => array(
		array('title' => '用户统计', 			'url' => url('census/count')),
	)
);
return $ms;
