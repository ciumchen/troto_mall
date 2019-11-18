<?php
/**
 * load()->model('goods.visit');
 * 用户商品搜索&浏览日志（前端逻辑调用）
 * 
 */
defined('IN_IA') or exit('Access Denied');

/**
 * 商品访问日志插入
 * @param str $typeTxt 
 * @param int $type 1.商品详细访问;2.商品搜索;3.商品分类
 */

function goods_addVisitLogInfoMation($typeTxt = "", $type = 1){
	global $_W;

	if(is_string($typeTxt)){		
		# 搜索日志插入
		$searchCode = strip_tags(str_replace(' ','',$typeTxt));
		if(!_is_utf8($searchCode)){  
			# 编码是否为utf8
			$searchCode =  iconv("GBK", "UTF-8", $searchCode);			# 强制转换为utf8
		}
		# 防止sql注入处理 ---
		$preventSqlArr = array(
				'union','select','delete','update','insert'
			);

		$searchTxt = $searchCode;
		for ($i = 0; $i <= count($preventSqlArr); $i++){
			if(@stripos($searchCode,$preventSqlArr[$i]) !== false){
				$searchTxt = str_ireplace($preventSqlArr[$i],'',$searchCode);
				break;
			}
		}
	}else{
		$searchTxt = $typeTxt;
	}

	$data = array();
	$data['uid'] = $_W['member']['uid'];
	$data['openid'] = $_W['fans']['from_user'];
	$data['searchtxt'] = $searchTxt;
	$data['type'] = $type;
	$data['ip'] = getip();
	$data['createdt'] = TIMESTAMP;
	$res = pdo_insert("log_goods_visit", $data);
	if($res)
		return true;
	return false;	
}

function _is_utf8($string) {
	return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
}

