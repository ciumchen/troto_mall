<?php
/**
 * load()->classs('express');
 * express.class.php            快递查询类
 * @copyright                   
 * @license                    
 * 
 */


class Express {
 
	private $express =array(); //封装了快递名称

	function __construct(){
		// $this->express = $this->getExpress();
	}

	/**
	 * 返回$data array      快递数组
	 * @param $name         快递名称
	 * 
	 * 能达速递-如风达-瑞典邮政-全一快递-全峰快递-全日通-申通快递-顺丰快递-速尔快递-TNT快递-天天快递
	 * 天地华宇-UPS快递-新邦物流-新蛋物流-香港邮政-圆通快递-韵达快递-邮政包裹-优速快递-中通快递)
	 * 中铁快运-宅急送-中邮物流
	 * @param $order        快递的单号
	 * $data['ischeck'] ==1   已经签收
	 * $data['data']        快递实时查询的状态 array
	 */
	public  function getorder($name,$order){

	}

	/**
	 * 采集网页内容的方法
	 */
	private function getcontent($url){
		if(function_exists("file_get_contents")){
		$file_contents = file_get_contents($url);
		}else{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		}
		return $file_contents;
	}

	/**
	 * 获取快递公司以及对应编码
	 * @return array express
	 */
	public function getExpresscom(){
		return array(
			array('com' => 'youzheng', 'title'=>'中国邮政'),
			array('com' => 'yunda', 'title'=>'韵达快运'),
			array('com' => 'yuantong', 'title'=>'圆通速递'),
			array('com' => 'shentong', 'title'=>'申通'),
			array('com' => 'shunfeng', 'title'=>'顺丰'),
			array('com' => 'zhongtong', 'title'=>'中通速递'),
			array('com' => 'yuntongkuaidi', 'title'=>'运通快递'),
			array('com' => 'zhaijisong', 'title'=>'宅急送'),
			array('com' => 'jixianda', 'title'=>'急先达'),
			array('com' => 'aae', 'title'=>'aae全球专递'),
			array('com' => 'anjie', 'title'=>'安捷快递'),
			array('com' => 'anxindakuaixi', 'title'=>'安信达快递'),
			array('com' => 'biaojikuaidi', 'title'=>'彪记快递'),
			array('com' => 'bht', 'title'=>'bht'),
			array('com' => 'baifudongfang', 'title'=>'百福东方国际物流'),
			array('com' => 'coe', 'title'=>'中国东方（COE）'),
			array('com' => 'changyuwuliu', 'title'=>'长宇物流'),
			array('com' => 'datianwuliu', 'title'=>'大田物流'),
			array('com' => 'debangwuliu', 'title'=>'德邦物流'),
			array('com' => 'dhl', 'title'=>'dhl'),
			array('com' => 'dpex', 'title'=>'dpex'),
			array('com' => 'dsukuaidi', 'title'=>'d速快递'),
			array('com' => 'disifang', 'title'=>'递四方'),
			array('com' => 'ems', 'title'=>'ems快递'),
			array('com' => 'fedex', 'title'=>'fedex（国外）'),
			array('com' => 'feikangda', 'title'=>'飞康达物流'),
			array('com' => 'fenghuangkuaidi', 'title'=>'凤凰快递'),
			array('com' => 'feikuaida', 'title'=>'飞快达'),
			array('com' => 'guotongkuaidi', 'title'=>'国通快递'),
			array('com' => 'ganzhongnengda', 'title'=>'港中能达物流'),
			array('com' => 'guangdongyouzhengwuliu', 'title'=>'广东邮政物流'),
			array('com' => 'gongsuda', 'title'=>'共速达'),
			array('com' => 'huitongkuaidi', 'title'=>'汇通快运'),
			array('com' => 'hengluwuliu', 'title'=>'恒路物流'),
			array('com' => 'huaxialongwuliu', 'title'=>'华夏龙物流'),
			array('com' => 'haihongwangsong', 'title'=>'海红'),
			array('com' => 'haiwaihuanqiu', 'title'=>'海外环球'),
			array('com' => 'jiayiwuliu', 'title'=>'佳怡物流'),
			array('com' => 'jinguangsudikuaijian', 'title'=>'京广速递'),
			array('com' => 'jjwl', 'title'=>'佳吉物流'),
			array('com' => 'jymwl', 'title'=>'加运美物流'),
			array('com' => 'jindawuliu', 'title'=>'金大物流'),
			array('com' => 'jialidatong', 'title'=>'嘉里大通'),
			array('com' => 'jykd', 'title'=>'晋越快递'),
			array('com' => 'kuaijiesudi', 'title'=>'快捷速递'),
			array('com' => 'lianb', 'title'=>'联邦快递（国内）'),
			array('com' => 'lianhaowuliu', 'title'=>'联昊通物流'),
			array('com' => 'longbanwuliu', 'title'=>'龙邦物流'),
			array('com' => 'lijisong', 'title'=>'立即送'),
			array('com' => 'lejiedi', 'title'=>'乐捷递'),
			array('com' => 'minghangkuaidi', 'title'=>'民航快递'),
			array('com' => 'meiguokuaidi', 'title'=>'美国快递'),
			array('com' => 'menduimen', 'title'=>'门对门'),
			array('com' => 'ocs', 'title'=>'OCS'),
			array('com' => 'peisihuoyunkuaidi', 'title'=>'配思货运'),
			array('com' => 'quanchenkuaidi', 'title'=>'全晨快递'),
			array('com' => 'quanfengkuaidi', 'title'=>'全峰快递'),
			array('com' => 'quanjitong', 'title'=>'全际通物流'),
			array('com' => 'quanritongkuaidi', 'title'=>'全日通快递'),
			array('com' => 'quanyikuaidi', 'title'=>'全一快递'),
			array('com' => 'rufengda', 'title'=>'如风达'),
			array('com' => 'santaisudi', 'title'=>'三态速递'),
			array('com' => 'shenghuiwuliu', 'title'=>'盛辉物流'),
			array('com' => 'sue', 'title'=>'速尔物流'),
			array('com' => 'shengfeng', 'title'=>'盛丰物流'),
			array('com' => 'saiaodi', 'title'=>'赛澳递'),
			array('com' => 'tiandihuayu', 'title'=>'天地华宇'),
			array('com' => 'tiantian', 'title'=>'天天快递'),
			array('com' => 'tnt', 'title'=>'tnt'),
			array('com' => 'ups', 'title'=>'ups'),
			array('com' => 'wanjiawuliu', 'title'=>'万家物流'),
			array('com' => 'wenjiesudi', 'title'=>'文捷航空速递'),
			array('com' => 'wuyuan', 'title'=>'伍圆'),
			array('com' => 'wxwl', 'title'=>'万象物流'),
			array('com' => 'xinbangwuliu', 'title'=>'新邦物流'),
			array('com' => 'xinfengwuliu', 'title'=>'信丰物流'),
			array('com' => 'yafengsudi', 'title'=>'亚风速递'),
			array('com' => 'yibangwuliu', 'title'=>'一邦速递'),
			array('com' => 'youshuwuliu', 'title'=>'优速物流'),
			array('com' => 'youzhengguonei', 'title'=>'邮政包裹挂号信'),
			array('com' => 'youzhengguoji', 'title'=>'邮政国际包裹挂号信'),
			array('com' => 'yuanchengwuliu', 'title'=>'远成物流'),
			array('com' => 'yuanweifeng', 'title'=>'源伟丰快递'),
			array('com' => 'yuanzhijiecheng', 'title'=>'元智捷诚快递'),
			array('com' => 'yuefengwuliu', 'title'=>'越丰物流'),
			array('com' => 'yad', 'title'=>'源安达'),
			array('com' => 'yinjiesudi', 'title'=>'银捷速递'),
			array('com' => 'zhongtiekuaiyun', 'title'=>'中铁快运'),
			array('com' => 'zhongyouwuliu', 'title'=>'中邮物流'),
			array('com' => 'zhongxinda', 'title'=>'忠信达'),
			array('com' => 'zhimakaimen', 'title'=>'芝麻开门'),
		);
	}

	/**
	 * 解析object成数组的方法
	 * @param $json 输入的object数组
	 * return $data 数组
	 */
	private function json_array($json){
		if($json){
			foreach ((array)$json as $k=>$v){
				$data[$k] = !is_string($v)?$this->json_array($v):$v;
			}
			return $data;
		}
	}

}
