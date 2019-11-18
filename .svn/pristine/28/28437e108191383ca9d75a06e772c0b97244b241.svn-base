<?php
/**
 * 产品自定义属性
 * load()->model('shopping.goods.option');
 *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 获取产品规格名
 * 
 */
function Option_getOptionByGoodsId($goodsId = 0){
	global $_GPC, $_W;
	return pdo_fetchall("SELECT id, title, thumb, marketprice, price, deduct, stock, weight, specs,comm1, comm2, comm3  
						 FROM " . tablename('shopping_goods_option') . " 
						 WHERE goodsid=:id 
						 ORDER BY id ASC", 
						 array(':id' => $goodsId)
					);
}

/**
 * 保存规格
 *
 */
function Option_saveGoodsOption($id = 0,$spec_items = array()){
	global $_GPC, $_W;
	$totalstocks = 0;
	$option_idss = $_POST['option_ids'];
	$option_specLenth = $_POST['option_specLenth'];
	$len = count($option_specLenth);
	$optionids = array();
	for ($k = 0; $k < $len; $k++) {
		$option_id = "";
		$ids = $option_idss[$k]; 
		$get_option_id = $_GPC['option_id_' . $ids][0];
		$idsarr = explode("_",$ids);		
		$newids = array();
		foreach($idsarr as $key=>$ida){
			foreach($spec_items as $it){
				if($it['get_id'] == $ida){
					$newids[] = $it['id'];
					break;
				}
			}
		}
		$newids = implode("_",$newids);
		$a = array(
			"title" => $_GPC['option_title_' . $ids][0],
			"price" => $_GPC['option_productprice_' . $ids][0],
			"deduct" => $_GPC['option_costprice_' . $ids][0],
			"marketprice" => $_GPC['option_marketprice_' . $ids][0],
			"stock" => $_GPC['option_stock_' . $ids][0],
			"weight" => $_GPC['option_weight_' . $ids][0],
			"goodsid" => $id,
			"specs" => $newids,
			"comm1" => $_GPC['option_comm1_' . $ids][0],
			"comm2" => $_GPC['option_comm2_' . $ids][0],
			"comm3" => $_GPC['option_comm3_' . $ids][0]
		);
		$totalstocks+=$a['stock'];
		if (empty($get_option_id)) {
			pdo_insert("shopping_goods_option", $a);
			$option_id = pdo_insertid();
		} else {
			pdo_update("shopping_goods_option", $a, array('id' => $get_option_id));
			$option_id = $get_option_id;
		}
		$optionids[] = $option_id;
	}

	if (count($optionids) > 0) {
		pdo_query("delete from " . tablename('shopping_goods_option') . " where goodsid=$id and id not in ( " . implode(',', $optionids) . ")");
	}
	else{
		pdo_query("delete from " . tablename('shopping_goods_option') . " where goodsid=$id");
	}
	return $totalstocks;
}

/**
 * execl 表格导出
 * 
 */
function Option_getInfoToExecl(){
	global $_W;
	$sql = "SELECT g.id goodsid, o.id optionid, g.title goodstitle, o.title optiontitle, o.marketprice, o.price, o.comm1, o.comm2, o.comm3, g.weid, g.status 
			FROM ims_shopping_goods_option o left join ims_shopping_goods g on o.goodsid = g.id where g.weid = :weid";
	$re = pdo_fetchall($sql, array(':weid' => $_W['uniacid']));
	if($re){
		require_once('../framework/library/phpexcel/PHPExcel.php');
		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A1', '商品ID')
					->setCellValue('B1', '规格ID')
					->setCellValue('C1', '商品名称')
					->setCellValue('D1', '规格名称')
					->setCellValue('E1', '销售价格')
					->setCellValue('F1', '市场价格')
					->setCellValue('G1', '一级提成')
					->setCellValue('H1', '二级提成')
					->setCellValue('I1', '三级提成')
					->setCellValue('J1', '三级提成');

		//set width  
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);		
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		foreach($re as $key=>$val)
		{
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.($key+2), ' '.$val['goodsid'])								//商品ID
						->setCellValue('B'.($key+2), ' '.$val['optionid'])								//规格ID
						->setCellValue('C'.($key+2), ' '.$val['goodstitle'])							//产品名
						->setCellValue('D'.($key+2), ' '.$val['optiontitle'])							//规格名
						->setCellValue('E'.($key+2), ' '.$val['marketprice'])							//销售价格
						->setCellValue('F'.($key+2), ' '.$val['price'])									//市场价格
						->setCellValue('G'.($key+2), ' '.$val['comm1'])									//一级提成
						->setCellValue('H'.($key+2), ' '.$val['comm2'])									//二级提成
						->setCellValue('I'.($key+2), ' '.$val['comm3'])								//三级提成
						->setCellValue('J'.($key+2), ($val['status'] == 1) ? '上架':'下架');
		}
		$title = '商品规格 Execl';
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);

		$date = date('Y-m-d');
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$title.$date.'.xls"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}else{
		message('操作失败，没有数据！', referer(), 'error');
	}
}

