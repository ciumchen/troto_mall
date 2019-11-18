<?php
/**
 * 用户管理
 * member.corpteam.mod.php *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 获取用户信息
 * 
 */
function Corpteamex($status=''){
    global $_W, $_GPC;
    require_once('../framework/library/phpexcel/PHPExcel.php');
    $objPHPExcel = new PHPExcel();

    //$corpteam = '';
    if($status == 1){
        $statusTitle = '启用';
         $corpteam .= ' status=1';
    } elseif($status == 0){
         $statusTitle = '未启用';
        $corpteam .= ' status=0';
    } else{
            $statusTitle = '全部';
    }

    $sql = 'SELECT *  FROM '.tablename('members');
    $re = pdo_fetchall($sql, array());
    $pindex = max(1, intval($_GPC['page']));
    $psize = 10;
    $total = pdo_fetchcolumn("SELECT count(uid) as totalNum FROM ".tablename('corpteam'));

    if($re){
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '用户ID')
            ->setCellValue('B1', '用户手机')
            ->setCellValue('C1', '用户名字')
            ->setCellValue('D1', '车牌号')
            ->setCellValue('E1', '用户余额')
            ->setCellValue('F1', '用户状态')
            ->setCellValue('G1', '创建时间')
            ->setCellValue('H1', '更新时间');
        //set width
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
        foreach($re as $key=>$val) {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($key+2),$val['uid'])
                ->setCellValue('B'.($key+2), $val['mobile'])
                ->setCellValue('C'.($key+2), $val['realname'])
                ->setCellValue('D'.($key+2), $val['carsn'])
                ->setCellValue('E'.($key+2), $val['deposit'])
                ->setCellValue('F'.($key+2), $val['status'])
                ->setCellValue('G'.($key+2), $val['createtime'])
                 ->setCellValue('H'.($key+2), $val['updatedt']);
        }
        $title = 'corpteam';
		$objPHPExcel->getActiveSheet()->setTitle($title);
		$objPHPExcel->setActiveSheetIndex(0);
        $date = date('ymdHis');
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $title . $date .'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }
    message('操作失败，没有数据！', referer(), 'error');
}