function goods_getGoodsDetailsRecord(){
	global $_W,$_GPC;
	if($_W['isajax']){
			$CommodItems = array();
			$Date = intval($_GPC['day']);
			$type = $_GPC['type'];
			$uid = $_GPC['uid'];
			$where = "";
			if(!empty($uid)){
				if(is_numeric($uid)){
					$where = "and uid = {$uid}";
				} else{
					$where = "and openid LIKE '%".$uid."%'";
				}
			}
			//商品详情访问记录
			
			if($type == "Detail"){

			  if($Date == 1) :
				$Result = pdo_fetchall("SELECT `searchtxt`,`createdt` FROM ".tablename("log_goods_visit")."
						 WHERE (type = 1) and FROM_UNIXTIME(`createdt`,'%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where} group by searchtxt"); 
			  else:
			  	$Result = pdo_fetchall("SELECT `searchtxt`,`createdt` FROM ".tablename("log_goods_visit")." 
			  			WHERE (type = 1) and FROM_UNIXTIME(`createdt`,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where} group by searchtxt"); 
			  endif;
				foreach ($Result as $Index => $Details) :
						$title = pdo_fetch("SELECT title FROM ".tablename("shopping_goods")." WHERE (`id` = {$Details['searchtxt']})");

						$CommodityDetails['Detail'][$Index] = $title;
						$CommodityDetails['Detail'][$Index]['createdt'] = $Details['createdt'];
						//$CommodityDetails['heading'][$Index]['ttt'] = $CommodityDetails['heading'][$Index-1]['title'];
						if($Date == 1):
							$CommodityDetails['Detail'][$Index]['count'] = pdo_fetchcolumn("SELECT COUNT(`searchtxt`) FROM ".tablename("log_goods_visit")."
																	 WHERE (`searchtxt` = {$Details['searchtxt']} AND type = 1) and FROM_UNIXTIME(`createdt`,'%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where}");
						else:
							$CommodityDetails['Detail'][$Index]['count'] = pdo_fetchcolumn("SELECT COUNT(`searchtxt`) FROM ".tablename("log_goods_visit")."
																	 WHERE (`searchtxt` = {$Details['searchtxt']} AND type = 1) and FROM_UNIXTIME(`createdt`,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where}");
						endif;
					
						if(@mb_strlen($CommodityDetails['Detail'][$Index]['title'],'UTF-8') >= 6){
							$text = mb_substr($CommodityDetails['Detail'][$Index]['title'],0,6,'UTF-8').'...';
							$CommodityDetails['Detail'][$Index]['title'] = $text;
						}

						$SortList[$Index] = $CommodityDetails['Detail'][$Index]['count'];
				endforeach;
					array_multisort($SortList,SORT_NUMERIC,SORT_DESC,$CommodityDetails['Detail']);

					if(count($CommodityDetails['Detail']) > 10){
						for($k=1;$k<10;$k++){
							$arr['Detail'][$k-1] = $CommodityDetails['Detail'][$k-1];
						}

						if(!empty($arr['Detail'])){
							ajaxReturn($arr);
						}
					}

					if(!empty($CommodityDetails['Detail'])){
						ajaxReturn($CommodityDetails);
					}
					ajaxReturn(array("Detail" => 0));		

			}

			if ($type == "Search"){
			//  商品搜索记录
				if($Date == 1):
					$Result = pdo_fetchall("SELECT `searchtxt`,`createdt` FROM ".tablename("log_goods_visit")." 
								WHERE (type = 2) and FROM_UNIXTIME(`createdt`,'%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where} group by searchtxt");
				else:
					$Result = pdo_fetchall("SELECT `searchtxt`,`createdt` FROM ".tablename("log_goods_visit")." 
								WHERE (type = 2) and FROM_UNIXTIME(`createdt`,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where} group by searchtxt");
				endif;

				foreach ($Result as $Index => $Search) :
					$CommoditySearch['Search'][$Index] = $Search;
					if($Date == 1):
						$CommoditySearch['Search'][$Index]['count'] = pdo_fetchcolumn("SELECT COUNT(`searchtxt`) FROM ".tablename("log_goods_visit")."
															 WHERE `searchtxt` = '{$Search['searchtxt']}' AND type = 2 AND FROM_UNIXTIME(`createdt`,'%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where}");
					else:
						$CommoditySearch['Search'][$Index]['count'] = pdo_fetchcolumn("SELECT COUNT(`searchtxt`) FROM ".tablename("log_goods_visit")."
															 WHERE (`searchtxt` = '{$Search['searchtxt']}' AND type = 2) and FROM_UNIXTIME(`createdt`,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where}");
					endif;

					if(mb_strlen($CommoditySearch['Search'][$Index]['searchtxt'],'UTF-8') >= 6){
						$text = mb_substr($CommoditySearch['Search'][$Index]['searchtxt'],0,6,'UTF-8').'...'; 
						$CommoditySearch['Search'][$Index]['searchtxt'] = $text;
					}
					$SortList[$Index] = $CommoditySearch['Search'][$Index]['count'];
				endforeach;
				array_multisort($SortList,SORT_NUMERIC,SORT_DESC,$CommoditySearch['Search']);

				if(count($CommoditySearch['Search']) > 10){
					for($k=1;$k<3;$k++){
						$arr['Search'][$k-1] = $CommoditySearch['Search'][$k-1];
					}

					if(!empty($arr['Search'])){
						ajaxReturn($arr);
					}
				}

				if(!empty($CommoditySearch['Search'])){
					ajaxReturn($CommoditySearch);
				}
				ajaxReturn(array("Search" => 0));	
			}

			if ($type == "Class" ){
					//   商品分类记录
					if($Date == 1):
						$Result = pdo_fetchall("SELECT `searchtxt`,`createdt` FROM ".tablename("log_goods_visit")." 
									WHERE (type = 3) AND FROM_UNIXTIME(`createdt`,'%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where} group by searchtxt");
					else :
						$Result = pdo_fetchall("SELECT `searchtxt`,`createdt` FROM ".tablename("log_goods_visit")." 
									WHERE (type = 3) AND FROM_UNIXTIME(`createdt`,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where} group by searchtxt");
					endif;

					foreach ($Result as $Index => $Category) :
						$Cate = pdo_fetch("SELECT `name` FROM ".tablename("shopping_category")." WHERE (`id` = {$Category['searchtxt']})");
						$CommodityCategory['Class'][$Index] = $Cate;
						$CommodityCategory['Class'][$Index]['createdt'] = $Category['createdt'];
						if($Date == 1):
							$CommodityCategory['Class'][$Index]['count'] = pdo_fetchcolumn("SELECT COUNT(`searchtxt`) FROM ".tablename("log_goods_visit")."
																 WHERE `searchtxt` = {$Category['searchtxt']} AND type = 3 AND FROM_UNIXTIME(`createdt`,'%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where}");
						else:
						 	$CommodityCategory['Class'][$Index]['count'] = pdo_fetchcolumn("SELECT COUNT(`searchtxt`) FROM ".tablename("log_goods_visit")."
						 										 WHERE `searchtxt` = {$Category['searchtxt']} AND type = 3 AND FROM_UNIXTIME(`createdt`,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL {$Date} DAY) {$where}");
						endif;

						$SortList[$Index] = $CommodityCategory['Class'][$Index]['count'];
					endforeach;

				array_multisort($SortList,SORT_NUMERIC,SORT_DESC,$CommodityCategory['Class']);

				if(count($CommodityCategory['Class']) > 10){
					for($k=1;$k<10;$k++){
						$arr['Class'][$k-1] = $CommoditySearch['Class'][$k-1];
					}

					if(!empty($arr['Class'])){
						ajaxReturn($arr);
					}
				}

				if(!empty($CommodityCategory['Class'])){
					ajaxReturn($CommodityCategory);
				}
				ajaxReturn(array("Class" => 0));
			}

		}
}
function goods_getLogDetailFromList(){
	global $_W, $_GPC;
	$pindex = max(1, intval($_GPC['page']));
	$psize = 10;
	$Item = array();

	if($_GPC['op'] == 'detail'){
		$where = "`type` = 1";
	} else if($_GPC['op'] == 'search'){
		$where = "`type` = 2";
	} else if($_GPC['op'] == 'class'){
		$where = "`type` = 3";
	}

	$Result = pdo_fetchall("SELECT * FROM ".tablename("log_goods_visit")." WHERE {$where} order by createdt desc limit ". ($pindex - 1) * $psize . ',' . $psize);

	foreach ($Result as $Index => $Row) {
		switch ($Row['type']) {
			case '1':
				$Result[$Index]['type_name'] = "商品详情访问";
				$Result[$Index]['info_title'] = pdo_fetch("SELECT unm_b.title FROM ".tablename("log_goods_visit")."
												 as unm_a left join ".tablename("shopping_goods")." as unm_b on unm_a.searchtxt = unm_b.id 
												 WHERE unm_a.searchtxt = ".$Row['searchtxt']." and unm_a.type = ".$Row['type']);
				break;
			case '2':
				$Result[$Index]['type_name'] = "商品搜索访问";
				$Result[$Index]['info_title']['title'] = (mb_strlen($Row['searchtxt'],'UTF-8') >= 7) ? mb_substr($Row['searchtxt'], 0, 7,'UTF-8')."..." : $Row['searchtxt'];
				break;
			case '3':
				$Result[$Index]['type_name'] = "商品分类访问";
				$Result[$Index]['info_title'] = pdo_fetch("SELECT unm_b.name as title FROM ".tablename("log_goods_visit")."
												 as unm_a left join ".tablename("shopping_category")." as unm_b on unm_a.searchtxt = unm_b.id 
												 WHERE unm_a.searchtxt = ".$Row['searchtxt']." and unm_a.type = ".$Row['type']);
				break;
			default:
				unset($Item['list'][$Index]['type_name']);
				break;
		}
	}
	$Item['list'] = $Result;
	$Item['total'] = pdo_fetchcolumn("SELECT COUNT(`id`) FROM ".tablename("log_goods_visit")." WHERE {$where}");
	$Item['pager'] = pagination($Item['total'], $pindex, $psize);

	return $Item;
}

function goods_AloneDateSelectToItems($data = array()){
	global $_GPC;
		if($data != null && is_array($data)){
			$pindex = max(1, intval($_GPC['page']));
			$psize = 10;
			$where = "";
			$returnMsc = array();
			switch ($data['btype']) {
				case 'detail':
					$where = '`type` = 1 and';
					$returnMsc['type_name'] = "商品详情访问";
					$returnMsc['info_mations'] = "访问的商品";
					$info = 'umr_b.title';
					$tablename = "shopping_goods";
					$where_jointype = 'umr_a.type = 1';
					break;
				case 'search':
					$where = '`type` = 2 and';
					$returnMsc['type_name'] = "商品搜索访问";
					$returnMsc['info_mations'] = "搜索的商品";
					$info = false;
					break;
				case 'class':
					$where = '`type` = 3 and';
					$returnMsc['type_name'] = "商品分类访问";
					$returnMsc['info_mations'] = "访问的分类";
					$info = 'umr_b.name as title';
					$tablename = "shopping_category";
					$where_jointype = 'umr_a.type = 3';
					break;

				default:
					$where = "";
					break;
			}

			if($data['Date'] == 1){
				$fromUnixTime = "FROM_UNIXTIME(createdt,'%Y-%m-%d') = DATE_SUB(CURDATE(), INTERVAL {$data['Date']} DAY)";

			} else {
				$fromUnixTime = "FROM_UNIXTIME(createdt,'%Y-%m-%d') >= DATE_SUB(CURDATE(), INTERVAL {$data['Date']} DAY)";
		
			}

			$returnMsc['items'] = pdo_fetchall("SELECT * FROM ".tablename('log_goods_visit')." 
									WHERE {$where} {$fromUnixTime} order by createdt asc limit ". ($pindex - 1) * $psize . ',' . $psize);

			if($info){
				foreach ($returnMsc['items'] as $Index => $Row) {
					$returnMsc['items'][$Index]['info_title'] = pdo_fetch("SELECT {$info} FROM ".tablename('log_goods_visit')." as umr_a left join "
												.tablename($tablename)." as umr_b on umr_a.searchtxt = umr_b.id where {$where_jointype} and umr_a.searchtxt = ".$Row['searchtxt']);
					$returnMsc['items'][$Index]['createdt'] = date('Y-m-d H:i:s',$Row['createdt']);
			
				}
			} else {
				foreach ($returnMsc['items'] as $Index => $Row) {
					$returnMsc['items'][$Index]['searchtxt'] = $Row['searchtxt'];
					$returnMsc['items'][$Index]['createdt'] = date('Y-m-d H:i:s',$Row['createdt']);
			
				}
			}
			if($returnMsc != null){
				$returnMsc['total'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("log_goods_visit")." 
									WHERE {$where} {$fromUnixTime}");
				$returnMsc['pager'] = pagination($returnMsc['total'], $pindex, $psize);
			}

			return $returnMsc;
		} 
}
