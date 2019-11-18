<?php
/**
 * 货柜管理
 * shopping.cabinet.mod.php *
 */

defined('IN_IA') or exit('Access Denied');

/**
 * 获取货柜
 * 
 */
function Cabinetex($status='')
    {
        global $_W, $_GPC;
        require_once('../framework/library/phpexcel/PHPExcel.php');
        $objPHPExcel = new PHPExcel();

        $condition = '';
        if($status == 1)
        {
            $statusTitle = '维护';
            $condition .= ' status=1';
        } elseif($status == 0)
        {
            $statusTitle = '启用';
            $condition .= ' status=0';
        } elseif($status == -1)
        {
            $statusTitle = '未启用';
            $condition .= ' status=-1';
        } else
        {
            $statusTitle = '全部';
        }

        $sql = 'SELECT *  FROM '.tablename('cabinet').' WHERE  '.$condition;

        $re = pdo_fetchall($sql, array());
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        $total = pdo_fetchcolumn("SELECT count(cabinetid) as totalNum FROM ".tablename('cabinet'));

        if($re)
        {
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '货柜ID')
                ->setCellValue('B1', '货柜名称')
                ->setCellValue('C1', '货柜状态')
                ->setCellValue('D1', '货柜库存')
                ->setCellValue('E1', '纬度')
                ->setCellValue('F1', '经度')
                ->setCellValue('G1', '省份')
                ->setCellValue('H1', '城市')
                ->setCellValue('I1', '区县')
                ->setCellValue('J1', '详细描述')
                ->setCellValue('K1', '详细地址')
                ->setCellValue('L1', '创建时间')
                ->setCellValue('M1', '更新时间');
            //set width
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(16);
            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
            $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(18);
            foreach($re as $key=>$val) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.($key+2),$val['cabinetid'])
                    ->setCellValue('B'.($key+2), $val['name'])
                    ->setCellValue('C'.($key+2), $val['status'])
                    ->setCellValue('D'.($key+2), $val['stock'])
                    ->setCellValue('E'.($key+2), $val['lat'])
                    ->setCellValue('F'.($key+2), $val['lng'])
                    ->setCellValue('G'.($key+2), $val['addr_prov'])
                    ->setCellValue('H'.($key+2), $val['addr_city'])
                    ->setCellValue('I'.($key+2), $val['addr_dist'])
                    ->setCellValue('J'.($key+2), $val['addr_mark'])
                    ->setCellValue('K'.($key+2), $val['address'])
                    ->setCellValue('L'.($key+2), $val['createdt'])
                    ->setCellValue('M'.($key+2), $val['updatedt']);
            }
            $objPHPExcel->getActiveSheet()->setTitle($statusTitle);
            $objPHPExcel->setActiveSheetIndex(0);

            $date = date('ymdHis');
            ob_end_clean();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="cabinet'.$date.'.xls"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');

            exit;
        }
        message('操作失败，没有数据！', referer(), 'error');
    }