/**
 * 商品表格导入，并修改表格信息
 * @log 记录上传的文件名，operator，时间
 */
function Option_putGoodsOpionByimport(){
	global $_GPC, $_W;
	if(!empty($_FILES['goodsoption']['name']) && !empty($_FILES['goodsoption']['size'])){
		$tmp_file = $_FILES['goodsoption']['tmp_name'];
		$file_types = explode ( ".", $_FILES ['goodsoption']['name']);
	    $file_type = $file_types [count ( $file_types ) - 1];
	     /*判别是不是.xls文件，判别是不是excel文件*/
	    if (strtolower ( $file_type ) != "xls" && strtolower ( $file_type ) != "xlsx"){
	    	message('不是Excel文件，重新上传！', referer(), 'error');
	    }
	    $savePath = '../'.$_W['config']['upload']['attachdir'].'/files/'; 
		$file_name =  'putGoodsOpionByimport'.date('YmdHis'). "." . $file_type;
		$savePath = $savePath . $file_name;
	
	// $savePath = '../attachment/files/putGoodsOpionByimport20150909102026.xls';
	    if(!copy ( $tmp_file, $savePath)){
	    	message('该文件上传失败！', referer(), 'error');
	    }
		if($file_type == 'xls'){
            //如果excel文件后缀名为.xls，导入这个类
            require_once('../framework/library/phpexcel/PHPExcel/Reader/Excel5.php');
            $PHPReader = new PHPExcel_Reader_Excel5();
        }else if($file_type == 'xlsx'){
            //如果excel文件后缀名为.xlsx，导入这下类
            require_once('../framework/library/phpexcel/PHPExcel/Reader/Excel2007.php');
            $PHPReader = new PHPExcel_Reader_Excel2007();
        }
		$PHPExcel = $PHPReader->load($savePath);
        //获取表中的第一个工作表，如果要获取第二个，把0改为1，依次类推
        $currentSheet = $PHPExcel->getSheet(0);
        //获取总列数
        $allColumn = $currentSheet->getHighestColumn();
        //获取总行数
        $allRow = $currentSheet->getHighestRow();
        //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
        $arr = array();
        for($currentRow = 1;$currentRow <= $allRow;$currentRow++){
            //从哪列开始，A表示第一列
            for($currentColumn = 'A';$currentColumn <= $allColumn;$currentColumn++){
                //数据坐标
                $address=$currentColumn.$currentRow;
                //读取到的数据，保存到数组$arr中
                $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();    
            }
        }        
        unset($arr[1]);        
        foreach($arr as $v){
        	$update = array(
        		'marketprice' => floatval($v['E']), 
        		'price' => floatval($v['F']), 
        		'comm1' => floatval($v['G']),
        		'comm2' => floatval($v['H']),
        		'comm3' => floatval($v['I']),
        		'title' => $v['D'],
        		);
        	$condition = array(
        		'id' => intval($v['B']),
        		'goodsid' => intval($v['A']),
        		);
        	pdo_update('shopping_goods_option', $update, $condition);
        }
        message('上传成功，请查看是否修改成功！', referer(), 'success');
	}else{
		message('该文件有异常！', referer(), 'error');
	}
}