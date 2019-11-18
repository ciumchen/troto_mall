<?php
/**
 * 微商城功能主文件
 */
defined('IN_IA') or exit('Access Denied');
session_start();
include 'model.php';
class Ewei_shoppingModuleSite extends WeModuleSite {

    const LOGISTICS_NOTICE_TEMPLATE = 'aKNa1b5I1MVIfiwfZVosaypCJdexoVyIuBa-tovE9nA';

    public function doWebCategory() {
        global $_GPC, $_W, $_PRICE;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            if (!empty($_GPC['displayorder'])) {
                foreach ($_GPC['displayorder'] as $id => $displayorder) {
                    pdo_update('shopping_category', array('displayorder' => $displayorder), array('id' => $id));
                }
                message('分类排序更新成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            $children = array();
            $category = pdo_fetchall("SELECT * FROM " . tablename('shopping_category') . " WHERE weid = '{$_W['uniacid']}' ORDER BY parentid ASC, displayorder DESC");
            foreach ($category as $index => $row) {
                if (!empty($row['parentid'])) {
                    $children[$row['parentid']][] = $row;
                    unset($category[$index]);
                }
            }

            include $this->template('category');
        } elseif ($operation == 'post') {
            $parentid = intval($_GPC['parentid']);
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $category = pdo_fetch("SELECT * FROM " . tablename('shopping_category') . " WHERE id = '$id'");
            } else {
                $category = array(
                    'displayorder' => 0,
                );
            }
            if (!empty($parentid)) {
                $parent = pdo_fetch("SELECT id, name FROM " . tablename('shopping_category') . " WHERE id = '$parentid'");
                if (empty($parent)) {
                    message('抱歉，上级分类不存在或是已经被删除！', $this->createWebUrl('post'), 'error');
                }
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['catename'])) {
                    message('抱歉，请输入分类名称！');
                }
                $data = array(
                    'weid' => $_W['uniacid'],
                    'name' => $_GPC['catename'],
                    'enabled' => intval($_GPC['enabled']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'isrecommand' => intval($_GPC['isrecommand']),
                    'community' => intval($_GPC['community']),
                    'description' => $_GPC['description'],
                    'parentid' => intval($parentid),
                    'thumb' => $_GPC['thumb']
                );
                if (!empty($id)) {
                    unset($data['parentid']);
                    pdo_update('shopping_category', $data, array('id' => $id));
                    load()->func('file');
                    file_delete($_GPC['thumb_old']);
                } else {
                    pdo_insert('shopping_category', $data);
                    $id = pdo_insertid();
                }
                message('更新分类成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
            }
            include $this->template('category');
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $category = pdo_fetch("SELECT id, parentid FROM " . tablename('shopping_category') . " WHERE id = '$id'");
            if (empty($category)) {
                message('抱歉，分类不存在或是已经被删除！', $this->createWebUrl('category', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_category', array('id' => $id, 'parentid' => $id), 'OR');
            message('分类删除成功！', $this->createWebUrl('category', array('op' => 'display')), 'success');
        }
    }

    /**
     * 设置商品的专题属性
     * @param int id 商品ID
     * @param str type 专题属性
     * @param int data 专题属性状态
     */
    public function doWebSetGoodsProperty() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $type = $_GPC['type'];
        $data = intval($_GPC['data']);

        if (in_array($type, array('new', 'hot', 'recommand', 'discount', 'brush', 'import'))) {
            $data = ($data==1?'0':'1');
            pdo_update("shopping_goods", array("is" . $type => $data), array("id" => $id, "weid" => $_W['uniacid']));
            die(json_encode(array("result" => 1, "data" => $data)));
        }

        if (in_array($type, array('status'))) {
            $data = ($data==1?'0':'1');
            pdo_update("shopping_goods", array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
            die(json_encode(array("result" => 1, "data" => $data)));
        }

        if (in_array($type, array('type'))) {
            $data = ($data==1?'2':'1');
            pdo_update("shopping_goods", array($type => $data), array("id" => $id, "weid" => $_W['uniacid']));
            die(json_encode(array("result" => 1, "data" => $data)));
        }
        die(json_encode(array("result" => 0)));
    }

    public function doWebGoods() {
        global $_GPC, $_W;
        load()->func('tpl');
        load()->model('shopping.goods');
        load()->model('shopping.goods.param');
        load()->model('shopping.spec');
        load()->model('shopping.goods.option');
        load()->model('shopping.suppliers');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';

        $category = pdo_fetchall("SELECT * FROM " . tablename('shopping_category') . " ORDER BY parentid ASC, displayorder DESC", array(), 'id');
        $countryList   = pdo_fetchall("SELECT * FROM " . tablename('shopping_country'));
        $brandList     = pdo_fetchall("SELECT * FROM " . tablename('shopping_brand').' ORDER BY displayorder DESC');
        $cabinetList = pdo_fetchall("SELECT * FROM " . tablename('cabinet').' WHERE status = 0 ORDER BY cabinetid DESC');
        if (!empty($category)) {
            $children = [];
            foreach ($category as $cid => $cate) {
                if (!empty($cate['parentid'])) {
                    $children[$cate['parentid']][$cate['id']] = array($cate['id'], $cate['name']);
                }
            }
        }
        if ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $item = Goods_getGoodsByDetail($id);
                if (empty($item)) {
                    message('抱歉，商品不存在或是已经删除！', '', 'error');
                }
                $allspecs = pdo_fetchall("select * from " . tablename('shopping_spec')." where goodsid=:id order by displayorder asc",array(":id"=>$id));

                foreach ($allspecs as &$s) {
                    $s['items'] = pdo_fetchall("select * from " . tablename('shopping_spec_item') . " where specid=:specid order by displayorder asc", array(":specid" => $s['id']));
                }
                unset($s);
                $params = pdo_fetchall("select * from " . tablename('shopping_goods_param') . " where goodsid=:id order by displayorder DESC", array(':id' => $id));
                $piclist1 = unserialize($item['thumb_url']);
                $piclist = array();
                if(is_array($piclist1)){
                    foreach($piclist1 as $p){
                        $piclist[] = is_array($p)?$p['attachment']:$p;
                    }
                }
                //处理规格项
                $html    = "";
                $res     = Goods_getGoodsSpec($id);
                $options = $res['options'];//商品属性
                $specs   = $res['specs'];//商品规格值
                //找出数据库存储的排列顺序
                if (count($options) > 0) {
                    $html = Goods_setSpecsToHtml($options, $specs);
                }
            }
            if (empty($category)) {
                message('抱歉，请您先添加商品分类！', $this->createWebUrl('category', array('op' => 'post')), 'error');
            }
            if (checksubmit('submit')) {
                if (empty($_GPC['goodsname'])) {
                    message('请输入商品名称！');
                }
                if (empty($_GPC['pcate'])) {
                    message('请选择商品分类！');
                }
                if(empty($_GPC['thumbs'])){
                    $_GPC['thumbs'] = array();
                }

                $id = Goods_saveGoods($id);
                Param_saveGoodsParam($id);
                //处理自定义参数       ims_shopping_goods_param
                $spec_items = Spec_saveGoodsSpec($id);                              //处理商品规格            ims_shopping_spec && ims_shopping_spec_item
                $totalstocks = Option_saveGoodsOption($id, $spec_items);
                //保存产品信息            ims_shopping_goods
                //保存规格              ims_shopping_goods_option
                //总库存
                if ( ($totalstocks > 0) && ($data['totalcnf'] != 2) ) {
                    pdo_update("shopping_goods", array("total" => $totalstocks), array("id" => $id));
                }
                message('商品更新成功！', $this->createWebUrl('goods', array('op' => 'display','page' => $_GPC['page'])), 'success');
            }
            $supp = suppliers_getInfoToManage();
        } elseif ($operation == 'display') {
            $res = Goods_ListToManage();
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT id, thumb FROM " . tablename('shopping_goods') . " WHERE id = :id", array(':id' => $id));
            if (empty($row)) {
                message('抱歉，商品不存在或是已经被删除！');
            }
            pdo_update("shopping_goods", array("deleted" => 1), array('id' => $id));
            message('删除成功！', referer(), 'success');
        } elseif ($operation == 'productdelete') {
            $id = intval($_GPC['id']);
            pdo_delete('shopping_product', array('id' => $id));
            message('删除成功！', '', 'success');
        } elseif ($operation == 'import') {
            $file = trim($_GPC['file']);
            // d($file);
            if($file){
                $filePath = $_SERVER['DOCUMENT_ROOT'].$file;
                if ($filePath) {
                    require_once('../framework/library/phpexcel/PHPExcel.php');
                    $PHPReader = new PHPExcel_Reader_Excel2007();
                    if(!$PHPReader->canRead($filePath)){
                        $PHPReader = new PHPExcel_Reader_Excel5();
                        if(!$PHPReader->canRead($filePath)){
                            message('文件 ['. $file .'] 读取失败，请检查该文件权限！', '', 'fail');
                        }
                    } else {
                        $data = array();
                        $PHPExcel = $PHPReader->load($filePath);
                        $currentSheet = $PHPExcel->getSheet(0);  //取第一个工作表
                        $allColumn = $currentSheet->getHighestColumn();   //列总数
                        $allRow = $currentSheet->getHighestRow();  //行总数
                        //第1行是标题，从第2行读起
                        for($currentRow=2; $currentRow<=$allRow; $currentRow++){
                            $currentRowValue = array();
                            for($currentColumn='A';$currentColumn<=$allColumn;$currentColumn++){
                                $currentCell = $currentColumn.$currentRow;
                                $currentRowValue[] = trim($currentSheet->getCell($currentCell)->getValue());
                                //echo $currentColumn.':'.$currentSheet->getCell($currentCell)->getValue()."\t";
                            }
                            $data[] = $currentRowValue;
                        }
                        //取得所有商品分类
                        $cates = array();
                        $list = pdo_fetchall('SELECT id,name FROM '.tablename('shopping_category').' WHERE weid = :weid',array(':weid'=>$_W['uniacid']));
                        foreach ($list as $cate) {
                            $cates[$cate['name']] = $cate['id'];
                        }
                        $importFailedIdStr = '';
                        foreach ($data as $goods) {
                            $goodsIn = array();
                            $goodsIn['id'] = intval($goods[0]);
                            $goodsIn['title'] = $goods[1];
                            $goodsIn['weid'] = $_W['uniacid'];
                            $goodsIn['pcate'] = $cates[$goods[4]];
                            $goodsIn['ccate'] = 0;
                            $goodsIn['type'] = $goods[6]=='实物' ? 1 : 2;
                            $goodsIn['description'] = $goods[17];  //描述，废弃
                            $goodsIn['thumb'] = 'images/'.$_W['uniacid'].'/'.date('Y').'/'.$goodsIn['id'].'/t1.jpg';
                            $goodsIn['thumb1'] = 'images/'.$_W['uniacid'].'/'.date('Y').'/'.$goodsIn['id'].'/t2.jpg';
                            $goodsIn['unit'] = $goods[2]; //单位，如袋、瓶、包、盒、箱
                            $goodsIn['content'] = '<p><img title="'.$goods[1].'" src="../attachment/images/'.$_W['uniacid'].'/'.date('Y').'/'.$goodsIn['id'].'/c.jpg" alt="images/'.$_W['uniacid'].'/'.date('Y').'/'.$goodsIn['id'].'/t1.jpg" /></p>';
                            $goodsIn['originalprice'] = $goods[8];
                            $goodsIn['marketprice'] = $goods[9];
                            $goodsIn['productprice'] = $goods[10];
                            $goodsIn['comm1'] = $goods[11];
                            $goodsIn['comm2'] = $goods[12];
                            $goodsIn['comm3'] = $goods[13];
                            $goodsIn['total'] = $goods[15];
                            $goodsIn['totalcnf'] = $goods[14];
                            $goodsIn['brand'] = $goods[16];
                            $goodsIn['fdesc'] = $goods[3];  //商品描述
                            $goodsIn['status'] = 0;  //下架
                            //检查指定id商品是否已经存在，并给出提示
                            $rs = pdo_fetch('SELECT id FROM '.tablename('shopping_goods').' WHERE id = :id',array(':id'=>$goodsIn['id']));
                            if($rs){
                                $importFailedIdStr .= $goodsIn['id'].',';
                            } else {
                                $rs = pdo_insert('shopping_goods', $goodsIn);
                            }
                        }
                        if($importFailedIdStr!=''){
                            message('文件导入成功！其中，('.substr($importFailedIdStr, 0, -1).')导入失败，请检查该商品ID是否已经存在！', '', 'fail');
                        } else {
                            message('文件中商品 ['. $file .'] 全部导入成功！', '', 'success');
                        }
                    }
                } else {
                    message('文件 ['. $file .'] 不存在，请先上传！', '', 'fail');
                }
            }
        }else if($_GPC['op'] == 'option'){

        }
        include $this->template('goods');
    }

    /**
     * 管理后台商品规格操作
     * @param str token 令牌
     */
    public function doWebGoodsoption() {
        global $_GPC, $_W;
        load()->model('shopping.goods.option');

        $token = $_GPC['token'];
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if($operation == 'display'){
            if($token == token()){
                Option_getInfoToExecl();
            }
        }else if($operation == 'import'){
            Option_putGoodsOpionByimport();
        }else{
            message('操作失败，异常请求！', referer(), 'error');
        }
    }
    public function doWebOrder(){
        global $_W, $_GPC;
        load()->func('tpl');
        load()->model('shopping.order');
        load()->model('shopping.order.goods');
        load()->model('shopping.suppliers');
        load()->classs('express');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $cabinetListData = pdo_fetchall("SELECT * FROM " . tablename('cabinet'));
        // d($cabinetListData);
        foreach ($cabinetListData as $cabinet) {
            $cabinet[$cabinet['cabinetid']] = $cabinet['name'];
        }

        if ($operation == 'display') {
            //供应商信息(供应商账号不显示)
            if(!$_W['user']['sid']){
                $supp = suppliers_getInfoToManage();
            }

            //获取所有子订单信息
            $ret = Order_getOrderListToManage();
            // d($ret);

            //获取每个订单下商品信息
            foreach($ret['list'] as $key=>$val){
                $ret['list'][$key]['goods'] = sogoods_listByOrderId($val['id']);
            }
        } elseif ($operation == 'detail') {
            # 订单详细
            $id = intval($_GPC['id']);
            $order = ShoppingOrder::singleton($id);
            if($order->parent_ordersn > 0){
                $id = $order->parent_ordersn;
                $order = ShoppingOrder::singleton($order->parent_ordersn);
            }
            $item = Order_getOrderDetailToManage($id);
            $expresscom = pdo_fetchall("SELECT * FROM " . tablename('shopping_express'). " ORDER BY displayorder DESC");
            $order = ShoppingOrder::singleton($id);

            # 发货
            if (checksubmit('confirmsend')) {
                // if (!empty($_GPC['isexpress']) && empty($_GPC['expresssn'])) { message('请输入快递单号！'); }
                // if($item['cancelgoods'] == 1) { message('发货失败，用户申请退货', referer(), 'error'); }

                // $content = '亲爱的云吉家人，您的'.$title.'已发货已由云吉金牌快递员护送着奔向您的怀抱!';

                // $this->sendPost($item['uid'], array('text'=>array('content'=>$content)));
//              load()->model('shopping.order.goods');
                if (!empty($item['transid'])) { 
                    $this->changeWechatSend($id, 1);
                }
                if ($_GPC['isexpress']==1 && empty($_GPC['expresssn'])) {
                    message('请输入快递单号！');
                }
                $order = ShoppingOrder::singleton($_GPC['orderid']);
                $order->status      = ShoppingOrder::STAUTS_DELIVERED;
                $order->sendtype    = intval($_GPC['isexpress']);
                $order->express     = trim($_GPC['express']);
                $order->expresscom  = trim($_GPC['expresscom']);
                $order->expresssn   = trim($_GPC['expresssn']);
                $order->sendexpress = time();
                $order->save();

                //根据选择的是否发送模板消息通知给订单所属用户
                if (intval($_GPC['sendNoticeMsg'])) {
                    //推送发货通知模板消息
                    $userOpenid = pdo_fetchcolumn('SELECT openid FROM '.tablename('members').' WHERE uid=:uid', array(':uid'=>$order->uid));
                    $messages['touser'] = $openid;
                    $orderDetailUrl = 'http://mall.troto.com.cn/order/detail?order_id='.$_GPC['orderid'];
                    $message = array(
                        'first'=>array(
                            'value'=>'小主，您的商品已发货。您可通过『我的订单』查询物流信息。',
                            'color'=>"#000099"
                        ),
                        'keyword1'=>array('value'=>$order->ordersn,'color'=>"#000099"),
                        'keyword2'=>array('value'=>$_GPC['expresscom'],'color'=>"#000099"),
                        'keyword3'=>array('value'=>$_GPC['expresssn'],'color'=>"#000099"),
                        'keyword4'=>array('value'=>date('Y-m-d H:i:s', $order->paymenttime),'color'=>"#000099"),
                        'remark'=>array('value'=>'感谢您的支持，欢迎再次光临十点一刻海淘生活馆！','color'=>"#000099"),
                        );
                    $acid = $_W['account']['uniacid'];
                    $account = WeAccount::create($acid);
                    // $ret = $account->sendTplNotice(,$this->json_en($messages),false);
                    $account->sendTplNotice($userOpenid, self::LOGISTICS_NOTICE_TEMPLATE, $message, $orderDetailUrl);
                    message('发货操作成功，并已提示小伙伴我们发货了！', referer(), 'success');
                } else {
                    message('发货操作成功！', referer(), 'success');
                }

//              $strBetchJoin = empty($_GPC['goodsidIds']) ? 0 : $_GPC['goodsidIds'];
//              if(!$strBetchJoin) message('请选择商品！');
//              $gidGoodsArr = explode(",", $strBetchJoin);
//              foreach ($gidGoodsArr as $goodsItem =>$goodsId) {
//                  $res .= Goods_confirmSendOrderGoods($goodsId,$id).' 和 ';
//              }
            }

            if($_W['isajax']){
                $goodsid = isset($_GPC['goodsid']) ? intval($_GPC['goodsid']) : 0;
                if(!$goodsid){
                    $message['status'] = -200;
                    $message['msc'] = "订单参数有误或不存在！";
                    ajaxReturn($message);
                }
                $orderStatic = pdo_fetch("SELECT * FROM ".tablename("shopping_order_goods")."
                                         WHERE orderid = :orderid AND goodsid = :goodsid",array(":orderid" => $id,":goodsid" => $goodsid));


                if(empty($orderStatic['express'] && $orderStatic['expresssn'] && $orderStatic['expresstime'])){

                    $message['status'] = -200;
                    $message['msc'] = "该订单还未发货！";
                    ajaxReturn($message);
                }else{
                    $res = pdo_update("shopping_order_goods",array('express' => "","expresssn" => "","expresstime" => "","expresscom" => ""),
                        array("orderid" => $id,"goodsid" => $goodsid));
                    if(!$res){
                        $message['status'] = -200;
                        $message['msc'] = "取消发货失败！";
                    } else {
                        $message['status'] = 200;
                        $message['msc'] = true;
                    }
                }
                ajaxReturn($message);
            }
            # 取消发货，订单status 2 => 1
            if (checksubmit('cancelsend')) {
                $orderid = $_GPC['orderid'];
                $order = ShoppingOrder::singleton($orderid);
                $order->status = ShoppingOrder::STAUTS_PAID;
                $order->ext['process'][] = array(
                    'status' => ShoppingOrder::STAUTS_PAID,
                    'user' => $_W['username'],
                    'action' => '取消发货',
                    'remark' => $_GPC['cancelresons'],
                    'time' => time(),
                );
                $order->sendtype    = 0;
                $order->express     = '';
                $order->expresscom  = '';
                $order->expresssn   = '';
                $order->sendexpress = '';
                $order->save();
                if (!empty($item['transid'])) {
                    $this->changeWechatSend($id, 0, $_GPC['cancelresons']);

                }
//              Order_cancelSendOrder($id);
                message('取消发货操作成功！', referer(), 'success');
            }

            # 完成订单
            if (checksubmit('finish')) {
                # 待测试
                if(pdo_fetchcolumn(" SELECT count(1) FROM" . tablename('shopping_order') . " WHERE id = :id and accomplish = 0 and creditsettle = 0 and (cancelgoods = 0 or cancelgoods = 2)", array(':id' => $id))){
                    $rs = pdo_update(
                        'shopping_order', array(
                        'status' => 4,
                        'sendexpress' => TIMESTAMP,
                        'receipttime' => TIMESTAMP,
                        'remark' => $_GPC['remark'],
                        'accomplish' => 1,
                        'creditsettle' => 1), array(
                            'id' => $id)
                    );
                    if($rs){
                        $this->balanceCreditByOrderUpdate($id);
                    }
                    message('订单操作成功！并给该订单相关的上家加分成！', referer(), 'success');
                }
                message('订单操作失败, 请查看订单的状态是完成还是申请售后中！', referer(), 'danger');
            }
            if (checksubmit('cancel')) {
                // pdo_update('shopping_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
                // message('取消完成订单操作成功！', referer(), 'success');
                message('cancel， 当你能打开此功能时请联系开发者', referer(), 'success');
            }
            if (checksubmit('cancelpay')) {
                // pdo_update('shopping_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                //# 设置库存
                // $this->setOrderStock($id, false);
                //# 减少积分
                // $this->balanceCreditByOrderUpdate($id, false);
                message('cancelpay, 当你能打开此功能时请联系开发者！', referer(), 'danger');
            }
            if(checksubmit('cancelgoods')){
                # 取消订单，已支付情况下可以无偿取消订单
                $order = ShoppingOrder::singleton($_GPC['id']);
                $order->status = ShoppingOrder::STAUTS_CANCEL;
                $order->ext['process'][] = array(
                    'status' => ShoppingOrder::STAUTS_CANCEL,
                    'user' => $_W['username'],
                    'action' => '取消订单',
                    'remark' => $_GPC['cancelreson'],
                    'time' => time(),
                );
                $order->save();
                message('取消成功！', referer(), 'success');
            }

            # 用户付款
            if (checksubmit('confrimpay')) {

                $order = ShoppingOrder::singleton($_GPC['id']);
                $ret = Order_confrimpay($_GPC['id'], $order->data);
                if(isset($ret['result']) && $ret['result'] == 'success'){

                    //设置库存
                    $this->setOrderStock($id);
                    $this->payResult($ret);
                    message('确认订单付款操作成功！', referer(), 'success');
                }else{
                    message('操作失败！', referer(), 'error');
                }
                ajaxReturn();
            }

            # 微信推送 订单信息 
            if ($_GPC['wechatsend'] == 'yes' && !empty($_POST)){
                load()->model('shopping.order.goods');
                load()->model('mc');
                $description = "您有一笔待确认的订单：\n";
                $orderInfo = Sogoods_getTitleInfo($id);
                if(!empty($orderInfo)){
                    // $user = mc_fetch($item['uid'], array('credit2'));
                    foreach($orderInfo as $v){
                        $description .= $v['title'];
                        if(!empty($v['optionname'])){
                            $description .= "[".$v['optionname']."]";
                        }
                        $description .= " x".$v['total']."\n";
                    }
                    $description .= "支付金额：". sprintf("%.2f", $item['price'])." 元\n";
                    // $description .= "当前余额：".$user['credit2']." 元\n\n点击查看详情>>";
                    $messages = array(
                        'news'=>array(
                            'articles'=>array(
                                array(
                                    'title'=>'订单提醒',
                                    'description' => $description,
                                    'picurl'=>'http://wechat.yunji007.com/attachment/images/17/2015/07/y69QmVx4QYglxITI5Y2ITGYYdm5GIg.jpg',
                                    'url' => $_W['siteroot'] . 'app' . substr($this->createMobileUrl('seeorder', array('id'=>$id)), 1)
                                )
                            )
                        )
                    );
                    $uid = !empty($_GPC['userid']) ? intval($_GPC['userid']) : $item['uid'];
                    $wxres = wechatPush($uid, $messages);
                    if(isset($wxres['errno'])){
                        message($wxres['message'], referer(), 'danger');
                    }elseif(isset($wxres['errcode'])){
                        if($wxres['errcode'] == 0){
                            message('已推送给微信！', referer(), 'success');
                        }
                        message($wxres['errmsg'], referer(), 'success');
                    }
                }else{
                    message('订单信息不存在！', referer(), 'danger');
                }
            }
            if (checksubmit('closeRefund')) {

                # 订单取消（佣金回退处理、订单取消处理、余额日志、库存）
                // if($item['status'] > ShoppingOrderGoods::STAUTS_SUBMIT){
                //  $res = $this->balanceCreditByOrderUpdate($id, false);
                //  if($res){
                //      pdo_update('shopping_order', array('status' => -1, 'accomplish' => 1, 'creditsettle' => 1, 'remark' => $_GPC['reson']), array('id' => $id));
                //      load()->model('mc');
                //      $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
                //      # 添加回滚金额    日志
                //      mc_credit_update($item['uid'], $setting['creditbehaviors']['currency'], $item['price'],
                //          array($item['uid'], '取消订单，回滚金额 '.$setting['creditbehaviors']['currency'].':' . $item['price'] . ' 元')
                //      );

                //      if (!empty($item['transid']) && $item['paytype'] == 2) {
                //          $this->changeWechatSend($id, 0, $_GPC['reson']);
                //      }
                //      $this->setOrderStock($id, false);
                //      message('该订单已取消，金额已还原！', referer(), 'success');
                //  }else{
                //      message('操作失败！', referer(), 'danger');
                //  }
                // }
                // 
                
                /**易极付退款流程**/
                $order = ShoppingOrder::singleton($id);
                if ($order->status<1) {
                    message('该订单未付款，退不了...', referer(), 'danger');
                }else if($order->price<$_GPC['refundMoney']){
                    message('退款金额不能超过商品总价！', referer(), 'danger');
                }
                $order->ext['process'][] = array(
                    'status' => ShoppingOrder::STAUTS_PAID,
                    'user' => $_W['username'],
                    'action' => '提交退款',
                    'remark' => $_GPC['reson'],
                    'time' => time(),
                );
                $order->save();

                $this->fastPayTradeRefund($id,$_GPC['reson'],$_GPC['refundMoney'],$order->transid,$order->ordersn);
                //$this->fastPayTradeRefund($id,'121212212','0.11',$order->transid,$order->ordersn);
            }
            if(checksubmit('open')) {
                # 开启订单
                pdo_update('shopping_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
                message('订单已开启，当前为未支付状态！', referer(), 'success');
            }
            $dispatch = pdo_fetch("SELECT * FROM " . tablename('shopping_dispatch') . " WHERE id = :id", array(':id' => $item['dispatch']));
            if (!empty($dispatch) && !empty($dispatch['express'])) {
                $express = pdo_fetch("select * from " . tablename('shopping_express') . " WHERE id=:id limit 1", array(":id" => $dispatch['express']));
            }

            load()->model('shopping.address');
            load()->model('mc');
            $item['user'] = Address_getDetailById($item['addressid']);
            $item['info'] = mc_fetch($item['uid'], array('nickname','createtime','realname','credits2'));
            if($item['pid'] == -1){
                foreach($item['child'] as &$childOrder){
                    $childOrder['goods'] = sogoods_listByOrderId($childOrder['id']);
                }
            }else{
                $goodsList = sogoods_listByOrderId($id);
                $item['goods'] = array();
                foreach($goodsList as $good){
                    $item['goods'][] = $good;
                    if ($goods['srtno']!='') {
                        $strstatus=1;
                    }
                }
            }
        } elseif ($operation == 'delete') {
            # 订单删除
            Order_delOrderToManage();
        }
        include $this->template('order');
    }

    /**
     * 售后
     *
     */
    public function doWebAftermarket(){
        global $_W, $_GPC;
        load()->func('tpl');
        load()->model('shopping.order.aftermarket');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if($operation == 'display'){
            $ret = Order_getOrderAftermarketListToManage();
        }else if($operation == 'detail'){
            $item = Aftermarket_getDetailToManage();
            $piclist = unserialize($item['thumbs']);
            // $piclist[] = 'images/16/2015/08/vJs8R6zsRfQZ88Ss28dx1nRqS6FnfS.png';
            // pre($item);
        }else if($operation == 'ConfirmSale'){
            //售后审核通过
            Aftermarket_ConfirmSale();
            ajaxReturn();
        }else if($operation == 'accomplish'){
            //售后完结
            if ($this->finishCustomerOrder($_GPC['orderid'])) {
                message('订单售后处理成功', url('entry/index/order', array('m'=>'ewei_shopping','op'=>'detail','id'=>$_GPC['orderid'])), 'success');
            } else {
                message('售后订单处理原则：<br><ul><li>退货：买家发货超过7天</li><li>换货：卖家重新发货超过7天</li></ul>', url('entry/index/order', array('m'=>'ewei_shopping','op'=>'detail','id'=>$_GPC['orderid'])), 'fail');
            }

        }
        include $this->template('aftermarket');
    }
    /**
     * 管理员操作，作用不大
     *
     */
    public function doWebNocloseOrder(){
        global $_W, $_GPC;
        $id = $_GPC['id'];
        pdo_update('shopping_order', array('status' => 4,'sendexpress'=>TIMESTAMP), array('id' => $id));
        echo 1;
        exit();
    }

    /**
     * 导出商品
     * @param $status = 1 导出上架的商品
     * @param $status = 0 导出下架的商品
     */
    public function doWebGoodsex() {
        global $_W, $_GPC;
        $status = isset($_GPC['status']) ? intval($_GPC['status']) : '';
        load()->model('shopping.goods');
        SG_Goodsex($status);
    }

    /**
     * 导出execl
     */
    public function doWebOrderex() {
        global $_W, $_GPC;
        /**
         * @param $status = 1 导出需要发货的订单
         * @param $status = 2 导出已发货的订单
         */
        load()->model('shopping.order');
        Order_Orderex();
    }

    public function doWebOrderexexp() {
        global $_W, $_GPC;
        require_once('../framework/library/phpexcel/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $sql = 'select 
                    a.ordersn,a.transid,a.cancelgoods, a.`status`, b.total, f.title, e.title as guige, a.createtime,
                    c.realname, c.mobile, c.province, c.city, c.area, c.address, a.remark
                from 
                    ims_shopping_order a, ims_shopping_order_goods b left join ims_shopping_goods_option e on b.optionid = e.id, ims_shopping_goods f, ims_shopping_address c     
                where 
                    a.status >= 2 and a.id = b.orderid and b.goodsid = f.id and c.id = a.addressid and a.weid = :weid order by a.createtime asc';
        $re = pdo_fetchall($sql,array(':weid'=>$_W['uniacid']));
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '日期')
            ->setCellValue('B1', '订单')
            ->setCellValue('C1', '微信单号')
            ->setCellValue('D1', '产品名')
            ->setCellValue('E1', '规格')
            ->setCellValue('F1', '数量')
            ->setCellValue('G1', '退货状态')
            ->setCellValue('H1', '收货人')
            ->setCellValue('I1', '联系方式')
            ->setCellValue('J1', '收货地址')
            ->setCellValue('K1', '备注信息');
        //set width
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(80);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(80);
        foreach($re as $key=>$val){
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.($key+2), date('Y-m-d H:i:s', $val['createtime']))
                ->setCellValue('B'.($key+2), $val['ordersn'].' ')
                ->setCellValue('C'.($key+2), $val['transid'].' ')
                ->setCellValue('D'.($key+2), $val['title'])
                ->setCellValue('E'.($key+2), $val['guige'])
                ->setCellValue('F'.($key+2), $val['total'])
                ->setCellValue('G'.($key+2), $val['cancelgoods']?'退货':'否')
                ->setCellValue('H'.($key+2), $val['realname'])
                ->setCellValue('I'.($key+2), $val['mobile'].' ')
                ->setCellValue('J'.($key+2), $val['province'].$val['city'].$val['area'].$val['address'])
                ->setCellValue('K'.($key+2), $val['remark']);
        }

        // Miscellaneous glyphs, UTF-8

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('订单快递');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $date = date('Y-m-d');
        // Redirect output to a client’s web browser (Excel5)
        ob_end_clean();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$date.'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

        exit;
    }

    //设置订单商品的库存 minus  true 减少  false 增加
    private function setOrderStock($id = '', $minus = true) {
        $goods = pdo_fetchall("SELECT g.id, g.title, g.thumb, g.unit, g.marketprice,g.total as goodstotal,o.total,o.optionid,g.sales FROM " . tablename('shopping_order_goods') . " o left join " . tablename('shopping_goods') . " g on o.goodsid=g.id "
            . " WHERE o.orderid='{$id}'");
        foreach ($goods as $item) {
            if ($minus) {
                //属性
                if (!empty($item['optionid'])) {
                    pdo_query("update " . tablename('shopping_goods_option') . " set stock=stock-:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
                }
                $data = array();
                if (!empty($item['goodstotal']) && $item['goodstotal'] != -1) {
                    $data['total'] = $item['goodstotal'] - $item['total'];
                }
                $data['sales'] = $item['sales'] + $item['total'];
                pdo_update('shopping_goods', $data, array('id' => $item['id']));
            } else {
                //属性
                if (!empty($item['optionid'])) {
                    pdo_query("update " . tablename('shopping_goods_option') . " set stock=stock+:stock where id=:id", array(":stock" => $item['total'], ":id" => $item['optionid']));
                }
                $data = array();
                if (!empty($item['goodstotal']) && $item['goodstotal'] != -1) {
                    $data['total'] = $item['goodstotal'] + $item['total'];
                }
                $data['sales'] = $item['sales'] - $item['total'];
                pdo_update('shopping_goods', $data, array('id' => $item['id']));
            }
        }
    }
    public function doWebNotice() {
        global $_GPC, $_W;
        load()->func('tpl');
        $operation = empty($_GPC['op']) ? 'display' : $_GPC['op'];
        $operation = in_array($operation, array('display')) ? $operation : 'display';
        $pindex = max(1, intval($_GPC['page']));
        $psize = 50;
        if (!empty($_GPC['date'])) {
            $starttime = strtotime($_GPC['date']['start']);
            $endtime = strtotime($_GPC['date']['end']) + 86399;
        } else {
            $starttime = strtotime('-1 month');
            $endtime = time();
        }
        $where = " WHERE `weid` = :weid AND `createtime` >= :starttime AND `createtime` < :endtime";
        $paras = array(
            ':weid' => $_W['uniacid'],
            ':starttime' => $starttime,
            ':endtime' => $endtime
        );
        $keyword = $_GPC['keyword'];
        if (!empty($keyword)) {
            $where .= " AND `feedbackid`=:feedbackid";
            $paras[':feedbackid'] = $keyword;
        }
        $type = empty($_GPC['type']) ? 0 : $_GPC['type'];
        $type = intval($type);
        if ($type != 0) {
            $where .= " AND `type`=:type";
            $paras[':type'] = $type;
        }
        $status = empty($_GPC['status']) ? 0 : intval($_GPC['status']);
        $status = intval($status);
        if ($status != -1) {
            $where .= " AND `status` = :status";
            $paras[':status'] = $status;
        }
        $total = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('shopping_feedback') . $where, $paras);
        $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_feedback') . $where . " ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $paras);
        $pager = pagination($total, $pindex, $psize);
        $transids = array();
        foreach ($list as $row) {
            $transids[] = $row['transid'];
        }
        if (!empty($transids)) {
            $sql = "SELECT * FROM " . tablename('shopping_order') . " WHERE weid='{$_W['uniacid']}' AND transid IN ( '" . implode("','", $transids) . "' )";
            $orders = pdo_fetchall($sql, array(), 'transid');
        }
        $addressids = array();
        if(is_array($orders)){
            foreach ($orders as $transid => $order) {
                $addressids[] = $order['addressid'];
            }
        }
        $addresses = array();
        if (!empty($addressids)) {
            $sql = "SELECT * FROM " . tablename('shopping_address') . " WHERE weid='{$_W['uniacid']}' AND id IN ( '" . implode("','", $addressids) . "' )";
            $addresses = pdo_fetchall($sql, array(), 'id');
        }
        foreach ($list as &$feedback) {
            $transid = $feedback['transid'];
            $order = $orders[$transid];
            $feedback['order'] = $order;
            $addressid = $order['addressid'];
            $feedback['address'] = $addresses[$addressid];
        }
        include $this->template('notice');
    }

    /**
     * 购物车数量
     *
     */
    public function getCartTotal() {
        load()->model('shopping.cart');
        return Cart_getCartTotal();
    }

    private function getFeedbackType($type) {
        $types = array(1 => '维权', 2 => '告警');
        return $types[intval($type)];
    }

    private function getFeedbackStatus($status) {
        $statuses = array('未解决', '用户同意', '用户拒绝');
        return $statuses[intval($status)];
    }

    /**
     * 用户签到
     */
    public function doMobileSignInToday(){
        global $_GPC, $_W;
        $ret = array('status'=>-100, 'msg'=>'用户异常，请稍后再试。');
        $uid = $_W['member']['uid'];
        load()->model('signin');
        $res = saveUserSign();
        if($res === 200){
            $res = getMySignin();
            $ret['route'] = $res['usersign']['route'];
            $ret['times'] = $res['usersign']['times'];
            $ret['status'] = 200;
            $ret['msg'] = '签到成功，明天继续！';
        }elseif($res == -100){
            $ret['msg'] = '异常签到！';
        }elseif($res == -101){
            $ret['msg'] = '今天已经签过了！';
        }elseif($res == -200){
            $ret['msg'] = '用户异常，请稍后再试！';
        }
        exit(json_encode($ret));
    }

    /**
     * 用户签到
     *
     */
    public function doMobileUserSign(){
        global $_GPC, $_W;
        // $this->checkAuth();
        load()->model('shopping.goods');
        $op = $_GPC['op'];
        $agid = intval($_GPC['agid']);
        $goodsid = intval($_GPC['awardid']);
        if($_W['isajax']){
            $ret = array('status' => -200, 'msg' => '兑换失败！');
            if(empty($_W['member']['uid'])){
                $ret['msg'] = '用户异常，请稍后再试！';
            }else if($agid == 0 && $op != 'coupon'){
                $ret['msg'] = '兑换异常，请刷新后再试！';
            }else{
                load()->model('signin');
                load()->func('WebConst');
                $res = signin_exchangeGoods($agid);
                if($res === false){
                    $ret['msg'] = '兑换失败，搞奖品出现异常！';
                }else if($res === -504){
                    $ret['msg'] = Webconst($res);
                }else if($res === -510){
                    $ret['msg'] = Webconst($res);
                }else if($res === -501){
                    $ret['msg'] = Webconst($res);
                }else if($res > 0){
                    $ret['status'] = 200;
                    $ret['msg'] = '兑换成功！';
                    if($op == 'coupon'){
                        $ret['link'] = url('mc/profile', array('agrid' => $res));
                    }else{
                        $ret['link'] = url('entry/index/address', array('m' => 'ewei_shopping', 'op' => 'usersign', 'agrid' => $res));
                    }
                }
            }
            ajaxReturn($ret);
        }
        $goods = Goods_getGoodsByDetail($goodsid);
        if (empty($goods)) {
            message('抱歉，商品不存在或是已经被删除！');
        }
        //浏览量
        Goods_putGoodsViewcount($goodsid);
        $piclist1 = array(array("attachment" => $goods['thumb']));
        $piclist = array();
        if (is_array($piclist1)) {
            foreach($piclist1 as $p){
                $piclist[] = is_array($p)?$p['attachment']:$p;
            }
        }
        if ($goods['thumb_url'] != 'N;') {
            $urls = unserialize($goods['thumb_url']);
            if (is_array($urls)) {
                foreach($urls as $p){
                    $piclist[] = is_array($p)?$p['attachment']:$p;
                }
            }
        }
        $marketprice = $goods['marketprice'];
        $productprice= $goods['productprice'];
        $originalprice = $goods['originalprice'];
        $stock = $goods['total'];

        $res = Goods_getGoodsSpec($goodsid);
        $specs = $res['specs'];
        $options = $res['options'];
        include $this->template('usersign');
    }

    /**
     * 获取用户城市
     *
     */
    public function doMobilegetMyCity(){
        $city = '中国';
        $ip = getip();
        $res = GetIpLookup($ip);
        if(!empty($res)){
            $city = $res;
        }
        echo $city;
        exit();
    }

    /**
     * 抢红包
     *
     */
    public function doMobileRedPacket(){
        global $_GPC, $_W;
        load()->model('mc');
        /**
         * 授权用户抢红包
         * 获取活动信息
         * 当前活动信息是否存在
         * 活动状态、活动时间
         *
         * 抢红包中，当前金额、数量、
         */

        $ret = array('status'=>-100, 'msg'=>'异常请求！');
        $op = $_GPC['op'];
        $uid = $_W['member']['uid'];
        /**
         * 授权
         *
         */
        if(empty($uid) || (empty($_W['member']['avatar']) || empty($_W['member']['nickname']))){
            $mcres = mc_oauth_userinfo();
            if($mcres['errno'] < 0){
                message($mcres['message'], $this->createMobileUrl('list'), 'error');
            }
        }
        $id = intval($_GPC['id']);
        load()->model('activity.redpacket');
        load()->func('WebConst');

        $res = redpacket_getInfoById($id);

        if((empty($id) || empty($res)) && !$_W['isajax']){
            message('抱歉！当前页面不存在，还是去首页看看吧！', $this->createMobileUrl('list'), 'error');
        }else if($res['returnstatus'] < 0 && $_W['isajax']){
            $ret['msg'] = Webconst($res['returnstatus']);
            ajaxReturn($ret);
        }
        $activityMoney = $res['countmoney'];                //总余额
        $surplus = $res['countmoney'] - $res['gain'];       //可抢金额
        $sendnum = $res['sendnum'];                         //可抢数量
        $getnum = $res['getnum'];                           //已抢数量
        $settingMoney = $res['money'];                      //当前可抢到面额
        // console($res);
        // console('---'.$money);
        if($_W['isajax'] && $op == 'grab'){
            load()->model('mc.get.bonus');
            $UserUse = getBonus_existGain($res['fid'], $uid);
            if($UserUse == 0){
                if($surplus > 0){
                    $money = randRedBond($surplus, $sendnum, $getnum, ($surplus/$sendnum), $settingMoney);      //获取的金额
                    if($surplus <= $money){
                        $money = $surplus;
                    }
                    //当前可抢到的余额
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'snid' => $res['fid'],
                        'getuid' => $uid,
                        'senduid' => 0,
                        'money' => $money,
                        'time' => TIMESTAMP,
                        'status' => 2
                    );
                    pdo_insert('mc_get_bonus', $data);
                    pdo_query('UPDATE ' . tablename('activity_redpacket') .' SET getnum = getnum + 1,gain = gain + '.$money.' WHERE fid = :fid',array(':fid' => $res['fid']));
                    pdo_query('UPDATE ' . tablename('mc_members') .' SET credit2 = credit2 + '.$money.',credit7 = credit7 + '.$money.' WHERE uid = :uid',array(':uid' => $uid));
                    mc_credit_update($uid, 'credit2', $money, array($uid, '用户通过群红包获取：'.$money.'元'));
                    $ret['status'] = 200;
                    $ret['date'] = date('Y-m-d');
                    $ret['msg'] = '恭喜！您抢到了 '.$money.' 元抵用券！已帮您转到您的账户余额上，你可以用来消费以及发红包！';
                }else{
                    $ret['status'] = -201;
                    $ret['msg'] = '当前抵用券已被抢完，请留意下次的活动！';
                }
            }else{
                $ret['status'] = -200;
                $ret['msg'] = '您已经参加过本次活动，请留意下次的活动！';
            }
            exit(json_encode($ret));
        }elseif($_W['isajax'] && $op == 'info'){
            $ret = array('month'=>0, 'dataList'=>array(),'status'=>200);
            $money = pdo_fetchcolumn(" SELECT gain FROM " . tablename('activity_redpacket') .
                " WHERE uniacid = :uniacid and fid = :snid ",
                array(':uniacid' => $_W['uniacid'], ':snid' => $res['fid'])
            );
            if(!empty($money)){
                $ret['month'] = $money;
            }
            $ret['dataList'] = pdo_fetchall('select u.nickname,u.avatar, FROM_UNIXTIME( a.time, "%m-%d %H:%i" ) time,a.money, a.fid 
                                             from '. tablename('mc_members').' u, '. tablename('mc_get_bonus').' a 
                                             where u.uid = a.getuid and a.status = 2 and a.senduid = 0 and a.snid = :snid 
                                             and unix_timestamp(now())-3 <= a.time 
                                             order by a.time asc limit 12', array(':snid'=>$res['fid']));
            if(empty($ret['dataList'])){
                $ret['dataList'] = 0;
            }else{
                foreach($ret['dataList'] as $k=>$v){
                    if(empty($v['avatar'])){
                        $ret['dataList'][$k]['avatar'] = $_W['attachurl'].'images/global/avatars/avatar_7.jpg';
                    }
                    if(empty($v['nickname'])){
                        $ret['dataList'][$k]['nickname'] = '小吉鹿';
                    }
                }
            }
            exit(json_encode($ret));
        }else{
            $list = pdo_fetchall('select u.nickname,u.avatar, FROM_UNIXTIME( a.time, "%m-%d %H:%i" ) time,a.money, a.fid
                                  from '. tablename('mc_members').' u, '. tablename('mc_get_bonus').' a 
                                  where u.uid = a.getuid and a.status = 2 and a.senduid = 0 and a.snid = :snid and unix_timestamp(now())-3 < a.time  order by a.time desc limit 12', array(':snid' => $res['fid']));
            if(empty($list)){
                $list = pdo_fetchall('select u.nickname,u.avatar, FROM_UNIXTIME( a.time, "%m-%d %H:%i" ) time,a.money, a.fid
                                      from '. tablename('mc_members').' u, '. tablename('mc_get_bonus').' a 
                                      where u.uid = a.getuid and a.status = 2 and a.senduid = 0 and a.snid = :snid  order by a.time desc limit 12', array(':snid' => $res['fid']));
            }
        }
        include $this->template('redpacket');
    }

    /**
     * 抢红包
     *
     */
    public function doMobileRedPack(){
        global $_GPC, $_W;
        load()->model('activity.redpacket');
        load()->model('mc.get.bonus');

        $op = in_array($_GPC['a'], array('display', 'list', 'grab')) ? $_GPC['a'] : 'display';      # 控制器
        $uid = $_W['member']['uid'];        # 用户ID
        $id = intval($_GPC['id']);          # 红包ID

        # 未登陆需用户授权
        if(empty($uid) || (empty($_W['member']['avatar']) || empty($_W['member']['nickname'])) && !$_W['isajax']){
            load()->model('mc');
            $mcres = mc_oauth_userinfo();
            if($mcres['errno'] < 0){
                message($mcres['message'], $this->createMobileUrl('list'), 'error');
            }
        }else if($id == 0 && !$_W['isajax']){
            message('抱歉！当前页面不存在，还是去首页看看吧！', $this->createMobileUrl('list'), 'error');
        }

        if($op == 'display'){
            # 开抢界面

            include $this->template('redpack');
        }else if($op == 'list'){
            # 已抢列表

            $BonusRes = getBonus_list($id, $uid);
            $list = $BonusRes['list'];
            $user = $BonusRes['user'];

            $redpack = redpacket_getInfoById($id);
            if($redpack['sendnum'] == $redpack['getnum'] && $redpack['getnum']!=0){
                $redpack['getnum'] = $redpack['sendnum'];

            }else if(empty($user) && ($redpack['sendnum'] > $redpack['getnum'])){
                $op = 'display';
            }

            include $this->template('redpack');
        }else if($op == 'grab' && $_W['isajax']){
            $ret = array('status'=>-100, 'link' => $this->createMobileUrl('redpack', array('a' => 'list', 'id' => $id)));
            if(!$uid){
                $ret['msg'] = '请刷新后再试！';
                ajaxReturn($ret);
            }
            # 获取群红包信息
            $res = redpacket_getInfoById($id);
            $UserUse = getBonus_existGain($res['fid'], $uid);
            if($UserUse > 0 && $uid){
                $ret['status'] = 200;
            }
            if($res['returnstatus'] < 0){
                # 失败的状态码
                load()->func('WebConst');
                $ret['msg'] = Webconst($res['returnstatus']);
                ajaxReturn($ret);
            }
            $activityMoney = $res['countmoney'];                # 总余额
            $surplus = $res['countmoney'] - $res['gain'];       # 可抢金额
            $sendnum = $res['sendnum'];                         # 可抢数量
            $getnum = $res['getnum'];                           # 已抢数量
            $settingMoney = $res['money'];                      # 当前可抢到面额

            # 抢红包逻辑
            # 是否已抢
            if($UserUse == 0){
                if($surplus > 0){
                    # 获取的金额
                    $money = randRedBond($surplus, $sendnum, $getnum, ($surplus/($sendnum-$getnum)), $settingMoney);        //获取的金额
                    if($surplus <= $money)
                        $money = $surplus;

                    # 当前可抢到的余额
                    $data = array(
                        'uniacid' => $_W['uniacid'],
                        'snid' => $res['fid'],
                        'getuid' => $uid,
                        'senduid' => 0,
                        'money' => $money,
                        'time' => TIMESTAMP,
                        'status' => 2
                    );
                    pdo_insert('mc_get_bonus', $data);
                    pdo_query('UPDATE ' . tablename('activity_redpacket') .' SET getnum = getnum + 1,gain = gain + '.$money.' WHERE fid = :fid',array(':fid' => $res['fid']));
                    // pdo_query('UPDATE ' . tablename('mc_members') .' SET credit2 = credit2 + '.$money.',credit7 = credit7 + '.$money.' WHERE uid = :uid',array(':uid' => $uid));
                    mc_credit_update($uid, 'credit2', $money, array($uid, '用户通过群红包获取：'.$money.'元'));
                    $ret['status'] = 200;
                    // $ret['msg'] = '恭喜！您抢到了 '.$money.' 元抵用券！已帮您转到您的账户余额上，你可以用来消费以及发红包！';
                }else{
                    $ret['status'] = -201;
                    $ret['msg'] = '当前抵用券已被抢完，请留意下次的活动！';
                }
            }
            ajaxReturn($ret);
        }
    }

    /**
     * 我的购物车
     *
     */
    public function doMobileMyCart() {
        global $_W, $_GPC;
        $this->checkAuth();
        $op = $_GPC['op'];
        $_SESSION['cartid'] = 2;
        $_SESSION['orderid'] = null;
        load()->model('shopping.cart');

        if ($op == 'add') {
            $goodsid = intval($_GPC['id']);
            $total   = intval($_GPC['total']);
            $total   = empty($total) ? 1 : $total;
            $optionid = intval($_GPC['optionid']);
            $goods = pdo_fetch("SELECT id, type, total,marketprice,maxbuy FROM " . tablename('shopping_goods') . " WHERE id = :id", array(':id' => $goodsid));
            if (empty($goods)) {
                $result['message'] = '抱歉，该商品不存在或是已经被删除！';
                message($result, '', 'ajax');
            }
            $marketprice = $goods['marketprice'];
            if (!empty($optionid)) {
                $option = pdo_fetch("select marketprice from " . tablename('shopping_goods_option') . " where id=:id limit 1", array(":id" => $optionid));
                if (!empty($option)) {
                    $marketprice = $option['marketprice'];
                }
            }
            $row = pdo_fetch("SELECT id, total FROM " . tablename('shopping_cart') . " WHERE from_user = :from_user AND weid = '{$_W['uniacid']}' AND goodsid = :goodsid  and optionid=:optionid", array(':from_user' => $_W['fans']['from_user'], ':goodsid' => $goodsid,':optionid'=>$optionid));
            if ($row == false) {
                //不存在
                $data = array(
                    'weid' => $_W['uniacid'],
                    'goodsid' => $goodsid,
                    'goodstype' => $goods['type'],
                    'marketprice' => $marketprice,
                    'from_user' => $_W['fans']['from_user'],
                    'total' => $total,
                    'optionid' => $optionid
                );
                pdo_insert('shopping_cart', $data);
            } else {
                //累加最多限制购买数量
                $t = $total + $row['total'];
                if (!empty($goods['maxbuy'])) {
                    if ($t > $goods['maxbuy']) {
                        $t = $goods['maxbuy'];
                    }
                }
                //存在
                $data = array(
                    'marketprice' => $marketprice,
                    'total' => $t,
                    'optionid' => $optionid
                );
                pdo_update('shopping_cart', $data, array('id' => $row['id']));
            }
            //返回数据
            $carttotal = $this->getCartTotal();
            $result = array(
                'result' => 1,
                'total' => $carttotal
            );
            die(json_encode($result));
        } else if ($op == 'clear') {
            Cart_delAllById();
            die(json_encode(array("result" => 1)));
        } else if ($op == 'remove') {
            $id = intval($_GPC['id']);
            Cart_delMyCartById($id);
            die(json_encode(array("result" => 1, "cartid" => $id)));
        } else if ($op == 'update') {
            $id  = intval($_GPC['id']);
            $num = intval($_GPC['num']);
            Cart_putMyCartNumById($id, $num);
            die(json_encode(array("result" => 1)));
        } else {
            $list = Cart_getMyCart();
            foreach ($list as $goodsKey=>$goodsOne) {
                $list[$goodsKey]['limit'] = '';
                $params = pdo_fetchall("select * from " . tablename("shopping_goods_param") . " where goodsid=:goodsid",array(':goodsid'=>$goodsOne['goods']['id']));
                foreach ($params as $limits) {
                    if (isset($limits['title']) && $limits['title']=='limit') {
                        $list[$goodsKey]['limit'] = $limits['value'];
                    }
                }
            }
            include $this->template('cart');
        }
    }

    /**
     * 确定订单
     *
     */
    public function doMobileConfirm() {
        global $_W, $_GPC;
        $this->checkAuth();
        load()->model('shopping.goods');
        $operation = $_GPC['op'];
        Goods_getGoodsToOrderConfirm();
        # 隐匿商品参数（待优化）
        if($operation == 'order'){
            //$id = substr($_GPC['id'], 0, -1);
            $id = $_GPC['id'];      # 前端过来了最后的逗号，后端直接获取即可
            $_SESSION['orderid'] = $id;
            exit();
        }

        $totalprice = 0;
        $allgoods = array();
        $id = intval($_GPC['id']);
        $optionid = intval($_GPC['optionid']);
        $total = intval($_GPC['total']);
        if((empty($total)) || ($total < 1)){
            $total = 1;
        }
        $direct = false;    # 是否是直接购买
        $returnurl = "";    # 当前连接
        $_SESSION['cartid'] = $id;
        if (!empty($id)) {
            $item = pdo_fetch("select id,thumb,title,weight,marketprice,total,type,totalcnf,sales,unit,istime,timeend,sid,isimport from " . tablename("shopping_goods") . " where id=:id limit 1", array(":id" => $id));
            if ($item['istime'] == 1) {
                if (time() > $item['timeend']) {
                    $backUrl = $this->createMobileUrl('detail', array('id' => $id));
                    $backUrl = $_W['siteroot'] . 'app' . ltrim($backUrl, '.');
                    message('抱歉，商品限购时间已到，无法购买了！', $backUrl, "error");
                }
            }
            if (!empty($optionid)) {
                $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $optionid));
                if ($option) {
                    $item['optionid'] = $optionid;
                    $item['title'] = $item['title'];
                    $item['optionname'] = $option['title'];
                    $item['marketprice'] = $option['marketprice'];
                    $item['weight'] = $option['weight'];
                }
            }
            $item['sid'] = $item['sid'];
            $item['stock'] = $item['total'];
            $item['total'] = $total;
            $item['totalprice'] = $total * $item['marketprice'];
            $allgoods[] = $item;
            $totalprice+= $item['totalprice'];
            if ($item['type'] == 1) {
                $needdispatch = true;
            }
            $direct = true;
            $returnurl = $this->createMobileUrl("confirm", array("id" => $id, "optionid" => $optionid, "total" => $total, 'community' => $_GPC['community']));
            $_SESSION['orderid'] = null;
        }
        if (!$direct) {
            # 如果不是直接购买（从购物车购买）
            $id = $_SESSION['orderid'];
            $where = '';
            if(!empty($id)){
                $where = ' and id in ('.$id.')';
            }
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_cart') . " WHERE  weid = '{$_W['uniacid']}' AND from_user = '{$_W['fans']['from_user']}' ".$where);
            if (!empty($list)) {
                foreach ($list as &$g) {
                    $item = pdo_fetch("select id,thumb,title,weight,marketprice,total,type,totalcnf,sales,unit,sid,isimport from " . tablename("shopping_goods") . " where id=:id limit 1", array(":id" => $g['goodsid']));
                    # 属性
                    $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $g['optionid']));
                    if ($option) {
                        $item['optionid'] = $g['optionid'];
                        $item['title'] = $item['title'];
                        $item['optionname'] = $option['title'];
                        $item['marketprice'] = $option['marketprice'];
                        $item['weight'] = $option['weight'];
                    }
                    $item['sid'] = $item['sid'];
                    $item['stock'] = $item['total'];
                    $item['total'] = $g['total'];
                    $item['totalprice'] = $g['total'] * $item['marketprice'];
                    $allgoods[] = $item;
                    $totalprice+= $item['totalprice'];
                    if ($item['type'] == 1) {
                        $needdispatch = true;
                    }
                }
                unset($g);
            }
            $returnurl = $this->createMobileUrl("confirm", array('community' => $_GPC['community']));
        }
        if (count($allgoods) <= 0) {
            header("location: " . $this->createMobileUrl('myorder'));
            exit();
        }
        # 配送方式
        $dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("shopping_dispatch") . " order by displayorder desc");
        foreach ($dispatch as &$d) {
            $weight = 0;
            foreach ($allgoods as $g) {
                $weight+=$g['weight'] * $g['total'];
            }
            $price = 0;
            if ($weight <= $d['firstweight']) {
                $price = $d['firstprice'];
            } else {
                $price = $d['firstprice'];
                $secondweight = $weight - $d['firstweight'];
                if ($secondweight % $d['secondweight'] == 0) {
                    $price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
                } else {
                    $price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
                }
            }
            $d['price'] = $price;
        }

        //判断是否含有海外商品（需要填写证件信息）
        $isimportOrder = false;
        foreach ($allgoods as $goods) {
            if ($goods['isimport']) {
                $isimportOrder = true;
            }
        }

        unset($d);
        # 表单提交，生成订单
        if (checksubmit('submit')) {
            # 是否自提
            $sendtype=1;
            $address = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => intval($_GPC['address'])));
            if (empty($address)) {
                message('抱歉，请您填写收货地址！');
            }
            # 商品价格
            $goodsprice = 0;
            foreach ($allgoods as $row) {
                if ($item['stock'] != -1 && $row['total'] > $item['stock']) {
                    message('抱歉，“' . $row['title'] . '”此商品库存不足！', $this->createMobileUrl('confirm'), 'error');
                }
                $goodsprice+= $row['totalprice'];
            }
            # 运费
            $dispatchid = intval($_GPC['dispatch']);
            $dispatchprice = 0;
            foreach ($dispatch as $d) {
                if ($d['id'] == $dispatchid) {
                    $dispatchprice = $d['price'];
                    $sendtype = $d['dispatchtype'];
                }
            }
            /**
             * $data = {}新增uid
             * 'ordersn' => date('md') . random(4, 1),
             */
            $data = array(
                'weid' => $_W['uniacid'],
                'from_user' => $_W['fans']['from_user'],
                'uid' => $_W['member']['uid'],
                'ordersn' => randNumByOrder(5),
                'price' => $goodsprice + $dispatchprice,
                'dispatchprice' => $dispatchprice,
                'goodsprice' => $goodsprice,
                'status' => 0,
                'sendtype' =>intval($sendtype),
                'dispatch' => $dispatchid,
                'goodstype' => intval($cart['type']),
                'remark' => $_GPC['remark'],
                'addressid' => $address['id'],
                'createtime' => TIMESTAMP,
                'community' => intval($_GPC['community'])
            );
            if ( $isimportOrder && $_GPC['idName']!='' && $_GPC['idName']!='' ) {
                $data['id_no'] = $_GPC['idCard'];
                $data['id_name'] = $_GPC['idName'];
            }
            pdo_insert('shopping_order', $data);
            $orderid = pdo_insertid();

            # 插入订单商品
            foreach ($allgoods as $row) {
                if (empty($row)) {
                    continue;
                }
                $d = array(
                    'weid' => $_W['uniacid'],
                    'goodsid' => $row['id'],
                    'orderid' => $orderid,
                    'total' => $row['total'],
                    'price' => $row['marketprice'],
                    'createtime' => TIMESTAMP,
                    'optionid' => $row['optionid'],
                    'sid' => $row['sid']
                );
                $o = pdo_fetch("select title from ".tablename('shopping_goods_option')." where id=:id limit 1",array(":id"=>$row['optionid']));
                if(!empty($o)){
                    $d['optionname'] = $o['title'];
                }
                pdo_insert('shopping_order_goods', $d);
            }
            # 清空购物车
            if (!$direct) {
                pdo_delete("shopping_cart", array("weid" => $_W['uniacid'], "from_user" => $_W['fans']['from_user']));
            }
            # 变更商品库存
            $this->setOrderStock($orderid);
            $logoutjs = "<script language=\"javascript\" type=\"text/javascript\">window.location.href=\"" . $this->createMobileUrl('pay', array('orderid' => $orderid)) . "\";</script>";
            exit($logoutjs);
        }

        $row = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE isdefault = 1 and openid = :openid limit 1", array(':openid' => $_W['fans']['from_user']));
        include $this->template('confirm');
    }

    /**
     * 订单商品库存不足继续支付确认订单
     */
    public function doMobileConfirmNotEnoughOrder() {
        global $_W, $_GPC;
        $this->checkAuth();
        $orderid = intval($_GPC['orderid']);

        load()->model('shopping.order.goods');
        $allgoods = goods_getOrderGoodsDetailByOrderId($orderid);

        if (count($allgoods) < 1) {
            header("location: " . $this->createMobileUrl('myorder'));
            exit();
        }

        //更新字段方便公用confirm模板
        $totalprice = 0;
        foreach ($allgoods as $ogkey => $orderGoods) {
            $allgoods[$ogkey]['total'] = $orderGoods['num'];
            $orderGoods['marketprice'] = $orderGoods['optionmarketprice'] ? intval($orderGoods['optionmarketprice']) : $orderGoods['marketprice'];
            $totalprice += $orderGoods['num']*$orderGoods['marketprice'];
        }

        # 配送方式
        $dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("shopping_dispatch") . " order by displayorder desc");
        foreach ($dispatch as &$d) {
            $weight = 0;
            foreach ($allgoods as $g) {
                $weight+=$g['weight'] * $g['total'];
            }
            $price = 0;
            if ($weight <= $d['firstweight']) {
                $price = $d['firstprice'];
            } else {
                $price = $d['firstprice'];
                $secondweight = $weight - $d['firstweight'];
                if ($secondweight % $d['secondweight'] == 0) {
                    $price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
                } else {
                    $price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
                }
            }
            $d['price'] = $price;
        }
        unset($d);
        $row = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE isdefault = 1 and openid = :openid limit 1", array(':openid' => $_W['fans']['from_user']));
        include $this->template('confirm');
    }

    /**
     * 更新分成及业绩（订单状态更新后调用更新)
     *    credit1 红包, credit2-余额, credit3-三级分成, credit3results-三级业绩,
     *    credit4-代理分成,  credit4results-代理业绩,
     *    credit5-冻结余额,  credit6-实际付款,  credit7-累计余额
     *
     * @param $orderId int 订单号
     * @param $add bool true-加业绩佣金, false-减业绩佣金
     * @param $type bool true-正式增加, false-临时增加(被冻结)
     * return bool
     */
    private function balanceCreditByOrderUpdate($orderId, $add=true, $type=true) {
        load()->func('compat.biz');
        load()->func('pdoT');  //事务操作封装函数库
        load()->model('mc');
        load()->model('shopping.order');
        load()->model('mc.comm.log');

        //获取订单详细信息
        $order = Order_getOrderGoodsDetailByOrderId($orderId);

        //空订单/未支付/取消/关闭订单直接返回错误
        if(empty($order) || $order[0]['status']<1) {
            return false;
        }

        $uid = $order[0]['uid'];
        //如果是修复分成则使用支付时间；否则使用运行时间
        $orderPayDt = $order[0]['paymenttime']>strtotime('2010-01-01') ? $order[0]['paymenttime'] : time();
        //获取用户nickname
        $nickname = fans_search($uid, array('nickname','status'));
        $nickname = $nickname['nickname'];

        //根据结算所处的步骤获得需要处理的上家们
        if ($add && !$type) {
            $pid = getPid($uid,3);
        } else {
            $orderuid = isset($order[0]['uid']) ? $order[0]['uid'] : $order['uid'];
            //根据分成流水将三级分成放入订单内相应商品信息中
            $order = $this->updateOrderGoodsCommByCommLog($order, getOrderGoodsCommByOrderId($orderId));
            $pid = getPidsByCommLog($orderuid, $orderId, 3);
        }

        //循环
        //1、计算分成及订单总额
        //2、拿到订单内商品名称列表
        $goodsTitle = array();
        $comms = array();
        $orderTotalPrice = $comms[1] = $comms[2] = $comms[3] = 0;
        foreach($order as $orderGoods){
            $comms[1] += $orderGoods['comm1']*$orderGoods['total'];
            $comms[2] += $orderGoods['comm2']*$orderGoods['total'];
            $comms[3] += $orderGoods['comm3']*$orderGoods['total'];
            $orderTotalPrice += $orderGoods['price']*$orderGoods['total'];  //订单总额(业绩)

            if (trim($orderGoods['title'])!='') {
                $goodsTitle[] = $orderGoods['title'];
            }
        }
        $goodsTitleTxt = implode(' 和 ', $goodsTitle);

        //如果订单总额、没有上家，则推送消费提示消息给张总,并更新分成状态为已经处理
        if ( $add && !$type && (!$orderTotalPrice || empty($pid[1])) ){
            $text = "消费提示: 有新的消费单！\n";
            $text .= $nickname['nickname'].'在'.date('Y-m-d H:i:s',$orderPayDt)."购买了{$goodsTitleTxt}，订单总价￥{$orderTotalPrice}！";
            $this->sendPostToOpenid('opZZjs3o2cO1w9ztqdF4WEUELbu4', array('text'=>array('content'=>$text)));
            // addMsgIntoQueen(37,'opZZjs3o2cO1w9ztqdF4WEUELbu4', 1, array('text'=>$text));
            pdo_update('shopping_order', array('creditsettle'=>1), array('id'=>$orderId));
        } else {
            //否则进入佣金-业绩-冻结-总余额等数据结算逻辑
            load()->model('mc.mapping.fans');

            pdoT_begin();

            foreach ($pid as $level => $upperUID) {
                //如果存在上家
                if ($upperUID) {
                    //查询具体上家详细信息
                    $upperUser = fans_search($upperUID, array("credit3","credit3results","credit5",'nickname','status'));
                    //正式发放佣金
                    if ($add && $type) {
                        $customType = 0;
                        foreach ($order as $orderGoods) {
                            if ($orderGoods['order_goods_cancle']!='0') {
                                $customType = $orderGoods['order_goods_cancle'];
                            }
                        }
                        //如果没有退货
                        if($customType!=2){
                            //解除冻结
                            pdoT_execute('update '.tablename('mc_members').' set credit5=credit5-'.$comms[$level].' where uid='.$upperUID);
                            //更新佣金发放记录标识
                            pdoT_execute('update '.tablename('mc_comm_log').' set status=1 where pid='.$upperUID.' and orderid='.$orderId);
                        } else {
                            //订单内有退货
                            //好像是框架fetchAll的bug，查出来的数据只有一条数据就回返回一维数组
                            $newOrder = array();
                            foreach ($order as $ov) {
                                $newOrder[] = $ov;
                            }
                            $order = $newOrder;

                            //查询退单内商品数量等信息
                            $orderBack = Order_getOrderAftersaleByOrderId($orderId);
                            $neworderBack = array();
                            foreach ($orderBack as $ov) {
                                $neworderBack[] = $ov;
                            }
                            $orderBack = $neworderBack;
                            //设置订单内每个商品退货数量，key是num
                            foreach ($orderBack as $orderBackGoodsDetail) {
                                $order = $this->combineBackNumToOrderDetail($order, $orderBackGoodsDetail);
                            }

                            //更新发放标识
                            pdoT_execute('update '.tablename('mc_comm_log').' set status=1 where pid='.$upperUID.' and orderid='.$orderId);

                            //处理退货商品的提成和业绩
                            $backCredit3 = $credit3results = 0;
                            foreach ($order as $orderGoodsOne) {
                                if ($orderGoodsOne['order_goods_cancle']=='2') {
                                    //减业绩
                                    $credit3results += $orderGoodsOne['price2']*$orderGoodsOne['num'];
                                    //退回商品减提成
                                    $backCredit3One = $orderGoodsOne['comm'.$level]*$orderGoodsOne['num'];
                                    $backCredit3 += $backCredit3One;
                                    //增加减提成记录
                                    $sql = 'INSERT INTO '.tablename('mc_comm_log').'(uid,pid,level,fmoney,orderid,gid,optionid,comm,status)';
                                    $sql.= " VALUES ({$uid},{$pid},{$level},-{$backCredit3One},{$orderId},".$orderGoodsOne['goodsid'].",".$orderGoodsOne['optionid'].",".$orderOne['comm'.$level].",1)";
                                    pdoT_execute($sql);
                                }
                            }
                            //解除冻结余额
                            $sql = 'update '.tablename('mc_members').' set credit3=credit3-'.$backCredit3.',credit3results=credit3results-'.$credit3results.',credit5=credit5-'.$comms[$level];
                            $sql.= ' where uid='.$upperUID;
                            pdoT_execute($sql);
                        }
                    } else if ($add && !$type) {
                        //临时发放佣金（冻结发放的佣金）
                        $credit5 = $creditAdd = 0;
                        foreach ($order as $orderOne) {
                            $credit5 = $orderOne['comm'.$level]*$orderOne['total'];
                            $creditAdd += $credit5;
                            //如果optionid为空(无属性商品)会导致sql事务失败
                            if (!isset($orderOne['optionid']) || $orderOne['optionid']=='') {
                                $orderOne['optionid'] = 0;
                            }
                            //新增佣金发放记录
                            $sql = 'INSERT INTO '.tablename('mc_comm_log').'(uid,pid,level,fmoney,orderid,gid,optionid,comm,status)';
                            $sql.= " VALUES ({$uid},{$upperUID},{$level},{$credit5},{$orderId},".$orderOne['goodsid'].",".$orderOne['optionid'].",".$orderOne['comm'.$level].",0)";
                            pdoT_execute($sql);
                        }
                        //加分成，加冻结分成
                        if ($creditAdd) {
                            $sql = 'update '.tablename('mc_members').' set credit3=credit3+'.$creditAdd.',credit3results=credit3results+'.$orderTotalPrice.',credit5=credit5+'.$creditAdd;
                            $sql.= ' where uid='.$upperUID;
                            pdoT_execute($sql);

                            $upperUserOpenid = Fans_getOpenidByUid($upperUID);

                            //推送消息给上家
                            $text = "特大喜讯传来！\n";
                            $text .= '有'.$creditAdd.'元进入您钱包！您的 '.self::getUpperNickText($level)." {$nickname} 在".date('Y-m-d H:i:s', $orderPayDt).' 购买了'.$goodsTitleTxt.'，快去查看吧！';
                            // addMsgIntoQueen($upperUID, $upperUserOpenid, 1, array('text'=>$text));
                            $ret = $this->sendPost($upperUID, array('text'=>array('content'=>$text)));

                            $text = '消费分成提示！有'.$creditAdd.'元的提成单！是 '.self::getUpperNickText($level)." {$nickname} 在".date('Y-m-d H:i:s', $orderPayDt).' 购买了'.$goodsTitleTxt.'！';
                            // addMsgIntoQueen($upperUID, $upperUserOpenid, 1, array('text'=>$text));
                            $this->sendPostToOpenid('opZZjs3o2cO1w9ztqdF4WEUELbu4', array('text' => array('content' => $text)));
                        }
                    } else if (!$add) {
                        //直接所有回退佣金和业绩，佣金发放历史数据做相应的减操作
                        $commPlusList = pdo_fetchall('SELECT * FROM '.tablename('mc_comm_log').'WHERE orderid=:orderid and level=:level and status=0', array(':orderid'=>$orderId, ':level'=>$level));

                        if (!empty($commPlusList)) {
                            //增加回退记录
                            foreach ($commPlusList as $commPluslog) {
                                $commPluslog['fmoney'] = 0-$commPluslog['fmoney'];
                                $commPluslog['optionid'] = isset($commPluslog['optionid']) ? intval($commPluslog['optionid']) : 0;
                                $sql = 'INSERT INTO '.tablename('mc_comm_log').'(uid,pid,level,fmoney,orderid,gid,optionid,comm,status)';
                                $sql.= " VALUES ('".$commPluslog['uid']."','".$commPluslog['pid']."','{$level}','".$commPluslog['fmoney']."','{$orderId}','".$commPluslog['gid']."','".$commPluslog['optionid']."','".$commPluslog['comm']."',1)";
                                pdoT_execute($sql);
                            }
                            //修改佣金和业绩流水状态为终态(更新佣金发放记录标识)
                            $sql = 'update '.tablename('mc_comm_log').'set status=1 ';
                            $sql.= " where orderid={$orderId} and level=".$level;
                            pdoT_execute($sql);
                            //减业绩,解除冻结余额
                            $sql = 'update '.tablename('mc_members').' set credit3=credit3-'.$comms[$level].',credit3results=credit3results-'.$orderTotalPrice.',credit5=credit5-'.$comms[$level];
                            $sql.= ' where uid='.$upperUID;
                            pdoT_execute($sql);
                        }
                    } else {
                        return false;
                    }
                }
            }
            //如果结算处理成功则在订单表标注，否则默认标记为未处理
            $balanceCreditResult = pdoT_confirm();
            if ($balanceCreditResult) {
                pdo_update('shopping_order', array('creditsettle'=>1), array('id'=>$orderId));
            }
            return $balanceCreditResult;
        }
        return true;
    }

    public function doMobilePay() {
        global $_W, $_GPC;
        $this->checkAuth();
        $orderid = intval($_GPC['orderid']);

        /* 判断订单内商品有没有不能支付的商品(未上线/下架/库存剩余小于订单商品数)
           如果有，删除订单内该商品，并跳转到确认页面等待用户重新确认并支付 */
        load()->model('shopping.order.goods');
        $orderGoods = goods_getOrderGoodsDetailByOrderId($orderid);

        $goodsOff = array();
        foreach ($orderGoods as $orderGoodsOne) {
            //判断下架和开始时间
            if ($orderGoodsOne['status']==0 || $orderGoodsOne['timestart']>time()) {
                $goodsOff[] = $orderGoodsOne;
                //判断库存
            } else if (isset($orderGoodsOne['optionid']) && $orderGoodsOne['optionid']>0) {
                if ($orderGoodsOne['num'] > $orderGoodsOne['stock']) {
                    $goodsOff[] = $orderGoodsOne;
                }
            } else if ($orderGoodsOne['num'] > $orderGoodsOne['total']) {
                $goodsOff[] = $orderGoodsOne;
            }
        }

        $emptyGoodsOrder = false;
        if (count($goodsOff)==count($orderGoods)) {
            $emptyGoodsOrder = true;
            //pdo_update('shopping_order', array('status'=>'-2'), array('orderid'=>$orderid, ':weid'=>$_W['uniacid']));
            include $this->template('goods_not');
            exit();
        } else if (!empty($goodsOff)) {
            //删除订单内下架/未上架商品
            foreach ($goodsOff as $goodsOffOne) {
                $goodsOffOne['optionid'] = $goodsOffOne['optionid'] ? $goodsOffOne['optionid'] : 0;
                $where = array('weid'=>$_W['uniacid'], 'orderid'=>$orderid, 'goodsid'=>$goodsOffOne['goodsid']);
                $where['optionid'] = $goodsOffOne['optionid'];
                pdo_delete('shopping_order_goods', $where);
            }
            //重新计算订单总价
            $newOrderGoods = goods_getOrderGoodsDetailByOrderId($orderid);
            $newOrderPrice = 0;
            foreach ($newOrderGoods as $orderGoodsOne) {
                if ($orderGoodsOne['optionmarketprice']) {
                    $newOrderPrice += $orderGoodsOne['optionmarketprice'] * $orderGoodsOne['num'];
                } else {
                    $newOrderPrice += $orderGoodsOne['price'] * $orderGoodsOne['num'];
                }
            }
            pdo_update('shopping_order', array('goodsprice'=>$newOrderPrice, 'price'=>$newOrderPrice), array('orderid'=>$orderId, ':weid'=>$_W['uniacid']));
            include $this->template('goods_not');
            exit();
        }

        $emptyGoodsOrder = false;

        $order = pdo_fetch("SELECT paytype, addressid, price, ordersn, id, goodstype, status  
                            FROM " . tablename('shopping_order') . " 
                            WHERE id = :id", array(':id' => $orderid));
        if(!$_GPC['price']){
            $price = $order['price'];
        }else{
            $price = $_GPC['price'];
        }
        if ($order['status'] != '0') {
            message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
        }
        if (checksubmit('codsubmit')) {
            $ordergoods = pdo_fetchall("SELECT goodsid, total,optionid FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$orderid}'", array(), 'goodsid');
            if (!empty($ordergoods)) {
                $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total,credit FROM " . tablename('shopping_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
            }
            //邮件提醒
            if (!empty($this->module['config']['noticeemail'])) {
                $address = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $order['addressid']));
                $body = "<h3>购买商品清单</h3> <br />";
                if (!empty($goods)) {
                    foreach ($goods as $row) {
                        //属性
                        $option = pdo_fetch("select title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where id=:id limit 1", array(":id" => $ordergoods[$row['id']]['optionid']));
                        if ($option) {
                            $row['title'] = "[" . $option['title'] . "]" . $row['title'];
                        }
                        $body .= "名称：{$row['title']} ，数量：{$ordergoods[$row['id']]['total']} <br />";
                    }
                }
                $paytype = $order['paytype']=='3'?'货到付款':'已付款';
                $body .= "<br />总金额：{$order['price']}元 （{$paytype}）<br />";
                $body .= "<h3>购买用户详情</h3> <br />";
                $body .= "真实姓名：$address[realname] <br />";
                $body .= "地区：$address[province] - $address[city] - $address[area]<br />";
                $body .= "详细地址：$address[address] <br />";
                $body .= "手机：$address[mobile] <br />";
                load()->func('communication');
                ihttp_email($this->module['config']['noticeemail'], '微商城订单提醒', $body);
            }
            pdo_update('shopping_order', array('status' => '1', 'paytype' => '3'), array('id' => $orderid));
            //增加积分
            // $this->balanceCreditByOrderUpdate($orderid);
            message('订单提交成功，请您收到货时付款！', $this->createMobileUrl('myorder'), 'success');
        }

        if (checksubmit()) {
            if ($order['paytype'] == 1 && $_W['fans']['credit2'] < $price) {
                message('抱歉，您帐户的余额不够支付该订单，请充值！', url('entry', array('m' => 'recharge', 'do' => 'pay')), 'error');
                // message('抱歉，您帐户的余额不够支付该订单，请充值！', create_url('mobile/module/charge', array('name' => 'member', 'weid' => $_W['uniacid'])), 'error');
            }
            if ($price == '0') {
                $this->payResult(array('tid' => $orderid, 'from' => 'return', 'type' => 'credit2'));
                exit;
            }
        }
        // 商品名称
        $sql = 'SELECT a.`title`,b.`total` FROM ' . tablename('shopping_goods') . " a, ".tablename('shopping_order_goods')." b WHERE b.`orderid` = :orderid and b.goodsid = a.id";

        $goodsTitles = pdo_fetchall($sql, array(':orderid' => $orderid));
        $goodsTitle = '';
        if(!empty($goodsTitles)){
            foreach($goodsTitles as $v){
                if(!empty($goodsTitle)){
                    $goodsTitle .= ' 和 '.$v['title'];
                }else{
                    $goodsTitle = $v['title'];
                }
            }
        }
        $params['tid'] = $orderid;
        $params['user'] = $_W['fans']['from_user'];
        $params['fee'] = $price;
        $params['title'] = $goodsTitle;
        $params['ordersn'] = $order['ordersn'];
        $params['virtual'] = $order['goodstype'] == 2 ? true : false;
        $params['goodsTitle'] = $goodsTitles;
        include $this->template('pay');
    }

    /**
     * 订单物流进度查询
     */
    public function doMobileLogistics() {
        global $_GPC, $_W;
        include $this->template('logistics');
    }

    /**
     * 用户查看手工生成的订单
     * @param int id(订单id)
     *
     */
    public function doMobileSeeOrder(){
        global $_W, $_GPC;
        $uid = intval($_W['member']['uid']);
        if(empty($uid)){
            load()->model('mc');
            mc_oauth_userinfo();
        }
        load()->model('shopping.order');
        $orderid = intval($_GPC['id']);
        $item = Order_getMyOrderDetail($orderid);
        if($item['uid'] == $uid){
            $user = mc_fetch($uid, array('credit2'));
        }
        $addressInfo = pdo_fetch('SELECT * FROM '.tablename('shopping_address').' WHERE id = :addressid', array(':addressid' => $item['addressid']));
        include $this->template('seeorder');
    }

    /**
     * 用户取消订单
     */
    public function doMobileCancelorder() {
        global $_W, $_GPC;
        $this->checkAuth();
        $orderid = intval($_GPC['orderid']);
        //$count = pdo_fetchcolumn("SELECT COUNT(uid) FROM " . tablename('shopping_order') . " WHERE id = :orderid and from_user = :user and status = 0", 
        //              array(':orderid' => $orderid, ':user' => $_W['fans']['from_user']));
        $status = 0;
        $ret = pdo_update('shopping_order', array('status'=>-1), array('id'=>$orderid, 'uid'=>$_W['member']['uid'], 'status'=>0));
        if($ret){
            $status = 1;
        }
        echo $status;
        exit();
    }

    /**
     * 用户订单信息
     * @param op {confirm:'确认收货', detail:'订单详细', display:'订单列表'}
     * @param status  订单状态
     * @param orderid 订单ID
     */
    public function doMobileMyOrder() {
        global $_W, $_GPC;
        $this->checkAuth();
        $op = $_GPC['op'];
        load()->model('shopping.order');
        $orderid = intval($_GPC['orderid']);

        if($op == 'confirm') {
            $order = pdo_fetch("SELECT * FROM " . tablename('shopping_order') . " WHERE id = :id AND from_user = :from_user", array(':id' => $orderid, ':from_user' => $_W['fans']['from_user']));
            if(empty($order)){
                message('抱歉，您的订单不存或是已经被取消！', $this->createMobileUrl('myorder'), 'error');
            }
            pdo_update('shopping_order', array('status' => 3,'sendexpress'=>TIMESTAMP), array('id' => $orderid, 'from_user' => $_W['fans']['from_user']));
            message('确认收货完成！', $this->createMobileUrl('myorder'), 'success');
        }else if ($op == 'detail') {
            $item = Order_getMyOrderDetail($orderid);
            $goods = $item['goods'];
            $addressInfo = pdo_fetch('SELECT * FROM '.tablename('shopping_address').' WHERE id = :addressid', array(':addressid' => $item['addressid']));
            // $dispatch = pdo_fetch("select id,dispatchname from " . tablename('shopping_dispatch') . " where id=:id limit 1", array(":id" => $item['dispatch']));
            //761597035880
            include $this->template('order_detail');
        }else{
            $list = Order_getMyOrderList();

            if($_W['isajax']){
                ajaxReturn($list);
            }
            include $this->template('order');
        }
    }

    /**
     * 售后申请（选择）
     *
     */
    public function doMobileAftermarket(){
        global $_W, $_GPC;
        $this->checkAuth();
        $operations = empty($_GPC['op']) ? 'display' : $_GPC['op'];
        if($operations == 'display'){
            load()->model('shopping.order');
            $orderid = intval($_GPC['id']);
            $item = Order_getMyOrderDetail($orderid);
            // pre($item);
        }else if($operations == 'list'){
            /**
             * 售后列表
             */
            load()->model('shopping.order.aftermarket');
            $list = Aftermarket_getList();
        }

        include $this->template('aftermarket');
    }

    /**
     * 售后申请（商品功能操作）
     *
     */
    public function doMobileApplyafter(){
        global $_W, $_GPC;
        $this->checkAuth();
        $operations = $_GPC['op'] ? $_GPC['op'] : 'display';
        if($operations == 'progress'){
            /**
             * 售后确定
             *
             */
            load()->model('shopping.order.aftermarket');
            $id = intval($_GPC['id']);
            if($_W['isajax']){
                $res = Aftermarket_ConfirmOperation();
                $ret = array('status' => 200, 'link'=> url('mc/home'));
                if($res === -101){
                    $ret = array('status' => -200, 'msg'=>'快递单号不能为空！');
                }else if($res === false){
                    $ret = array('status' => -200, 'msg'=>'网络错误，刷新后再操作！');
                }
                ajaxReturn($ret);
            }
            $item = Aftermarket_getItem($id);
            // pre($item);
        }else{
            if($_W['isajax']){
                /**
                 * 售后申请
                 *
                 */
                $ret = array('status' => -200, 'msg' => '异常请求！');
                load()->model('shopping.order.aftermarket');
                $res = Aftermarket_saveInfo();
                if($res === false){
                    $ret['msg'] = '操作失败,订单出现异常！';
                }else if($res){
                    $ret['msg'] = '操作成功！';
                    $ret['status'] = 200;
                    $ret['link'] = url('mc/home/index');
                }else if($res == -404){
                    $ret['msg'] = '图片不能为空!';
                }else{
                    $ret['msg'] = '提交失败！';
                }
                ajaxReturn($ret);
            }else{
                load()->model('shopping.order');
                $orderid = intval($_GPC['id']);
                $ogid = intval($_GPC['ogid']);
                $item = Order_getMyOrderGoodsChoice($orderid, $ogid);
                // pre($item);
                if($item['cancelgoods']){
                    message('该商品正在售后审核中...', referer(), "error");
                }
            }

        }
        include $this->template('apply_after');
    }

    /**
     * 图片上传
     *
     */
    public function doMobileUpload(){
        global $_W, $_GPC;
        // $this->checkAuth();
        load()->func('file');
        $filename = $_FILES['image']['name'];
        $file = file_upload($_FILES['image'], 'image');

        if(isset($file['errno'])){
            ajaxReturn(array('status' => -200, 'msg' => $file['message']));
        }else if(isset($file['success']) && $file['success'] == true){
            pdo_insert('core_attachment', array(
                'uniacid' => $_W['uniacid'],
                'uid' => $_W['member']['uid'],
                'filename' => $filename,
                'attachment' => $file['path'],
                'type' => 1,
                'createtime' => TIMESTAMP,
                'status' => 0
            ));
            ajaxReturn(array('status' => 200, 'path' => $file['path']));
        }else{
            ajaxReturn(array('status' => -200, 'msg' => '上传异常！'));
        }
    }

    public function doMobileAddress() {
        global $_W, $_GPC;
        $this->checkAuth();
        load()->model('shopping.address');

        $from = $_GPC['from'];
        $returnurl = urldecode($_GPC['returnurl']);
        $operation = $_GPC['op'];
        if ($operation == 'post') {
            //判断是新增地址还是修改地址
            $id = intval($_GPC['id']) ? intval($_GPC['id']) : 0;
            //查询有没有默认，已经有的数量
            $addressList = pdo_fetchall('select * from '.tablename("shopping_address")." where openid = '{$_W['fans']['from_user']}' and deleted = 0 order by isdefault desc");

            $data = array(
                'weid' => $_W['uniacid'],
                'openid' => $_W['fans']['from_user'],
                'realname' => $_GPC['realname'],
                'mobile' => $_GPC['mobile'],
                'province' => $_GPC['province'],
                'city' => $_GPC['city'],
                'area' => $_GPC['area'],
                'address' => $_GPC['address']
            );

            //添加第一个地址，强制作为默认地址
            if (empty($addressList) || $addressList[0]['isdefault'] == 0) {
                $data['isdefault'] = 1;
                pdo_insert("shopping_address",$data);
            } else {
                //判断是要设置默认地址
                if ($addressList[0]['isdefault'] == 1) {
                    if(empty($id) && !$id){
                        pdo_insert("shopping_address",$data);
                    }
                }
            }


            if (empty($_GPC['realname']) || empty($_GPC['mobile']) || empty($_GPC['address'])) {
                message('请输完善您的资料！');
            }
            if (!empty($id)) {
                unset($data['weid']);
                unset($data['openid']);
                pdo_update('shopping_address', $data, array('id' => $id));
                message($id, '', 'ajax');
            } else {
                //pdo_update('shopping_address', array('isdefault' => 0), array('weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                $id = pdo_insertid();
                if (!empty($id)) {
                    message($id, '', 'ajax');
                } else {
                    message(0, '', 'ajax');
                }
            }
        } elseif ($operation == 'default') {
            // 设置默认地址
            Address_setDefault();

        } elseif ($operation == 'detail') {
            //获取地址详细
            $id = intval($_GPC['id']);
            $row = Address_getDetailById($id);
            message($row, '', 'ajax');
        } elseif ($operation == 'remove') {
            $id = intval($_GPC['id']);
            if (!empty($id)) {
                $address = pdo_fetch("select isdefault from " . tablename('shopping_address') . " where id='{$id}' and weid='{$_W['uniacid']}' and openid='{$_W['fans']['from_user']}' limit 1 ");
                if (!empty($address)) {
                    //pdo_delete("shopping_address", array('id'=>$id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                    //修改成不直接删除，而设置deleted=1
                    pdo_update("shopping_address", array("deleted" => 1, "isdefault" => 0), array('id' => $id, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                    if ($address['isdefault'] == 1) {

                        //如果删除的是默认地址，则设置是新的为默认地址
                        $maxid = pdo_fetchcolumn("select id from " . tablename('shopping_address') . " where weid='{$_W['uniacid']}' and deleted = 0 and openid='{$_W['fans']['from_user']}' order by id desc LIMIT 1");

                        if (!empty($maxid)) {
                            pdo_update('shopping_address', array('isdefault' => 1), array('id' => $maxid, 'weid' => $_W['uniacid'], 'openid' => $_W['fans']['from_user']));
                            die(json_encode(array("result" => 1, "maxid" => $maxid)));
                        }
                    }
                }
            }
            die(json_encode(array("result" => 1, "maxid" => 0)));
        } elseif($operation == 'add'){

        } elseif($_W['isajax'] && $operation == 'usersign'){
            //签到选择地址
            load()->model('signin');
            $res = setAwardAddr();
            $ret = array('status' => -200, 'msg' => '异常请求，请刷新后再试！');
            if($res === 1){
                $ret = array('status' => 200);
            }else if($res === 0){
                $ret['msg'] = '改奖品已发货！';
            }
            ajaxReturn($ret);
        } else {
            $profile = fans_search($_W['fans']['from_user'], array('resideprovince', 'residecity', 'residedist', 'address', 'realname', 'mobile'));
            $address = Address_getList();
            if($address[0]['isdefault'] == 1 && $address[1]['isdefault'] == 1){
                $fetchListOderId = pdo_fetch("select id from ".tablename("shopping_address")." where openid = :openid and isdefault = 1 order by id desc",array(":openid" => $_W['fans']['from_user']));
                pdo_update("shopping_address",array('isdefault' => 0),array('openid' => $_W['fans']['from_user'],'id' => $fetchListOderId['id']));
            }

            include $this->template('address');
        }
    }
    private function checkAuth() {
        global $_W;
        checkauth();
    }
    private function changeWechatSend($id, $status, $msg = '') {
        global $_W;
        $paylog = pdo_fetch("SELECT plid, openid, tag FROM " . tablename('core_paylog') . " WHERE tid = '{$id}' AND status = 1 AND type = 'wechat'");
        if (!empty($paylog['openid'])) {
            $paylog['tag'] = iunserializer($paylog['tag']);
            $acid = $paylog['tag']['acid'];
            $account = account_fetch($acid);
            $payment = uni_setting($account['uniacid'], 'payment');
            if ($payment['payment']['wechat']['version'] == '2') {
                return true;
            }
            $send = array(
                'appid' => $account['key'],
                'openid' => $paylog['openid'],
                'transid' => $paylog['tag']['transaction_id'],
                'out_trade_no' => $paylog['plid'],
                'deliver_timestamp' => TIMESTAMP,
                'deliver_status' => $status,
                'deliver_msg' => $msg,
            );
            $sign = $send;
            $sign['appkey'] = $payment['payment']['wechat']['signkey'];
            ksort($sign);
            $string = '';
            foreach ($sign as $key => $v) {
                $key = strtolower($key);
                $string .= "{$key}={$v}&";
            }
            $send['app_signature'] = sha1(rtrim($string, '&'));
            $send['sign_method'] = 'sha1';
            $account = WeAccount::create($acid);
            $response = $account->changeOrderStatus($send);
            if (is_error($response)) {
                message($response['message']);
            }
        }
    }
    /**
     * 支付回调
     * @param param['tid'] 订单id
     * $params = array('tid'=>65)
     */
    public function payResult($params) {
        //记录事件日志
        $eventLogData = $params;
        $userInfo = pdo_fetchall('SELECT * FROM '.tablename('mc_members')." where uid=:uid", array(':uid'=>$params['user']));
        $userInfo = isset($userInfo[0]) ? $userInfo[0] : array();
        if (!empty($userInfo)) {
            $eventLogData['uid']            = $eventLogData['uid'];
            $eventLogData['uniacid']        = $eventLogData['uniacid'];
            $eventLogData['mobile']         = $eventLogData['mobile'];
            $eventLogData['groupid']        = $eventLogData['groupid'];
            $eventLogData['credit1']        = $eventLogData['credit1'];
            $eventLogData['credit2']        = $eventLogData['credit2'];
            $eventLogData['credit3']        = $eventLogData['credit3'];
            $eventLogData['credit3results'] = $eventLogData['credit3results'];
            $eventLogData['credit4']        = $eventLogData['credit4'];
            $eventLogData['credit4results'] = $eventLogData['credit4results'];
            $eventLogData['credit5']        = $eventLogData['credit5'];
            $eventLogData['credit6']        = $eventLogData['credit6'];
            $eventLogData['credit7']        = $eventLogData['credit7'];
            $eventLogData['createtime']     = $eventLogData['createtime'];
            $eventLogData['realname']       = $eventLogData['realname'];
            $eventLogData['nickname']       = $eventLogData['nickname'];
            $eventLogData['status']         = $eventLogData['status'];
            $eventLogData['pids']           = getPid($eventLogData['uid'], 3);
        }
        recordEventLog($params['user'], 6, $eventLogData); //第二个参数值参考/app/common/common.func.php中函数定义

        $fee = intval($params['fee']);
        $data = array('status' => $params['result'] == 'success' ? 1 : 0);
        $paytype = array('credit' => '1', 'wechat' => '2', 'alipay' => '2', 'delivery' => '3');
        $data['paytype'] = $paytype[$params['type']];
        $data['paymenttime'] = TIMESTAMP;

        /**
         * 微信支付
         */
        $_SESSION['payResult'] = array('params' => $params, 'expire' => TIMESTAMP);
        // file_put_contents('params.txt', date('Y-m-d H:i:s')."\n\t".json_encodes($params)."\n\n",FILE_APPEND);

        if ($params['type'] == 'wechat') {
            $data['transid'] = $params['tag']['transaction_id'];
        }

        if ($params['type'] == 'delivery') {
            $data['status'] = 1;
        }

        //支付回调
        $rs = pdo_update('shopping_order', $data, array('id' => $params['tid']));
        if($rs){
            $this->balanceCreditByOrderUpdate($params['tid'], true, false);
        }

        if ($params['from'] == 'return') {

            //邮件提醒
            if (!empty($this->module['config']['noticeemail'])) {
                $order = pdo_fetch("SELECT `price`, `paytype`, `from_user`, `addressid` FROM " . tablename('shopping_order') . " WHERE id = '{$params['tid']}'");
                $ordergoods = pdo_fetchall("SELECT goodsid, total FROM " . tablename('shopping_order_goods') . " WHERE orderid = '{$params['tid']}'", array(), 'goodsid');
                $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total FROM " . tablename('shopping_goods') . " WHERE id IN ('" . implode("','", array_keys($ordergoods)) . "')");
                $address = pdo_fetch("SELECT * FROM " . tablename('shopping_address') . " WHERE id = :id", array(':id' => $order['addressid']));
                $body = "<h3>购买商品清单</h3> <br />";
                if (!empty($goods)) {
                    foreach ($goods as $row) {
                        $body .= "名称：{$row['title']} ，数量：{$ordergoods[$row['id']]['total']} <br />";
                    }
                }
                $paytype = $order['paytype'] == '3' ? '货到付款' : '已付款';
                $body .= "<br />总金额：{$order['price']}元 （{$paytype}）<br />";
                $body .= "<h3>购买用户详情</h3> <br />";
                $body .= "真实姓名：{$address['realname']} <br />";
                $body .= "地区：{$address['province']} - {$address['city']} - {$address['area']}<br />";
                $body .= "详细地址：{$address['address']} <br />";
                $body .= "手机：{$address['mobile']} <br />";
                load()->func('communication');
                ihttp_email($this->module['config']['noticeemail'], '微商城订单提醒', $body);
            }

            $setting = uni_setting($_W['uniacid'], array('creditbehaviors'));
            $credit = $setting['creditbehaviors']['currency'];
            if ($params['type'] == $credit) {
                message('支付成功！', $this->createMobileUrl('Single', array('id' => $params['tid'])), 'success');
            } else {
                message('支付成功！', '../../app/' . $this->createMobileUrl('Single', array('id' => $params['tid'])), 'success');
            }
        }
    }
    public function doWebOption() {
        $tag = random(32);
        global $_GPC;
        include $this->template('option');
    }
    public function doWebSpec() {
        global $_GPC;
        $spec = array(
            "id" => random(32),
            "title" => $_GPC['title']
        );
        include $this->template('spec');
    }
    public function doWebSpecItem() {
        global $_GPC;
        load()->func('tpl');
        $spec = array(
            "id" => $_GPC['specid']
        );
        $specitem = array(
            "id" => random(32),
            "title" => $_GPC['title'],
            "show" => 1
        );
        include $this->template('spec_item');
    }
    public function doWebParam() {
        $tag = random(32);
        global $_GPC;
        include $this->template('param');
    }

    public function doWebExpress() {
        global $_W, $_GPC;
        // pdo_query('DROP TABLE ims_shopping_express');
        //pdo_query("CREATE TABLE IF NOT EXISTS `ims_shopping_express` ( `id` int(10) unsigned NOT NULL AUTO_INCREMENT, `weid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所属帐号',  `express_name` varchar(50) NOT NULL COMMENT '分类名称',  `express_price` varchar(10) NOT NULL DEFAULT '0',  `displayorder` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',  `express_area` varchar(50) NOT NULL COMMENT '配送区域',  `enabled` tinyint(1) NOT NULL,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 ");
        //pdo_query("ALTER TABLE  `ims_shopping_order` ADD  `expressprice` VARCHAR( 10 ) NOT NULL AFTER  `totalnum` ;");
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_express'));
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                if (empty($_GPC['express_name']) || empty($_GPC['express_com'])) {
                    message('抱歉，请选择物流公司！');
                }
                $data = array(
                    'displayorder'   => intval($_GPC['displayorder']),
                    'express'        => $_GPC['express_com'],
                    'express_name'   => $_GPC['express_name'],
                    'express_price'  => $_GPC['express_price'],
                    'express_url'    => $_GPC['express_url'],
                    'express_area'   => $_GPC['express_area'],
                );
                if (!empty($id)) {
                    pdo_update('shopping_express', $data, array('id' => $id));
                } else {
                    pdo_insert('shopping_express', $data);
                    $id = pdo_insertid();
                }
                message('更新物流设置成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
            }
            //修改
            $express = pdo_fetch("SELECT * FROM " . tablename('shopping_express') . " WHERE id = '$id'");
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $express = pdo_fetch("SELECT id  FROM " . tablename('shopping_express') . " WHERE id = '$id'");
            if (empty($express)) {
                message('抱歉，物流方式不存在或是已经被删除！', $this->createWebUrl('express', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_express', array('id' => $id));
            message('物流方式删除成功！', $this->createWebUrl('express', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('express', TEMPLATE_INCLUDEPATH, true);
    }

    public function doWebDispatch() {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_dispatch') . " ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => $_W['uniacid'],
                    'displayorder' => intval($_GPC['displayorder']),
                    'dispatchtype' => intval($_GPC['dispatchtype']),
                    'dispatchname' => $_GPC['dispatchname'],
                    'express' => $_GPC['express'],
                    'firstprice' => $_GPC['firstprice'],
                    'firstweight' => $_GPC['firstweight'],
                    'secondprice' => $_GPC['secondprice'],
                    'secondweight' => $_GPC['secondweight'],
                    'description' => $_GPC['description']
                );
                if (!empty($id)) {
                    pdo_update('shopping_dispatch', $data, array('id' => $id));
                } else {
                    pdo_insert('shopping_dispatch', $data);
                    $id = pdo_insertid();
                }
                message('更新配送方式成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
            }
            //修改
            $dispatch = pdo_fetch("SELECT * FROM " . tablename('shopping_dispatch') . " WHERE id = '$id'");
            $express = pdo_fetchall("select * from " . tablename('shopping_express') . " ORDER BY displayorder DESC");
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $dispatch = pdo_fetch("SELECT id FROM " . tablename('shopping_dispatch') . " WHERE id = '$id'");
            if (empty($dispatch)) {
                message('抱歉，配送方式不存在或是已经被删除！', $this->createWebUrl('dispatch', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_dispatch', array('id' => $id));
            message('配送方式删除成功！', $this->createWebUrl('dispatch', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('dispatch', TEMPLATE_INCLUDEPATH, true);
    }

    /**
     * 仓库管理
     */
    public function doWebWarehouse() {
        global $_W, $_GPC;
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_warehouse') . " ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                $data = array(
                    'name'         => $_GPC['warehousename'], 
                    'status'       => intval($_GPC['status']),
                    'declaration'  => intval($_GPC['declaration']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'description'  => $_GPC['description']
                );
                if ($id) {
                    pdo_update('shopping_warehouse', $data, array('id'=>$id) );
                } else {
                    pdo_insert('shopping_warehouse', $data);
                    $id = pdo_insertid();
                }
                message('更新仓库信息成功！', $this->createWebUrl('warehouse', array('op'=>'display')), 'success');
            }
            //修改
            $warehouse = pdo_fetch("SELECT * FROM " . tablename('shopping_warehouse') . " WHERE id = '$id'");
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $warehouse = pdo_fetch("SELECT id FROM " . tablename('shopping_warehouse') . " WHERE id = '$id'");
            if (empty($warehouse)) {
                message('抱歉，仓库信息不存在或是已经被删除！', $this->createWebUrl('warehouse', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_warehouse', array('id' => $id));
            message('仓库信息删除成功！', $this->createWebUrl('warehouse', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('warehouse', TEMPLATE_INCLUDEPATH, true);
    }

    /**
     * 品牌管理
     */
    public function doWebbranding() {
        global $_W, $_GPC;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_brand') . " ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $name = $_GPC['brandname'];
            if (checksubmit('submit')) {
                //品牌名是主键，需要检查品牌是否已经存在
                $ifExistBranding = pdo_fetchcolumn('SELECT brand FROM '.tablename('shopping_brand').' WHERE brand=:brand', array(
                            ':brand'=>$_GPC['brandingname']
                    ));
                if ($ifExistBranding) {
                    message($_GPC['brandingname'].' -该品牌已经存在，操作失败！', $this->createWebUrl('branding', array('op'=>'display')), 'success');
                }

                $data = array(
                    'brand' => $_GPC['brandingname'], 
                    'spellname' => $_GPC['spellname'], 
                    'fullname' => $_GPC['fullname'], 
                    'brandimg' => $_GPC['brandimg'], 
                    'brandurl' => $_GPC['brandurl'], 
                    'displayorder' => intval($_GPC['displayorder']),
                    'content' => $_GPC['content']
                );
                if ($name) {
                    pdo_update('shopping_brand', $data, array('brand'=>$name) );
                } else {
                    pdo_insert('shopping_brand', $data);
                    $name = pdo_insertid();
                }
                message('更新品牌信息成功！', $this->createWebUrl('branding', array('op'=>'display')), 'success');
            }
            //修改
            $branding = pdo_fetch("SELECT * FROM " . tablename('shopping_brand') . " WHERE brand = '$name'");
        } elseif ($operation == 'delete') {
            // $name = $_GPC['brandname'];
            // $branding = pdo_fetch("SELECT brand FROM " . tablename('shopping_brand') . " WHERE brand = '$name'");
            // if (empty($branding)) {
            //  message('抱歉，品牌信息不存在或是已经被删除！', $this->createWebUrl('branding', array('op' => 'display')), 'error');
            // }
            // pdo_delete('shopping_brand', array('brand' => $namename));
            message('品牌信息不能删除！', $this->createWebUrl('branding', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('branding', TEMPLATE_INCLUDEPATH, true);
    }

    public function doWebAdv() {
        global $_W, $_GPC;
        load()->func('tpl');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        if ($operation == 'display') {
            $list = pdo_fetchall("SELECT * FROM " . tablename('shopping_adv') . " WHERE weid = '{$_W['uniacid']}' ORDER BY displayorder DESC");
        } elseif ($operation == 'post') {
            $id = intval($_GPC['id']);
            if (checksubmit('submit')) {
                $data = array(
                    'weid' => $_W['uniacid'],
                    'advname' => $_GPC['advname'],
                    'link' => $_GPC['link'],
                    'enabled' => intval($_GPC['enabled']),
                    'displayorder' => intval($_GPC['displayorder']),
                    'thumb'=>$_GPC['thumb']
                );
                if (!empty($id)) {
                    pdo_update('shopping_adv', $data, array('id' => $id));
                } else {
                    pdo_insert('shopping_adv', $data);
                    $id = pdo_insertid();
                }
                message('更新幻灯片成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
            }
            $adv = pdo_fetch("select * from " . tablename('shopping_adv') . " where id=:id and weid=:weid limit 1", array(":id" => $id, ":weid" => $_W['uniacid']));
        } elseif ($operation == 'delete') {
            $id = intval($_GPC['id']);
            $adv = pdo_fetch("SELECT id FROM " . tablename('shopping_adv') . " WHERE id = '$id' AND weid=" . $_W['uniacid'] . "");
            if (empty($adv)) {
                message('抱歉，幻灯片不存在或是已经被删除！', $this->createWebUrl('adv', array('op' => 'display')), 'error');
            }
            pdo_delete('shopping_adv', array('id' => $id));
            message('幻灯片删除成功！', $this->createWebUrl('adv', array('op' => 'display')), 'success');
        } else {
            message('请求方式不存在');
        }
        include $this->template('adv', TEMPLATE_INCLUDEPATH, true);
    }
    public function doMobileAjaxdelete() {
        global $_GPC;
        $delurl = $_GPC['pic'];
        if (file_delete($delurl)) {
            echo 1;
        } else {
            echo 0;
        }
    }


    /**
     * 运营管理
     * 1.插入订单
     * 2.自动化订单
     *
     */
    public function doWebOperations (){
        global $_GPC, $_W,$_CONTENT;
        load()->model('shopping.goods');
        load()->model('shopping.order');
        load()->func('tpl');
        $do = $_GPC['do'] ? $_GPC['do']:'operations';
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $ret = array('status'=>-100, 'msg'=>'请求异常！');
        // $a = Goods_getGoodsSpec(44);
        $condition = "weid = :weid and deleted = 0 and status = 1";
        $params[':weid'] = $_W['uniacid'];
        $search_GoodsId = empty($_GPC['goodsid']) ? 0 : intval($_GPC['goodsid']);
        $search_Title = empty($_GPC['title']) ? "" : strip_tags(str_replace(" ","",$_GPC['title']));
        if($search_GoodsId){
            $condition .= " and id = :goodsid";
            $params[':goodsid'] = $search_GoodsId;
        }
        if($search_Title){
            $title_arr = explode("|", $search_Title);
            if(count($title_arr) > 1){
                foreach ($title_arr as $key => $value) {
                    $keywords .= "`title` LIKE '%".$value."%' OR ";
                }
                $keywords = substr($keywords, 0,-4);
                $condition .= " AND (".$keywords .") ";
            } else {
                $condition .= " AND `title` LIKE '%".$title_arr[0]."%'";
            }
        }
        //echo ($pindex - 1)     * $psize . ',' . $psize;exit;
        // $total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("shopping_goods")." WHERE weid = '{$_W['uniacid']}' and deleted = 0");
        // $list = pdo_fetchall("SELECT taa.*,tbb.stock FROM ".tablename("shopping_goods")." 
        //                      as taa left join ".tablename("shopping_goods_option")." as tbb on taa.id = tbb.goodsid WHERE 
        //                      weid = '{$_W['uniacid']}' and deleted=0 GROUP BY status DESC, displayorder DESC, id DESC LIMIT 6" );

        $limit = $title_arr ? 1000 : 10;
        $list = pdo_fetchall("SELECT * FROM ".tablename("shopping_goods")." 
                         WHERE {$condition} GROUP BY status DESC, displayorder DESC, id DESC LIMIT {$limit}",$params);
        foreach ($list as $key => &$value) {
            $list[$key]['options'] = pdo_fetchall("SELECT id,stock FROM ".tablename('shopping_goods_option')." WHERE goodsid = :goodsid",array(':goodsid' => $value['id']));
            if(!empty($list[$key]['options'])){
                foreach ($list[$key]['options'] as $ms => $ls) {
                    $list[$key]['optionsAllTotal'] += $ls['stock'];
                }
            }
            foreach($title_arr as $_title){
                $value['title'] = str_replace($_title,'<span style="color:red">'.$_title.'</span>',$value['title']);
            }
        }
        $pager = pagination($total, $pindex, $psize);
        $endtime = time();
        /***全记录总额****/
        $WholeSumTotal = pdo_fetchcolumn("SELECT SUM(`price`) FROM ".tablename('shopping_order')." WHERE `status` >= 1 AND (`cancelgoods` = 0 OR `cancelgoods` = 2)");
        /****今日付款总额*****/
        $starttime = mktime(0,0,0,date('m'),date('d'),date('Y'));
        $sendtime = time();
        $todaySumTotal = pdo_fetchcolumn("SELECT SUM(`price`) FROM ".tablename('shopping_order')." WHERE 
                                        `status` >= 1 AND (`cancelgoods` = 0 OR `cancelgoods` = 2) AND createtime >= {$starttime} AND createtime <= {$sendtime}");
        /****昨日订单付款总额*****/

        $yestodaytime = mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $yendtime = mktime(0,0,0,date('m'),date('d'),date('Y'));
        // echo date("Y-m-d H:i:s" ,$yestodaytime);
        $yestodaySumTotal = pdo_fetchcolumn("SELECT SUM(`price`) FROM ".tablename("shopping_order")." WHERE 
                                            `status` >= 1 AND (`cancelgoods` = 0 OR `cancelgoods` = 2) AND createtime >= {$yestodaytime} AND createtime <= {$yendtime}");
        // /****一周的订单付款总额*****/
        $aweektime = mktime(0,0,0,date('m'),date('d')-date('w')+1-4,date('Y'));
        $aWeekSumTotal = pdo_fetchcolumn("SELECT SUM(`price`) FROM ".tablename("shopping_order")." WHERE 
                                        `status` >= 1 AND (`cancelgoods` = 0 OR `cancelgoods` = 2) AND createtime >= {$aweektime} AND createtime <= {$endtime}");

        /******订单总额搜索******/
        if($_GPC['type'] == 'search'){
            $message = array();
            if(is_array($_GPC['time'][0]) && !empty($_GPC['time'][0])){
                $timesTamp = $_GPC['time'][0];
                $dataTime = array(
                    'sYear' => ((int)substr($timesTamp['starttime'],0,4)),
                    'sMonth' => ((int)substr($timesTamp['starttime'],5,2)),
                    'sDay' => ((int)substr($timesTamp['starttime'],8,2))
                );

                $begintime = mktime(0,0,0,$dataTime['sMonth'],$dataTime['sDay'],$dataTime['sYear']);

                if($timesTamp['endtime'] == date('Y-m-d')){
                    $oldtime = time();
                } else {
                    $oldtime = strtotime($timesTamp['endtime']);
                }

                if($starttime && $endtime):
                    $res = pdo_fetchcolumn("SELECT SUM(`price`) FROM ".tablename("shopping_order")." WHERE 
                                         `status` >= 1 AND (`cancelgoods` = 0 OR `cancelgoods` = 2) AND createtime >= {$begintime} AND createtime <= {$oldtime}");

                    if($res){
                        $message['status'] = 200;
                        $message['dataTotal'] = $res;
                        $message['sumTotal'] = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename("shopping_order")." WHERE 
                                                `status` >= 1 AND (`cancelgoods` = 0 OR `cancelgoods` = 2) AND createtime >= {$begintime} AND createtime <= {$oldtime}");
                    } else {
                        $message['status'] = -200;
                        $message['msc'] = "没有查找到记录！";
                    }
                else:
                    $message['status'] = -200;
                    $message['msc'] = "时间格式错误！";
                endif;
            } else {
                $message['status'] = -200;
                $message['msc'] = "参数不正确！";
            }
            ajaxReturn($message);
        }

        if($operation == 'inserOrder'){
            $uid = $_GPC['uid'];
            $page = $_GPC['page'];
            load()->model('shopping.address');
            load()->model('mc');

            $result_address = getUserDefaultAddress($uid);
            //var_dump($result_address);exit;
            $mc_member = mc_fetch($uid,array("qq",'mobile','nickname'));
            $profile = mc_fetch($uid);
            //pre($mc_member);exit;
            if(checksubmit('submit')){
                /**
                 * 插入订单
                 * 需求：用户(id,addressid,) 产品（goodsid, optionid）
                 */
                if(!empty($_GPC['normal'])){

                    $checkBox = $_GPC['checkbox']=='' ? false : $_GPC['checkbox'];                  //选择的商品
                    $for_remark = empty($_GPC['for-remark']) ? '云吉良品' : $_GPC['for-remark'];    //备注
                    $dispatchid = intval($_GPC['dispatch']) ? : 6;
                    $_CONTENT['dispatchid'] = $dispatchid;
                    $_CONTENT['uid'] = $uid;
                    // $_CONTENT['expresscom'] = $select_index;
                    // $_CONTENT['express'] = $express_name;

                    $data = array();

                    if($_GPC['select-address'] == 'default'){
                        $adId = empty($_GPC['radio_info']) ? 0 : intval($_GPC['radio_info']);
                    }
                    if($_GPC['select-address'] == 'auto'){
                        $mc_nickname = $_GPC['auto_nickname'];
                        $mc_mobile =  $_GPC['auto_this_mobile'];
                        $mc_qq = $_GPC['auto_this_qq'];
                        $real_name = $_GPC['auto_realname'];
                        $real_mobile = $_GPC['auto_real_mobile'];
                        $real_address = $_GPC['auto_real_address'];
                        $status = "auto";
                        $Users = array('uid'=>$uid,'province'=>$_GPC['reside']['province'],'city'=>$_GPC['reside']['city'],'district'=>$_GPC['reside']['district'],'mc_nickname'=>$mc_nickname,'mc_mobile'=>$mc_mobile,'mc_qq'=>$mc_qq,'real_name'=>$real_name,
                            'real_mobile'=>$real_mobile,'real_address'=>$real_address,'status'=>$status);
                        //获取用户地址ID
                        $adId = Address_PostUserAddress($Users);
                    }

                    foreach ($checkBox as $key => $value) {
                        $SpecV = 'Spec-vals-'.$value;
                        $TotalV = 'select_total-'.$value;
                        if ($_GPC[$TotalV]) {
                            $data[] = array('id'=>$value, 'opid'=>$_GPC[$SpecV],'select_total'=>$_GPC[$TotalV]); //商品规格
                        }
                    }

                    if(empty($data)){
                        message("该商品库存不足！");
                    }
                    // insert ims_shopping_order 的data
                    // $info = array(
                    //  'for-remark'=>$for_remark
                    //  );

                    // $item = pdo_fetch("select id,thumb,title,weight,marketprice,total,type,totalcnf,sales,unit,istime,timeend from " .
                    //      tablename("shopping_goods") . " where weid = {$_W['uniacid']} and isbrush = 1 ORDER BY RAND()");
                    // if(!$item){
                    //      message('没有可以刷单产品！', $this->createWebUrl('goods'), 'danger');
                    //  }





                    //var_dump($data);exit;
                    //$mc_total = pdo_fetchall("select id,total from ".tablename("shopping_goods")."where weid = {$_W['weid']} and id in({$item_lists['goodsid']})");
                    //根据goodsid遍历减库存
                    $lists = array();
                    //var_dump($data);exit;
                    foreach ($data as $goods) {
                        if($goods['opid']==""){
                            $where = array(':goodsid' => $goods['id']);
                            $trs = "";
                        }else{
                            $where = array(':goodsid' => $goods['id'], ':optionid' => $goods['opid']);
                            $trs = " and tbb.specs = :optionid";
                        }
                        //echo "SELECT tba.id as goodsid,tbb.id as optionid,tba.total,tba.totalcnf,".$marketprice.",tbb.stock FROM ";
                        $goodsDetail = pdo_fetch("SELECT
                                                        tba.id as gid, tba.total gtotal, tba.totalcnf, tba.marketprice gprice,
                                                        tbb.id as optionid, tbb.marketprice oprice, tbb.weight oweight, tbb.title oname,tbb.stock ostock 
                                                      FROM ".tablename("shopping_goods").' AS tba LEFT JOIN '.tablename("shopping_goods_option").' AS tbb 
                                                    ON tba.id = tbb.goodsid 
                                                    WHERE tba.id = :goodsid '.$trs, $where);

                        $dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("shopping_dispatch") . " order by displayorder desc");
                        //根据optionid更新有规格记录的商品库存
                        if (!empty($goodsDetail['optionid']) && !empty($goodsDetail['oname'])){
                            if($goodsDetail['ostock'] < $goods['select_total']){
                                message('商品规格库存不足！');
                            }
                            // $result = getOrderDispatChprice($dispatch,$goodsDetail,"ostock");
                        } else {
                            //没有规格的商品直接更新商品表库存
                            if($goodsDetail['gtotal'] < $goods['select_total']){
                                message('商品库存不足！');
                            }
                            // $result = getOrderDispatChprice($dispatch,$goodsDetail,"total");
                        }
                        $goodsDetail['select_total'] = $goods['select_total'];
                        $lists[] = $goodsDetail;
                    }

                    // foreach ($lists as $key) {

                    //  $d = pdo_insert("shopping_order",array('weid'=>$key['weid'],'from_user'=>$key['from_user']
                    //      ,'uid'=>$key['uid'],'ordersn'=>$key['ordersn'],'price'=>$key['price'],'dispatchprice'=>$key['dispatchprice'],
                    //      'goodsprice'=>$key['goodsprice'],'status'=>$key['status'],'sendtype'=>$key['sendtype'],'dispatch'=>$key['dispatch'],
                    //      'remark'=>$key['remark'],'addressid'=>$key['addressid'], 'createtime'=>$key['createtime'],'paytype'=>$key['paytype'],
                    //      'transid'=>$key['transid']));

                    // }

                    $openid = pdo_fetchcolumn('select openid from '.tablename('mc_mapping_fans').' where uid = :uid', array(':uid' => $uid));
                    if(empty($openid)){
                        $openid = $uid;
                    }
                    $dispatchprice = 0;
                    $goodsprice = 0;
                    $orderoption = array();
                    foreach($lists as $v){
                        if(!empty($v['optionid'] && !empty($v['oname']))){
                            $goodsprice += $v['oprice'] * $v['select_total'];
                            $orderoption[] = array(
                                'weid' => $_W['uniacid'],
                                'goodsid' => $v['gid'],
                                'total' => $v['select_total'],
                                'price' => $v['oprice'],
                                'createtime' => TIMESTAMP,
                                'optionid' => $v['optionid'],
                                'optionname' => $v['oname']
                            );
                        }else{
                            $goodsprice += $v['gprice'] * $v['select_total'];
                            $orderoption[] = array(
                                'weid' => $_W['uniacid'],
                                'goodsid' => $v['gid'],
                                'total' => $v['select_total'],
                                'price' => $v['gprice'],
                                'createtime' => TIMESTAMP,
                                'optionid' => 0,
                                'optionname' => ''
                            );
                        }
                    }

                    $price = $goodsprice;
                    load()->model('shopping.goods');

                    $orderInfo = array(
                        'weid' => $_W['uniacid'],
                        'from_user' => $openid,
                        'uid' => $uid,
                        'ordersn' => randNumByOrder(5, 1, 1),
                        'price' => $price ,
                        'dispatchprice' => $dispatchprice,
                        'goodsprice' => $goodsprice,
                        'status' => 0,
                        'sendtype' => 1,
                        'dispatch' => $dispatchid,
                        'goodstype' => 0,
                        'remark' => $for_remark,
                        'addressid' => $adId,
                        'createtime' => TIMESTAMP,
                        'paytype' => 1,
                        'transid' => 0
                    );

                    pdo_insert('shopping_order', $orderInfo);
                    $orderid = pdo_insertid();
                    foreach($orderoption as $option){
                        $option['orderid'] = $orderid;
                        pdo_insert('shopping_order_goods', $option);
                    }

                    message('提交订单成功, 现在跳转到页面...', url('site/entry/order', array('op'=>'detail','id'=>$orderid,'m'=>'ewei_shopping')),'success');

                }else{

                    $sendtype=1;                                                //是否自提

                    $addressid = $_GPC['addressid'];
                    $goodsid = $_GPC['goodsid'];
                    $optionid = $_GPC['optionid'];
                    $dispatchid = intval($_GPC['dispatch']) ? : 6;
                    $total = !empty($_GPC['total']) ? $_GPC['total'] : 1;
                    $remark = $_GPC['remark'] ? $_GPC['remark'] : '云吉良品';
                    /**
                     * 测试的默认值
                     */
                    // $uid = 1;
                    // $goodsid = 47;
                    // $optionid = 61;
                    // $dispatchid = 6;
                    // $total = 1;


                    if(!empty($uid)){
                        if(empty($addressid)){
                            load()->model('shopping.address');
                            $addressid = getUserDefaultAddressId($uid);         //获取默认地址
                        }
                        $item = pdo_fetch("select id,thumb,title,weight,marketprice,total,type,totalcnf,sales,unit,istime,timeend from " .
                            tablename("shopping_goods") . " where weid = {$_W['uniacid']} and isbrush = 1 ORDER BY RAND()");
                        if(!$item){
                            message('没有可以刷单产品！', $this->createWebUrl('goods'), 'danger');
                        }
                        /**
                         * 商品限购时间判断判断
                         */
                        if($item['istime'] == 1 && time() > $item['timeend']){

                        }

                        /**
                         * 产品规格
                         * @param optionid不存在-->随机获取一个
                         */
                        $option = pdo_fetch("select id,title,marketprice,weight,stock from " . tablename("shopping_goods_option") . " where goodsid=:goodsid ORDER BY RAND()", array(":goodsid" => $item['id']));

                        if($option){
                            $item['optionid'] = $option['id'];
                            $item['title'] = $item['title'];
                            $item['optionname'] = $option['title'];
                            $item['marketprice'] = $option['marketprice'];
                            $item['weight'] = $option['weight'];
                        }

                        $item['stock'] = $item['total'];        //设置库存
                        $item['total'] = $total;                //设置购买的数量
                        $item['totalprice'] = $total * $item['marketprice'];        //购买的总价格
                        $allgoods[] = $item;
                        $totalprice+= $item['totalprice'];

                        if ($item['type'] == 1) {
                            $needdispatch = true;
                        }
                        $dispatch = pdo_fetchall("select id,dispatchname,dispatchtype,firstprice,firstweight,secondprice,secondweight from " . tablename("shopping_dispatch") . " order by displayorder desc");

                        foreach ($dispatch as &$d) {
                            $weight = 0;
                            foreach ($allgoods as $g) {
                                $weight+=$g['weight'] * $g['total'];
                            }
                            $price = 0;
                            if ($weight <= $d['firstweight']) {
                                $price = $d['firstprice'];
                            } else {
                                $price = $d['firstprice'];
                                $secondweight = $weight - $d['firstweight'];
                                if ($secondweight % $d['secondweight'] == 0) {
                                    $price+= (int) ( $secondweight / $d['secondweight'] ) * $d['secondprice'];
                                } else {
                                    $price+= (int) ( $secondweight / $d['secondweight'] + 1 ) * $d['secondprice'];
                                }
                            }
                            $d['price'] = $price;
                        }

                        //商品价格
                        $goodsprice = 0;
                        foreach ($allgoods as $row) {
                            // if ($item['stock'] != -1 && $row['total'] > $item['stock']) {
                            // message('抱歉，“' . $row['title'] . '”此商品库存不足！', $this->createMobileUrl('confirm'), 'error');
                            // }
                            $goodsprice+= $row['totalprice'];
                        }

                        //运费
                        $dispatchprice = 0;
                        foreach ($dispatch as $d) {
                            if($dispatchid){
                                if ($d['id'] == $dispatchid) {
                                    $dispatchprice = $d['price'];
                                    $sendtype = $d['dispatchtype'];
                                }
                            }else{
                                $dispatchid = $d['id'];
                                $dispatchprice = $d['price'];
                                $sendtype = $d['dispatchtype'];
                            }

                        }

                        $data = array(
                            'weid' => $_W['uniacid'],
                            'from_user' => $uid,
                            'uid' => $uid,
                            'ordersn' => randNumByOrder(5, 1, 2),
                            'price' => $goodsprice + $dispatchprice,
                            'dispatchprice' => $dispatchprice,
                            'goodsprice' => $goodsprice,
                            'status' => 1,
                            'sendtype' =>intval($sendtype),
                            'dispatch' => $dispatchid,
                            'goodstype' => 0,
                            'remark' => $remark,
                            'addressid' => $addressid,
                            'createtime' => TIMESTAMP,
                            'paytype' => 2,
                            'transid' => TIMESTAMP
                        );
                        pdo_insert('shopping_order', $data);
                        $orderid = pdo_insertid();

                        foreach ($allgoods as $row) {
                            if (empty($row)) {
                                continue;
                            }
                            $d = array(
                                'weid' => $_W['uniacid'],
                                'goodsid' => $row['id'],
                                'orderid' => $orderid,
                                'total' => $row['total'],
                                'price' => $row['marketprice'],
                                'createtime' => TIMESTAMP,
                                'optionid' => $row['optionid']
                            );

                            $o = pdo_fetch("select title from ".tablename('shopping_goods_option')." where id=:id ",array(":id"=>$row['optionid']));
                            if(!empty($o)){
                                $d['optionname'] = $o['title'];
                            }
                            pdo_insert('shopping_order_goods', $d);
                        }
                        $this->setOrderStock($orderid);   //设置库存
                        $this->balanceCreditByOrderUpdate($orderid, true, false);    //佣金更新

                        // message('提交订单成功, 现在跳转到页面...', url('site/entry/order', array('m'=>'ewei_shopping','op'=>'display')),'success');
                        message('提交订单成功, 现在跳转到页面...', url('mc/huiyuan', array('page'=>$page)),'success');

                    }else{
                        message('用户参数异常！', $this->createWebUrl('operations', array('op' => 'inserOrder','uid'=>$uid)), 'danger');
                    }
                }
            }else{
                load()->model('mc');
                $userInfo = mc_fetch($uid, array('pid','nickname','avatar','createtime'));
                //echo "<pre>";print_r($userInfo);exit;
                $userInfo['parent'] = mc_fetch($userInfo['pid'], array('nickname','avatar','createtime'));

            }

        }

        include $this->template('operations');
    }

    /**
     * 签到&签到活动产品管理
     *
     */
    public function doWebSigninGoods(){
        global $_GPC, $_W;
        load()->func('tpl');
        load()->model('shopping.goods');
        load()->model('activity.goods');
        $do = $_GPC['do'];
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $ret = array('status'=>200, 'msg'=>'已执行请查看！','link' => url('site/entry/SigninGoods', array('m'=>'ewei_shopping')));
        if($operation == 'handle'){
            if($_W['isajax']){
                Sign_saveSignRule();
                ajaxReturn($ret);
            }else{
                $goods = Goods_getGoodsToSignAddGoods();
            }
        }elseif ($operation == 'display'){
            $res = Sign_getSignAwardRule();
            if($res){
                $list = $res['list'];
                $pager = $res['pager'];
                $total = $res['total'];
            }

        }
        include $this->template('signgoods');
    }

    /**
     * 获取签到所需要的奖品
     * @param page
     */
    public function doWebApiSigninGoods(){
        global $_GPC, $_W;
        if($_W['isajax']){
            load()->model('shopping.goods');
            $list = Goods_getGoodsToSignAddGoods();
            ajaxReturn($list);
        }
    }

    /**
     * 红包活动管理
     * @param op 操作类型
     * @param id 红包活动表ID
     */
    public function doWebRedpacket (){
        global $_GPC, $_W;
        load()->func('tpl');
        load()->model('activity.redpacket');
        $do = $_GPC['do'];
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $id = intval($_GPC['id']);
        if($operation == 'display'){
            $res = getActivity();

        }else if($operation == 'delete'){
            if(pdo_update('activity_redpacket', array('deleted' => 1),array('fid'=>$id))){
                message('该红包活动已删除，请注意查看',referer());
            }else{
                message('删除操作失败，可能红包活动不存在！',referer(),'error');
            }
        }else if($operation == 'add'){
            if(checksubmit('submit')){
                //保存红包信息（增&改）
                Redpacket_saveInfo($id, $this->createWebUrl('redpacket', array('op' => 'display')));
            }
            $res = getInfoById($id);
        }else if($operation == 'setStatus'){
            $status = intval($_GPC['status']);
            $res = Redpacket_setStatus($id, $status);
            if($res == 1){
                $ret = array('status'=>200);
            }else{
                $ret = array('status'=>-200);
            }
            ajaxReturn($ret);
        }
        include $this->template('redpacket');
    }

    /**
     * 推送信息
     * @param uid
     * @param message = array('touser','msgtype','text'=>array('content'));
     * @desc sendPost($key,array('text'=>array('content'=>'')))
     */
    public function sendPost($uid,$messages=array()){
        global $_W;
        if(require_once '../framework/bootstrap.inc.php'){

        }elseif(require_once '../../framework/bootstrap.inc.php'){

        }
        $openid = pdo_fetch('select openid from '.tablename('mc_mapping_fans').' where `uid` = :uid' ,array(':uid'=>$uid));
        // var_dump($openid);
        // file_put_contents('sendPost.txt', date('Y-m-d H:i:s')."\n\t".$this->json_en($openid)."\n\t".$this->json_en($messages)."\n\t",FILE_APPEND);
        if(!empty($openid['openid'])){
            return $this->sendPostToOpenid($openid['openid'], $messages);
        }return '{"没有"}';
    }

    /**
     * 微信推送
     * @param str openid
     */
    public function sendPostToOpenid($openid='', $messages = array()){
        global $_W;
        $messages['touser'] = $openid;
        if(!isset($messages['msgtype'])){
            $messages['msgtype']='text';
        }
        $messages['text'] = $messages['text'];

        $acid = $_W['account']['uniacid'];
        $account = WeAccount::create($acid);
        $ret = $account->sendCustomNotice($this->json_en($messages),false);

        return $ret;
    }

    /**
     * json编译
     * @param array arr
     */
    private function json_en($arr) {
        $parts = array ();
        $is_list = false;
        //Find out if the given array is a numerical array
        $keys = array_keys ( $arr );
        $max_length = count ( $arr ) - 1;
        if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
            $is_list = true;
            for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
                if ($i != $keys [$i]) { //A key fails at position check.
                    $is_list = false; //It is an associative array.
                    break;
                }
            }
        }
        foreach ( $arr as $key => $value ) {
            if (is_array ( $value )) { //Custom handling for arrays
                if ($is_list)
                    $parts [] = $this->json_en ( $value ); /* :RECURSION: */
                else
                    $parts [] = '"' . $key . '":' . $this->json_en ( $value ); /* :RECURSION: */
            } else {
                $str = '';
                if (! $is_list)
                    $str = '"' . $key . '":';
                //Custom handling for multiple data types
                if (is_numeric ( $value ) && $value<2000000000)
                    $str .= $value; //Numbers
                elseif ($value === false)
                    $str .= 'false'; //The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes ( $value ) . '"'; //All other things
                // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
                $parts [] = $str;
            }
        }
        $json = implode ( ',', $parts );
        if ($is_list)
            return '[' . $json . ']'; //Return numerical JSON
        return '{' . $json . '}'; //Return associative JSON
    }

    private static function getUpperNickText( $level ){
        switch ($level) {
            case '1': $text = '富一代'; break;
            case '2': $text = '富二代'; break;
            case '3': $text = '富三代'; break;
            default:  $text = '无名氏'; break;
        }
        return $text;
    }

    /**
     * 正常订单完单
     *    订单记录cancelgoods为0则是正常完成的订单
     *
     * @param $orderId int 订单号
     * return bool
     *
     */
    private function finishGeneralOrder($orderId) {
        load()->model('shopping.order');
        $orderDetail = Order_getOrderGoodsByOrderId($orderId);
        //空订单
        if (empty($orderDetail)) {
            return false;
        }

        //遍历检查订单是否有退换货商品、收货没到七天
        foreach ($orderDetail as $order) {
            if ($order['cancelgoods'] || ($order['receipttime'])>time()) {
                return false;
            }
        }

        //正式发放分成奖励并修改订单为最终完成状态
        if($this->balanceCreditByOrderUpdate($orderId, true, true)){
            return pdo_update('shopping_order', array('accomplish'=>'1'), array('id' => $orderId));
        } else {
            return false;
        }
    }

    /**
     * 售后订单完单
     *    订单记录cancelgoods为1则是申请售后的订单，需要检查订单内是否有退换货商品
     *  对于退换货订单处理，将不做二次发货以及操作时间间隔检查(逻辑在foreach中)
     * @param $orderId int 订单号
     * return bool
     */
    private function finishCustomerOrder($orderId) {
        load()->model('shopping.order');
        $orderDetail = Order_getOrderAftersaleByOrderId($orderId);

        if (!empty($orderDetail)) {
            // foreach ($orderDetail as $reExpressGoods) {
            //  //type为换货，检查重新发货时间是否已经过了7天
            //  if ($reExpressGoods['type']=='1'){
            //      if(!$reExpressGoods['saexpresstime'] || ($reExpressGoods['saexpresstime']+86400*7)>time() ) {
            //          return false;
            //      }
            //  } else if ($reExpressGoods['type']=='2') {
            //  //type退货，检查客户发货是否已经过了7天
            //      if(!$reExpressGoods['expresstime'] || ($reExpressGoods['expresstime']+86400*7)>time() ) {
            //          return false;
            //      }
            //  }
            // }

            //满足7+7才更新订单终态
            $rs1 = pdo_update('shopping_order_goods', array('status'=>4), array('orderid'=>$orderId));
            $rs2 = pdo_update('shopping_order_aftermarket', array('accomplish'=>1, 'status'=>3), array('orderid'=>$orderId));
            $rs3 = pdo_update('shopping_order', array('accomplish'=>1), array('id'=>$orderId));

            //正式发放分成奖励并修改订单为最终完成状态
            if($rs1 && $rs2 && $rs3){
                return $this->balanceCreditByOrderUpdate($orderId, true, true);
            } else {
                return false;
            }
        }
        return false;
    }

    private function combineBackNumToOrderDetail($orderGoodsList, $backGoodsOne) {
        $orderGoods = array();
        foreach ($orderGoodsList as $orderGoodsOne) {
            if ($orderGoodsOne['goodsid'] = $backGoodsOne['goodsid']) {
                $orderGoodsOne['num'] = $backGoodsOne['num'];
            } else {
                $orderGoodsOne['num'] = 0;
            }
            $orderGoods[] = $orderGoodsOne;
        }
        return $orderGoods;
    }

    private function updateOrderGoodsCommByCommLog($order, $commLog) {
        foreach ($order as $orderGoodsKey=>$ordergoods) {
            foreach ($commLog as $commLogOne) {
                if ( $commLogOne['goodsid']==$order[$orderGoodsKey]['goodsid'] && $commLogOne['optionid']==$order[$orderGoodsKey]['optionid'] ) {
                    $order[$orderGoodsKey]['comm'.$commLogOne['level']] = $commLogOne['comm'];
                } else if ( $commLogOne['goodsid']==$order[$orderGoodsKey]['goodsid'] ) {
                    $order[$orderGoodsKey]['comm'.$commLogOne['level']] = $commLogOne['comm'];
                }
            }
        }
        return $order;
    }
    public function doWebGetListGoods($page){
        //逻辑得到html内容
        global $_GPC,$_W;
        $pindex = intval($_GPC['len']);
        load()->model('shopping.goods');
        $list = pdo_fetchall("SELECT * FROM ".tablename("shopping_goods")." WHERE weid = '{$_W['uniacid']}' and deleted=0 and status = 1 ORDER BY status DESC, displayorder DESC, id DESC LIMIT ".$pindex.",6");
        $total = count($list);

        for($j=0;$j<$total;$j++){
            $Info = Goods_getGoodsSpec($list[$j]['id']);
            $list[$j] += $Info;
        }
        foreach ($list as $layer1 => $value1) {
            $data = array();
            foreach ($value1['specs'] as $layer2 => $value2) {
                $data= $value2['items'];
            }
            $list[$layer1]['specs'] = array();
            $list[$layer1]['specs'] = $data;
        }

        foreach ($list as $key => $value) {
            $list[$key]['options'] = pdo_fetchall("SELECT id,stock FROM ".tablename('shopping_goods_option')." WHERE goodsid = :goodsid",array(':goodsid' => $value['id']));
            if(!empty($list[$key]['options'])){
                foreach ($list[$key]['options'] as $ms => $ls) {
                    $list[$key]['optionsAllTotal'] += $ls['stock'];
                }
            }
        }

        //var_dump($list[1]);
        //$sql = "SELECT COUNT(*) FROM ".tablename("shopping_goods")." WHERE weid = '{$_W['uniacid']}' and deleted=0 LIMIT ".($pindex - 1) * $psize.",".$psize;

        die(json_encode($list));
    }

    /**
     * 购物卡{未激活：；领取：；}
     * @param string tokencard 32
     */
    public function doMobileShoppingCard(){
        global $_GPC,$_W;
        load()->model('shopping.card');
        load()->model('mc');
        load()->model('event.log');
        $operation = $_GPC['op']?$_GPC['op']:'display';
        $uid = intval($_W['member']['uid']);

        if(!$uid){
            $mcres = mc_oauth_userinfo();
        }
        $activation = $_GPC['activation'];
        $use = $_GPC['use'];
        # $token = token('thisiskey&*G'.getip());
        if($operation == 'display'){
            load()->model('mc.mapping.fans');
            $ret = array('status'=>-100);
            $follow = Fans_checkFollow($uid);                   # 查看当前用户是否关注公众号
            /**
             * 1、查看当前用户是否关注
             *    未关注进入关注页面，并保存特殊信息，关注后自动领取
             *
             * 2、激活或者领取，ajax请求当前链接，根据自带的参数判断是那种类型的请求
             */

            // pre($follow);
            $where = array();
            if(!empty($activation)){
                $where = array('activationtoken' => $activation);
            }elseif(!empty($use)){
                $where = array('usetoken' => $use);
            }
            $res = shoppingcard_getDetail($where);
            if($_W['isajax']){
                $ret = array('status' => -200, 'msg' => '异常请求！');
                # ajax 事件
                if(!empty($activation)){
                    # 关注 && 未激活 && 校验成功 && 打开
                    if($follow && !$res['activation'] && $res['display'] && $res['status']){
                        $res = shoppingcard_userActivation($res['cid'], $uid);
                        if($res){
                            $ret['status'] = 200;
                            $ret['msg'] = '激活成功！';
                            $ret['name'] = $_W['member']['nickname'] ? $_W['member']['nickname'] : '小吉鹿';
                        }else{
                            $ret['msg'] = '礼券已激活！';
                        }
                    }else if($res['activation']){
                        $ret['msg'] = '该礼品券已激活！';
                    }else if(!$res['status']){
                        $ret['msg'] = '该礼品券暂时无法激活！';
                    }else if(!$follow){
                        $ret['msg'] = '请关注公众号并领取礼券！';
                    }
                }else if(!empty($use)){
                    # 关注 && 未使用 && 激活 && 校验成功 && 打开
                    if($follow && !$res['use'] && $res['activation'] && $res['display'] && $res['status']){
                        $useRes = shoppingcard_userUse($res['cid'], $uid, $res['price']);
                        if(is_array($useRes)){
                            $ret['msg'] = $useRes['message'];
                        }else{
                            if($useRes){
                                $pid = $res['uid']; # 绑定人的ID置为上家ID
                                $parentRes = mc_fetch($pid, array('nickname', 'realname')); # 获取上家的信息
                                if($_W['member']['pid']){
                                    // $pustContent = "礼品券信息通知：{$_W['member']['nickname']}领取了{$parentRes['nickname']}的礼品券，但是{$_W['member']['nickname']}已是他人的富一代！";
                                    $parentContent = "礼品券提醒：{$_W['member']['nickname']}领取了您的礼品券，但是{$_W['member']['nickname']}已是";
                                    if($_W['member']['pid'] == $pid){
                                        $parentContent .= "您的富一代。建议您将礼品券发给更多的新用户，以发展您的富一代。";
                                    }else{
                                        $parentContent .= "他人的富一代。建议您将礼品券发给更多的新用户，以发展您的富一代。";
                                    }
                                }else{
                                    load()->model('mc.relation');
                                    # 关系链处理
                                    if(relation_handle($uid, $pid)){
                                        $updMcRes = pdo_update('mc_members', array('pid' => $pid), array('uid' => $uid));
                                        // $pustContent = "礼品券信息通知：{$_W['member']['nickname']}领取了{$parentRes['nickname']}的礼品券，并成为他的富一代！";
                                        $parentContent = "礼品券提醒：{$_W['member']['nickname']}领取了您的礼品券，并成为您的富一代！";
                                    }else{
                                        $parentContent = "礼品券提醒：{$_W['member']['nickname']}领取了您的礼品券，但是{$_W['member']['nickname']}无法成为您的会员。建议您将礼品券发给更多的新用户，以发展您的富一代。";
                                    }
                                }
                                if(!empty($parentContent)){
                                    wechatPush($pid, array('text' => array('content' => $parentContent)));
                                }
                                // if(!empty($pustContent)){
                                //  wechatPush('opZZjs3o2cO1w9ztqdF4WEUELbu4', array('text' => array('content' => $pustContent)));
                                // }

                                $ret['status'] = 200;
                                $ret['money'] = $res['price'];
                                $ret['msg'] = '';
                                $ret['link'] = $this->createMobileUrl('list');
                            }else{
                                $ret['msg'] = '领取异常，请刷新后再试！';
                            }
                        }
                    }else if($res['use']){
                        $ret['msg'] = '该礼品券已使用！';
                    }else if(!$res['activation']){
                        $ret['msg'] = '该礼品券未激活，暂时无法领取！';
                    }else if(!$res['status']){
                        $ret['msg'] = '该礼品券暂时无法领取！';
                    }else if(!$follow){
                        $ret['msg'] = '请关注公众号并领取礼券！';
                    }
                }
                ajaxReturn($ret);
            }else{
                if($res){
                    $res['title'] = $res['title'] ? $res['title'] : '云吉良品礼品券';
                    $res['nickname'] = $res['nickname'] ? $res['nickname'] : '小吉鹿';
                    if(!empty($use)){
                        $parentRes = mc_fansNickname($res['receiptor']);    # 获取上家的信息

                        if(!empty($parentRes['tag']['nickname'])){
                            $res['receiptor'] = $parentRes['tag']['nickname'] ? $parentRes['tag']['nickname'] : '小吉鹿';
                        }
                        // $receiptor = mc_fetch($res['receiptor'], array('nickname'));
                        // $res['receiptor'] = !empty($receiptor['nickname']) ? $receiptor['nickname'] : '小吉鹿';

                    }
                    $res['thumb'] = unserialize($res['thumb']);
                }
                if(!empty($activation)){
                    # 激活购物卡金额
                    if(!$follow){
                        if($res['display'] == true && $res['activation'] == 0 && $res['status']){
                            # 储存事件， 关注公众号后激发
                            $eventData = array('cid' => $res['cid'], 'money' => $res['price']);
                            if($uid && event_exist($uid, 10, $eventData))
                                $addres = recordEventLog($uid, 10, $eventData);
                        }
                    }
                    include $this->template('scactivation');
                }else if(!empty($use)){
                    # 领取购物卡金额
                    if(!$follow){
                        # 关注领取
                        if($res['display'] == true && $res['activation'] && $res['use'] == 0 && $res['status']){
                            $eventData = array('cid' => $res['cid'], 'money' => $res['price'], 'uid' => $res['uid']);
                            if($uid && event_exist($uid, 9, $eventData))
                                $addres = recordEventLog($uid, 9, $eventData);
                        }
                    }

                    include $this->template('screceive');
                }else{
                    message('出现异常，正在跳转到首页！', $this->createMobileUrl('list'));
                }
            }
        }else if($operation == 'activation'){
            /**
             * 购物卡激活
             * @param uid 用户
             * @param tokencard 购物卡
             * @param token
             */
            $ajaxtoken = $_GPC['token'];
            $ret = array('msg'=>'异常访问！', 'status' => -100);
            if($_W['isajax']){
                if($ajaxtoken == $token){
                    $res = shoppingcard_activation($tokencard, $uid);
                    if($res){
                        $ret = array('msg'=>'激活成功！', 'status' => 200);
                    }else{
                        if(isset($_SESSION['scerr'])){
                            $ret = array('msg'=>'激活失败，该卡出现异常！', 'status' => -200);
                        }else{
                            $_SESSION['scerr'] = 1;
                            $ret = array('msg'=>'激活失败，请重新进入！', 'status' => -200);
                        }
                    }
                }
            }
            ajaxReturn($ret);
        }
    }

    /**
     * 下单成功
     *
     */
    public function doMobileSingle(){
        global $_GPC,$_W;
        load()->model('shopping.order');
        $uid = $_W['member']['uid'];
        if(empty($uid)){
            load()->model('mc');
            $mcres = mc_oauth_userinfo();
        }
        $info = Order_getOrderByNewest($uid);
        if(empty($info)){
            message('没有最新订单!', $this->createMobileUrl('list'));
        }
        include $this->template('singlesuccess');
    }

    /**
     * 购物卡
     * @param op 访问类型
     * @param cid 购物车ID
     */
    public function doWebShoppingCard(){
        global $_GPC,$_W;
        load()->func('tpl');
        load()->model('shopping.card');
        $operation = $_GPC['op']?$_GPC['op']:'display';

        $ret = array('msg'=>'', 'status' => -100);
        $moneylist = shoppingcard_settingMoney();
        if($operation == 'display'){
            $res = shoppingcard_infoToManage();
        }else if($operation == 'add'){
            $price = intval($_GPC['price']);
            $nums = intval($_GPC['num']);
            if(!empty($_POST)){
                if(in_array($price, $moneylist)){
                    $times = 0;
                    for($i = 0; $i < $nums; $i++){
                        $res = shoppingcard_insertToManage($price);
                        if($res['id']){
                            // $url = $_W['siteroot'] . 'app' . substr($this->createMobileUrl('shoppingcard', array('tokencard' => $res['token'])), 1);
                            // qrJionLogo($url, '../attachment/cardqrcode/'.$res['id'].'.png');
                            $times ++;
                        }
                    }
                    $ret['status'] = 200;
                    $ret['msg'] = $price.'的购物卡已添加'.$times.'个！';
                }else{
                    $ret['msg'] = $price.'的面额当前没有设置！';
                }
                ajaxReturn($ret);
            }
        }else if($operation == 'exportCard'){
            $price = intval($_GPC['price']);
            $re = shoppingcard_exportCard($price);
            if($re){
                require_once('../framework/library/phpexcel/PHPExcel.php');
                $objPHPExcel = new PHPExcel();

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'url')
                    ->setCellValue('B1', 'title');

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
                foreach($re as $key=>$val)
                {
                    $activationurl = $_W['siteroot'] . 'app' . substr($this->createMobileUrl('shoppingcard', array('activation' => $val['activationtoken'])), 1);
                    $useurl = $_W['siteroot'] . 'app' . substr($this->createMobileUrl('shoppingcard', array('use' => $val['usetoken'])), 1);
                    $rowNum1 = $key*2+2;
                    if ($rowNum1<2) {
                        $rowNum1=2;
                    }
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$rowNum1, $activationurl)                            //激活链接
                        ->setCellValue('B'.$rowNum1, substr($val['token'], 0, 16));             //标题
                    $rowNum2 = $rowNum1+1;
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$rowNum2, $useurl)                                   //领取链接
                        ->setCellValue('B'.$rowNum2, '扫描后自动充值');                            //标题

                }
                $title = $price.'元 Execl ';
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

        }else if($operation == 'binding'){
            if($_W['isajax']){
                $type = intval($_GPC['type']);
                if($type == -1){
                    //获取用户昵称信息
                    $uid = intval($_GPC['uid']);
                    $res = shoppingcard_getCardUserName($uid);
                    if($res){
                        $res['status'] = 200;
                    } else {
                        $res['status'] = -200;
                        $res['msc'] = "没有找到用户";
                    }

                    ajaxReturn($res);
                }elseif($type == 0){
                    //单个激活
                    $cid = intval($_GPC['cid']);
                    $uid = intval($_GPC['uid']);
                    $res = array();
                    if(!$cid && !$uid){
                        $res['status'] = -200;
                        $res['msc'] = '参数不正确！';
                        ajaxReturn($res);
                    }

                    $nicknameInfo= shoppingcard_getCardUserName($uid);

                    if(!$nicknameInfo){
                        $res['status'] = -200;
                        $res['msc'] = '该用户不存在！请重新填写uid！';
                        ajaxReturn($res);
                    }

                    $setFrom = shoppingcard_setCardUidInfo($cid,$uid);
                    if(is_numeric($setFrom) && $setFrom > 0){
                        $res['status'] = 200;
                        $res['msc'] = '激活成功!';
                        $res['link'] = $this->createWebUrl('shoppingcard', array('op' => 'display'));
                        shoppingcard_setStatic($cid,false,'start');
                    } else {
                        $res['status'] = -200;
                        $res['msc'] = '激活失败!';
                    }

                    ajaxReturn($res);
                } elseif ($type == 1){
                    //批量激活
                    $arr = !is_array($_GPC['arr']) ? (null) : $_GPC['arr'];
                    $bindingInfo = array();
                    $res = array();
                    foreach ($arr as $key => $value) {
                        if($value && $value != null){
                            $condition = "WHERE weid = ".$_W['weid']." AND cid = ".$value;
                            $bindingInfo[] = pdo_fetch("SELECT cid,token,price FROM ".tablename('shopping_card').$condition);

                        }
                    }

                    ajaxReturn($bindingInfo);
                } elseif ($type == 3){
                    /*取消激活*/
                    $cid = intval($_GPC['cid']);
                    $res = shoppingcard_setStatic($cid,false,'stop');
                    if($res){
                        $ret['status'] = 200;
                        $ret['msc'] = '取消成功！';
                    } else {
                        $ret['status'] = -200;
                        $ret['msc'] = '取消失败！';
                    }
                    ajaxReturn($ret);
                }

            } else {
                $cid = intval($_GPC['cid']);
                $res = shoppingcard_onceInfoToManager();

                if(!empty($_GPC['type']) && $_GPC['type'] == 2){
                    $uidList = empty($_GPC['betchUid']) ? (null) : $_GPC['betchUid'];
                    $cidList = empty($_GPC['betchCid']) ? (null) : $_GPC['betchCid'];

                    foreach ($uidList as $key => $value) {
                        $res = pdo_fetch("SELECT * FROM ".tablename("mc_members")."
                                                WHERE uid = ".$value);
                        if(!$res){
                            message("用户uid不存在！");
                        } else {
                            $items[$key]['uid'] = $value;
                        }
                    }

                    foreach ($cidList as $key => $value) {
                        if($value != ""){
                            $items[$key]['cid'] = $value;
                        }
                    }

                    foreach ($items as $key => $force) {
                        $res = shoppingcard_setCardUidInfo($force['cid'],$force['uid']);
                        shoppingcard_setStatic($force['cid'],true);
                    }
                    if($res){
                        message("批量激活成功！",'referer');
                    } else {
                        message("批量激活失败！",'refresh');
                    }
                    message("不明的数据！");
                }
            }
            /**
             * @param int type {0:单个激活，1：多个激活}
             */
            // $cid = intval($_GPC['cid']);
            // $uid = intval($_GPC['uid']);
            // $type = intval($_GPC['type']);
            // $res = shoppingcard_activation($cid, $uid);
            // if($res){
            //  $ret['msg'] = '更新成功！';
            //  $ret['status'] = 200;
            // }else{
            //  $ret['msg'] = '更新失败！';
            // }
            // ajaxReturn($ret);
        }else if($operation == 'batch'){
            if($_POST){
                shoppingcard_putByimport();
            }else{
                $price = $_GPC['price'];
                if($price){
                    $re = shoppingcard_getInfoToExecl($price);
                }
            }
        }else if($operation == 'setStatic'){
            $cid = intval($_GPC['cid']);
            $type = intval($_GPC['type']);
            if($type){
                $res = shoppingcard_setStatic($cid,true);
                if($res){
                    $ret['status'] = 200;
                    $ret['type'] = 1;
                }else{
                    $ret['status'] = 100;
                    $ret['msc'] = '启动失败!';
                }
            }else{
                $res = shoppingcard_setStatic($cid);
                if($res){
                    $ret['status'] = 200;
                    $ret['type'] = 2;
                }else{
                    $ret['status'] = 100;
                    $ret['msc'] = '启动失败!';
                }
            }

            ajaxReturn($ret);
        }else if($operation == 'uploadImg'){
            if($_W['ispost']){
                $cid = intval($_GPC['on-cid']);
                $thumb = !empty($_GPC['thumb']) ? $_GPC['thumb'] : null;

                $res = pdo_update("shopping_card",array('thumb' => serialize($thumb)),array('cid'=>$cid));
                if($res){
                    if($thumb != null){
                        message("图片上传成功！",'referer');
                    }else{
                        message("当前图片设置是默认的！",'referer');
                    }
                } else {
                    message("图片上传失败！");
                }

            }else{
                $res = shoppingcard_onceInfoToManager();
            }

        }elseif($operation == 'execl'){
            $price = intval($_GPC['price']);
            $re = shoppingcard_exportCard2($price);
            // pre($re);
            if($re){
                require_once('../framework/library/phpexcel/PHPExcel.php');
                $objPHPExcel = new PHPExcel();

                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'ID')
                    ->setCellValue('B1', '标示')
                    ->setCellValue('C1', '价格');

                $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
                $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
                foreach($re as $key=>$val)
                {

                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.($key+2), ' '.$val['cid'])                               //激活链接
                        ->setCellValue('B'.($key+2), substr($val['token'], 0, 16))
                        ->setCellValue('C'.($key+2), ' '.($val['price']));                      //标题
                }
                $title = $price.'元 Execl 统计专用';
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
        include $this->template('shoppingcard');
    }

    public function doWebfixOrderBalanceCredit() {
        global $_GPC, $_W;
        $orderId = intval($_GPC['orderid']);
        $creditsettleStatus = pdo_fetchcolumn('select creditsettle from'.tablename('shopping_order').' WHERE id=:id', array(':id'=>$orderId));
        if (!$creditsettleStatus) {
            $result = $this->balanceCreditByOrderUpdate($orderId, true, false);
            if ($result) {
                message('订单分成修复成功！', getenv("HTTP_REFERER"), 'success');
            } else {
                message('订单分成修复失败（如果多次处理都是失败，请联系技术部开发同学）！', getenv("HTTP_REFERER"), 'fail');
            }
        } else {
            message('分成成功的订单不能再进行分成处理操作！', getenv("HTTP_REFERER"), 'error');
        }
    }

    /**
     * 发货管理
     *
     */
    public function doWebOrderExpress(){
        global $_GPC, $_W;
        $op = $_GPC['op'];
        if($op == 'batch'){

        }
        include $this->template('orderexpress');
    }

    /*
     * 货柜管理
     * */
    public function doWebCabinet() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        load()->model('shopping.cabinet');
        load()->model('regions');
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        //货柜列表
        if ($operation == 'display') {
            $pindex = max(1, intval($_GPC['page']));
            $psize = 10;
            $list = pdo_fetchall("SELECT * FROM " . tablename('cabinet') .' ORDER BY cabinetid DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
            // 每个货柜的库存
            $sql = "SELECT sum(cp.stock) as cpstock, c.cabinetid FROM " . tablename('cabinet') . 'as c JOIN' . tablename('cabinet_pathway') . ' as cp on c.cabinetid = cp.cabinetid GROUP BY c.cabinetid';
            $cpstock = pdo_fetchall($sql);

            $sql = "SELECT * FROM " . tablename('cabinet') . 'as c JOIN ' . tablename('cabinet_pathway') . 'as cp ON c.cabinetid = cp.cabinetid JOIN' . tablename('shopping_goods') . 'as g ON cp.goodsid = g.id';
            $goodsList = pdo_fetchall($sql);
            // 更新货柜的库存
            foreach ($cpstock as $k => &$value) {
                $amount = $value['cpstock'];
                $cabinetid = $value['cabinetid'];
                $data = array('stock' => $amount);
                $where = array('cabinetid' => $cabinetid);
                pdo_update('cabinet', $data, $where);
            }

            //统计数量
            $sql = "SELECT count(*) as totalNum FROM " .tablename('cabinet');
            $totle = pdo_fetchcolumn($sql);
            //分页
            $pager = pagination($totle, $pindex, $psize);
        } elseif ($operation == 'post') {
            if ($id) {
                $sql = "select * from ".tablename('cabinet')." where cabinetid = :cabinetid";
                $cabinet = pdo_fetch($sql, array(':cabinetid' => $id));
                $goodsList = pdo_fetchall("SELECT id,title,brand,pcate FROM ".tablename('shopping_goods')." WHERE status=1");
                $parentCateList = pdo_fetchall("SELECT id,name FROM ".tablename('shopping_category'));
                $parentCateList = array_column($parentCateList, null, 'id');
                $cabinetPathwayList = pdo_fetchall("SELECT * FROM ".tablename('cabinet_pathway')." WHERE cabinetid=".intval($id).' order by pathway');
                $sql = "select p.`name` as prov,c.`name` as city,d.`name` as dist from ims_regions p join ims_regions c on c.parentid = p.regionid join ims_regions d on d.parentid = c.regionid where d.regionid = :regionid";
                $addr = pdo_fetch($sql, array(':regionid' => $cabinet['addr_dist']));
            }

            if (checksubmit('submit')) {
                $cabinetStockTotal = 0;
                // 更新状态是否合法
                foreach ($_GPC['status'] as $statusItem) {
                    if($statusItem > 1 && $statusItem < -1){
                        message('抱歉，轨道状态必选！');
                    }
                }
                // 价格是否大于规定价格
                foreach ($_GPC['price'] as $priceItem) {
                    if($priceItem<300){
                        message('抱歉，轮胎售价必须大于300元！');
                    }
                }
                // 单轨道库存数据是否大于规定库存
                foreach ($_GPC['stock'] as $stockItem) {
                    if($stockItem>10 || $stockItem<0){
                        message('抱歉，单条轨道的库存数合理值介于0和10！');
                    }
                }
                // 检查轮胎条码是否为空
                foreach ($_GPC['productsn'] as $productsnItem) {
                    if (strlen(trim($productsnItem))!=16) {
                        message('抱歉，轮胎条码必须为16位字符串编码！');
                    }
                }
                // 检查产品是否为空
                foreach ($_GPC['goodsid'] as $goodsidItem) {
                    if(!$goodsidItem){
                        message('抱歉，请选择轨道绑定的产品！');
                    }
                }
                //循环处理每条轨道数据更新,并求和机柜库存总数
                foreach ($_GPC['pathwayid'] as $gpcItem=>$gpcItemv) {
                    pdo_update('cabinet_pathway', array(
                        'status'    => intval($_GPC['status'][$gpcItem]),
                        'stock'     => intval($_GPC['stock'][$gpcItem]),
                        'goodsid'   => intval($_GPC['goodsid'][$gpcItem]),
                        'productsn' => trim($_GPC['productsn'][$gpcItem]),
                        'price'     => floatval($_GPC['price'][$gpcItem]),
                        'origprice' => floatval($_GPC['origprice'][$gpcItem]),
                    ), array('pathwayid'=>intval($gpcItemv)));
                    $cabinetStockTotal += intval($_GPC['stock'][$gpcItem]);
                }

                //更新机柜信息
                pdo_update('cabinet', array(
                    'stock' => $cabinetStockTotal,
                    'name' => trim($_GPC['name']),
                    'address' => trim($_GPC['address']),
                    'addr_mark' => trim($_GPC['addr_mark']),

                ), array('cabinetid'=>$id));

                message('更新货柜设置成功！', $this->createWebUrl('cabinet', array('op' => 'display')), 'success');
            }
        } else {
            message('请求方式不存在');
        }
        include $this->template('cabinet');
    }

    /**
     * 导出货柜
     * @param $status = 1 导出缺货的货柜
     * @param $status = 0 导出正常的货柜
     * @param $status = -1 导出维护中的货柜
     */
    public function doWebCabinetex()
    {
        global $_W, $_GPC;
        $status = isset($_GPC['status']) ? intval($_GPC['status']) : '';
        load()->model('shopping.cabinet');
        Cabinetex($status);
    }

    /*
     * 服务费管理
     */
    public function doWebserviceFee() {
        // 获取地址信息
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        //取出所有的省
        $provinceList = pdo_fetchall('SELECT * FROM ' . tablename('regions') . " WHERE level=0");
        $provinceList = array_column($provinceList, null, 'regionid');
        if($operation == 'display') {
            $list = pdo_fetchall('SELECT * FROM '.tablename('regions').' WHERE level=1 ORDER BY parentid ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
            $list = array_column($list, null, 'regionid');

            //取出分页数据中所有市的regionid
            $list2 = array_column($list, 'regionid');
            $list2 = pdo_fetchall('SELECT * FROM '.tablename('regions') .' WHERE level=2 AND parentid in ('.implode(',', $list2).') ORDER BY parentid');
            $tmpRegion = null;
            foreach ($list2 as $distItem) {
                if ($tmpRegion != $distItem['parentid']) {
                    $tmpRegion = $distItem['parentid'];
                    $list[$distItem['parentid']]['total'] = 0;
                    $list[$distItem['parentid']]['open']  = 0;
                }
                $list[$distItem['parentid']]['total']+=1;
                $list[$distItem['parentid']]['open'] += $distItem['status'];
            }

            $sql = "SELECT count(regionid) as totalNum FROM ".tablename('regions').' WHERE level=1';
            $total = pdo_fetchcolumn($sql);
            //分页
            $pager = pagination($total, $pindex, $psize);
        } elseif($operation == 'post') {
            if ($id) {
                $cityInfo = pdo_fetch('SELECT * FROM ' . tablename('regions') . " WHERE regionid=".$id);
                $distList = pdo_fetchall('SELECT * FROM ' . tablename('regions') . " WHERE parentid={$id} AND level=2");
            }
            if (checksubmit('submit')) {
                foreach ($_GPC['status'] as $statusItem) {
                    if($statusItem > 1 && $statusItem < -1){
                        message('抱歉，地区状态必选！');
                    }
                }
                foreach ($_GPC['fee'] as $feeItem) {
                    $feeItem = floatval($feeItem);
                    if($feeItem<0){
                        message('抱歉，服务费必须大于0元！');
                    }
                }
                //循环处理每个地区数据更新
                foreach ($_GPC['regionid'] as $gpcItem=>$gpcItemv) {
                    pdo_update('regions', array(
                        'status'    => intval($_GPC['status'][$gpcItem]),
                        'fee'     => intval($_GPC['fee'][$gpcItem])
                    ), array('regionid'=>intval($gpcItemv)));
                }

                //保存提交数据更新后更新上级区域状态（市和省）
                $cityStatus = $provStatus = 0;
                $distOpenNum = pdo_fetchall('SELECT sum(status) AS dist_open FROM '.tablename('regions')." WHERE parentid={$id}");
                $distOpenNum = $distOpenNum[0]['dist_open'];
                if($distOpenNum) $cityStatus = 1;
                pdo_update('regions', array('status' => $cityStatus), array('regionid'=>$id));

                $provId = intval(substr($id,0,2).'0000');
                $cityOpenNum = pdo_fetchall('SELECT sum(status) AS city_open FROM '.tablename('regions')." WHERE parentid=".$provId);
                $cityOpenNum = $cityOpenNum[0]['city_open'];
                if($cityOpenNum) $provStatus = 1;
                pdo_update('regions', array('status' => $provStatus), array('regionid'=>$provId));

                message('更新服务费设置成功！', $this->createWebUrl('servicefee', array('op' => 'display')), 'success');
            }
        }
        include $this->template('servicefee');
    }

    /*
    * 主副卡管理（主卡用户给副卡用户分配金额）
    */
    public function doWebCorpteam(){
        global $_GPC, $_W;
        //d($_GPC['m']);
        $uid = intval($_GPC['uid']);
        $corpteamid = intval($_GPC['corpteamid']);
        $operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
        $pindex = max(1, intval($_GPC['page']));
        $psize = 10;
        
        if($operation == 'display') {
            //取出所有的主卡用户和副卡用户
            $corpteamList = pdo_fetchall('SELECT c.*,m.realname,m.mobile,m.deposit FROM ' . tablename('corpteam') . 'c LEFT JOIN ' . tablename('members') . ' m ON c.uid=m.uid ORDER BY corpteamid ASC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize);
            //d($corpteamList);
            $corpteam = array_column($corpteamList, null, 'driverid');
            $sql = "SELECT count(corpteamid) as totalNum FROM ".tablename('corpteam');
            $total = pdo_fetchcolumn($sql);
            //分页
            $pager = pagination($total, $pindex, $psize);
        } elseif($operation == 'post') {
            if ($uid) {
                $corpteamDriverList = pdo_fetchall('SELECT c.*,cd.*,m.realname,m.deposit FROM ' . tablename('corpteam') . ' c LEFT JOIN ' . tablename('corpteam_driver') . ' cd ON c.corpteamid=cd.corpteamid LEFT JOIN ' . tablename('members') . ' m ON cd.uid=m.uid WHERE c.uid=' . $uid . ' and c.corpteamid=' . $corpteamid);
                $corpList = pdo_fetchall('SELECT m.*, c.name FROM ' . tablename('members') . ' m JOIN ' . tablename('corpteam') . ' c ON m.uid = c.uid and c.corpteamid= ' . $corpteamid);
                //d($corpList);
            }
            if (checksubmit('submit')) {
                // 更新主卡用户信息
                //d($_GPC['deposit']);
                $res = pdo_update('members', array('deposit' => $_GPC['deposit']), array('uid' => $_GPC['uid']));
                $corpStatus = pdo_fetchall('SELECT * FROM' . tablename('corpteam') . 'WHERE corpteamid=' . $corpteamid);
                if($corpStatus[0]['status'] != 1){
                    message('抱歉，该主卡用户账号异常，请确认后重试！');
                }
                foreach ($_GPC['status'] as $statusItem) {
                    if($statusItem > 1 && $statusItem < -1){
                        message('抱歉，副卡用户状态必选！');
                    }
                }
                foreach ($_GPC['fee'] as $key => $feeItem) {
                    $feeItem = floatval($feeItem);
                    if($feeItem > $corpList[0]['deposit']){
                        message('抱歉，当前分配金额大于账户余额！');
                    }
                }
                foreach ($_GPC['fee'] as $feeItem) {
                    $feeItem = floatval($feeItem);
                    if($feeItem < 0){
                        message('抱歉，分配金额必须大于0元！');
                    }
                }
                // 主卡用户余额数据更新
                $deposit = $corpList[0]['deposit'] - array_sum($_GPC['fee']);
                $resDeposit = pdo_update('members', array('deposit' => $deposit),array('uid' => $uid));
                // 主卡用户日志记录
                if ($resDeposit) {
                    // 循环处理每个主卡用户分配金额数据更新
                    foreach ($_GPC['driverid'] as $gpcItem => $gpcItemv) {
                        $driverList = pdo_fetchall('SELECT * FROM ' . tablename('corpteam_driver') . ' WHERE driverid=' . $gpcItemv);
                        if ($_GPC['fee'][$gpcItem] != 0) {
                            $data = array(
                            'uid'        => $uid,
                            'type'       => 4,
                            'amount'     => $_GPC['fee'][$gpcItem],
                            'remarks'    => "【后台操作】主卡用户[" . $uid ."]分配金额到[{$driverList[0]['uid']}]，金额：". $_GPC['fee'][$gpcItem],
                            'createtime' => time(),
                            'driverid'   => intval(floatval($gpcItemv))
                        );
                        pdo_insert('members_credits_log', $data);
                        }
                    }
                }

                // 循环处理每个副卡用户状态数据更新
                foreach ($_GPC['driverid'] as $gpcItem=>$gpcItemv) {
                    pdo_update('corpteam_driver', array(
                        'status'    => intval($_GPC['status'][$gpcItem]),
                    ), array('driverid'=>intval($gpcItemv)));
                }
                // 副卡用户日志记录
                if ($resDeposit) {
                    foreach ($_GPC['driverid'] as $gpcItem=>$gpcItemv) {
                        $driverList = pdo_fetchall('SELECT * FROM ' . tablename('corpteam_driver') . ' WHERE driverid=' . $gpcItemv);
                        $driverInfo = pdo_fetchall('SELECT deposit FROM ' . tablename('members') . ' WHERE uid=' . $driverList[0]['uid']);
                        $deposit = $driverInfo[0]['deposit'] + $_GPC['fee'][$gpcItem];
                        $resDeposit = pdo_update('members', array('deposit' => $deposit),array('uid' => $driverList[0]['uid']));
                        if ($_GPC['fee'][$gpcItem] != 0) {
                            $data = array(
                                'uid'        => $driverList[0]['uid'],
                                'type'       => 5,
                                'amount'     => $_GPC['fee'][$gpcItem],
                                'remarks'    => "【后台操作】副卡用户[" . $driverList[0]['uid'] . "]分配到金额：" . $_GPC['fee'][$gpcItem],
                                'createtime' => time(),
                                'driverid'   => intval(floatval($gpcItemv))
                            );
                            pdo_insert('members_credits_log', $data);
                        }                
                    }                
                }
            
                message('副卡用户设置成功！', $this->createWebUrl('corpteam', array('op' => 'display')), 'success');
            }
        }elseif($operation == 'import'){
            global $_W, $_GPC;
            $file = $_FILES['corpteam'];
            //d($file);
			if($file){
                $file_types = explode ( ".", $_FILES ['corpteam']['name']);
                $file_type = $file_types [count ( $file_types ) - 1];
                 /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower ( $file_type ) != "xls" && strtolower ( $file_type ) != "xlsx"){
                    message('不是Excel文件，重新上传！', referer(), 'error');
                }
                $fileName =  'corpteam' .date('YmdHis') . "." . $file_type;
                $savePath = $_SERVER['DOCUMENT_ROOT'] . '/' . 'import/';
                if (!file_exists($savePath)) {
                    mkdir($savePath, 0777, true);
                }
                $filePath = copy($file['tmp_name'],$savePath . $fileName);
                if($file_type == 'xls'){
                    //如果excel文件后缀名为.xls，导入这个类
                    require_once('../framework/library/phpexcel/PHPExcel/Reader/Excel5.php');
                    $PHPReader = new PHPExcel_Reader_Excel5();
                }else if($file_type == 'xlsx'){
                    //如果excel文件后缀名为.xlsx，导入这下类
                    require_once('../framework/library/phpexcel/PHPExcel/Reader/Excel2007.php');
                    $PHPReader = new PHPExcel_Reader_Excel2007();
                }
				if ($filePath) {
					$data = array();
                    $PHPExcel = $PHPReader->load($savePath. $fileName);
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
					//取得会员表格属性
					unset($arr[1]);        
                    foreach($arr as $v){
        	            $update = array(                           
        		            'mobile' => $v['B'],
        		            'realname' => floatval($v['C']), 
        		            'carsn' => floatval($v['D']), 
        		            'deposit' => floatval($v['E']),
        		            'status' => floatval($v['F']),
                            'createtime' => floatval($v['G']),
                            'updatedt' => floatval($v['H']),
        		            );
        	            $condition = array(
        		            'uid' => intval($v['A']),
        		        );
                        pdo_update('members', $update, $condition);
                    }
                message('上传成功，请查看是否修改成功！', referer(), 'success');
				} else {
					message('文件 ['. $file['name'] .'] 不存在，请先上传！', '', 'fail');
				}
			}
        }else {
            message('请求方式不存在');
        }
        include $this->template('corpteam');
    }

    /**
     * 导出用户
     * @param $status = 1 导出正常开启用户
     * @param $status = 0 导出关闭用户
     * 
     */
    public function doWebCorpteamOption()
    {
        global $_W, $_GPC;
        $status = isset($_GPC['status']) ? intval($_GPC['status']) : '';
        load()->model('member.corpteam');
        Corpteamex($status);
    }

    /**
     * 联动获取行政区域信息
     * @param string code
     * @return json
     */
    public function doWebAjaxCityData() {
        global $_W, $_GPC;
        $resp = ['code'=>201, 'msg'=>'无权访问该接口！', 'data'=>[]];
        //1、根据传入参数判断获取哪一级数据
        // $parentid = isset($_GPC['parentid']) ?intval($_GPC['parentid']): 0;
        $parentid = isset($_GPC['parentid']) ?$_GPC['parentid']: 0;
        $parentid = intval($parentid);

        $sql = "SELECT * FROM ims_regions WHERE parentid=:parentid";
        $rs = pdo_fetchall($sql, array(':parentid'=>$parentid));

        // $pro = $_GET['prov'];
        // d($pro);
        if (empty($rs)) {
            $resp['code'] = 200;
            $resp['msg']  = '无有效数据，请校验参数！';
        } else {
            $resp['code'] = 200;
            $resp['msg']  = 'ok';
            $resp['data'] = $rs;
        }
        return json_encode($resp);
    }

    /*
     * 获取产品信息
     * */
    public function doWebAjaxGoodsData() {
        global $_W, $_GPC;
        $resp = ['code'=>201, 'msg'=>'无权访问该接口！', 'data'=>[]];
        //1、根据传入参数判断获取哪一级数据
        $pcate = isset($_GPC['pcate']) ?$_GPC['pcate']: 0;
        $pcate = intval($pcate);

        $sql = "SELECT * FROM ims_shopping_goods WHERE pcate=:pcate";
        $rs = pdo_fetchall($sql, array(':pcate'=>$pcate));

        // d($rs);
        if (empty($rs)) {
            $resp['code'] = 200;
            $resp['msg']  = '无有效数据，请校验参数！';
        } else {
            $resp['code'] = 200;
            $resp['msg']  = 'ok';
            $resp['data'] = $rs;
        }
        return json_encode($resp);
    }

    //微信支付单上传
    public function doWebSubmitPayOrder(){
        global $_GPC, $_W;
        load()->model('shopping.order');
        load()->model('wechat');
        //获取order_id
        $orderid = $_GPC['orderid'];
        if (empty($orderid)) {
            $orderid = $_GPC['id'];
        }
        $data = array();
        //获取订单信息
        $order = pdo_fetch("SELECT * FROM ".tablename('shopping_order')." WHERE id=:id",array(':id'=>$orderid));
        //上传数据前检查上传数据是否存在
        $contrastData=array('ordersn'=>'订单号','transid'=>'支付流水号','id_no'=>'身份证号','realname'=>'姓名','price'=>'总价','taxtotal'=>'税费');
        $return=$this->verifyIsNull($order,$contrastData);
        if ($return!='true') {
            $content=$contrastData[$return]."不能为空！";
            $ret=array('content'=>$content,'rsg'=>'error');
            ajaxReturn($ret);
            exit();
        }
        $data['appid']  = $_W['config']['wechat']['appid'];
        $data['mch_id'] = $_W['config']['wechat']['mchid'];
        $data['out_trade_no'] = $order['ordersn'];
        $data['transaction_id'] = $order['transid'];
        $data['customs'] = $_W['config']['customs'];
        $data['mch_customs_no'] = $_W['config']['mch_customs_no'];
        $data['duty'] = $order['taxtotal']*100;

        //实名认证相关
        $data['cert_type'] = 'IDCARD';
        $data['cert_id'] = $order['id_no'];
        $data['name'] = $order['realname'];
        $sign = makeSignature($data,$_W['config']['wechat']['mchkey']);
        $data['sign'] = $sign;
        $args = splicingXml($data);  
        $url=$_W['config']['wechat']['customs_order_up_url'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // $header[] = "Content-type: text/xml";
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        $result  = curl_exec($ch);
        curl_close($ch);
        $result = FromXml($result);

        if (!is_array($result)) {
            ajaxReturn(array('content'=>'提交失败，请联系运营人员','rsg'=>'error'));
        }
        //签名校验
        $result_sign = $result['sign'];
        unset($result['sign']);
        if (signCheckout($result,$result_sign)!==true) {
            ajaxReturn(array('content'=>'签名验证失败，请联系运营人员','rsg'=>'error'));
            exit;
        }

        $state =array('UNDECLARED'=>'未申报','SUBMITTED'=>'申报已提交','PROCESSING'=>'申报中','SUCCESS'=>'申报成功','FAIL'=>'申报失败','EXCEPT'=>'海关接口异常');
        //数据处理
        if ($result['return_code']!==SUCCESS) {
            $ret = array('content'=>'请求通信失败，请稍后再试！');
        }else if($result['result_code']!==SUCCESS){
            $ret = array('content'=>$result['err_code_des'],'rsg'=>'error');
        }else{
            $ret = array('content'=>$state[$result['state']],'rsg'=>'success');
        }
        ajaxReturn($ret);
        exit;
    }


    /**
     * 同步生成报关报文xml文件downCustomsClearance($orderid)
     * @param int $orderid 订单编号
     */
    public function doWebDownCustomsClearance() {
        global $_GPC, $_W;

        // 联接FTP服务器 
        load()->model('ftp');

        $orderid=$_GPC['orderid'];
        if (!$orderid) {
            ajaxReturn(array('content'=>'非法请求!'));
            exit();
        }
        $filePath=dirname(__FILE__).'/reportfile/';
        //查询订单信息
        $orderInfo = pdo_fetch("SELECT * FROM ".tablename("shopping_order")." WHERE id=:id",array(':id'=>$orderid));

        //获取当前订单下的所有订单商品及商品详细信息
        $orderGoods = pdo_fetchall("SELECT a.total,b.artno,b.customssn,b.title,a.optionname,a.price,b.taxrate,b.brand,b.tags,b.unit,b.goodssn,b.productsn,b.country FROM ".tablename("shopping_order_goods")." as a ,".tablename("shopping_goods")." as b  WHERE a.goodsid=b.id and  a.orderid=:orderid",array(':orderid'=>$orderid));   
        foreach ($orderGoods as $key => &$goods) {
            if (empty($goods['country'])) {
                $goods['country']=142;
            }else{
                $countryNo= pdo_fetch("SELECT countryNo FROM ".tablename("shopping_country")." WHERE symbol_code=:code",array(':code'=>$goods['country']));
                $goods['country']=$countryNo['countryNo'];
            }   
            //计算商品税之后的单价相关信息
            $orderGoods[$key]['totalMoney']= sprintf("%.2f",$goods['price']*$goods['total']);
        }
        
        //检测上传的订单信息是否完善
        $upOrderData=array();
        //检测上传的商品信息是否完善
        $upGoodsData=array();
        //生成guid
        $guid = $this->guid();
        //将guid保存
        pdo_update("shopping_order",array('order_guid'=>$guid),array('id'=>$orderid));
        //设置messageid 
        $millisecond = $this->getTotalMillisecond();

        $MessageID="CEB_CEB311_DXPENT0000015220_EPORT_".$millisecond.substr($orderInfo['ordersn'],-4);
        //渲染文件
        $fc='<?xml version="1.0" encoding="UTF-8"?>
            <CEB311Message xmlns="http://www.chinaport.gov.cn/ceb" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" guid="'.$guid.'" version="1.0">
            <MessageHead>
                <MessageID>'.$guid.'</MessageID>
                <MessageType>CEB311</MessageType>
                <OrgCode>9144030036006389XH</OrgCode>
                <CopCode>4403160UTK</CopCode>
                <CopName>深圳前海十点一刻电子商务有限公司</CopName>
                <SenderID>DXPENT0000015220</SenderID>
                <ReceiverID>EPORT</ReceiverID>
                <ReceiverDepartment>CQM</ReceiverDepartment>
                <SendTime>'.$millisecond.'</SendTime>
                <Version>1.0</Version>
            </MessageHead>
            <Order>
                <OrderHead>
                    <guid>'.$guid.'</guid>
                    <appType>1</appType>
                    <appTime>'.date("YmdHis").'</appTime>
                    <appStatus>2</appStatus>
                    <orderType>I</orderType>
                    <orderNo>'.$orderInfo["ordersn"].'</orderNo>
                    <ebpCode>4403160UTK</ebpCode>
                    <ebpName>深圳前海十点一刻电子商务有限公司</ebpName>
                    <ebcCode>4403160UTK</ebcCode>
                    <ebcName>深圳前海十点一刻电子商务有限公司</ebcName>
                    <goodsValue>'.$orderInfo["goodsprice"].'</goodsValue>
                    <freight>0</freight>
                    <discount>'.$orderInfo['coupon'].'</discount>
                    <taxTotal>'.$orderInfo['taxtotal'].'</taxTotal>
                    <acturalPaid>'.$orderInfo["price"].'</acturalPaid>
                    <currency>142</currency>
                    <buyerRegNo>'.$orderInfo["uid"].'</buyerRegNo>
                    <buyerName>'.$orderInfo["realname"].'</buyerName>
                    <buyerIdType>1</buyerIdType>
                    <buyerIdNumber>'.$orderInfo["id_no"].'</buyerIdNumber>
                    <consignee>'.$orderInfo["realname"].'</consignee>
                    <consigneeTelephone>'.$orderInfo["mobile"].'</consigneeTelephone>
                    <consigneeAddress>'.$orderInfo["province"].$orderInfo["city"].$orderInfo["area"].' '.$orderInfo["address"].'</consigneeAddress>
                </OrderHead>';
        //开始拼接商品信息
        //gcode,ciqGno 按标准填写。 gmodel,ciqGmodel 不检验，但需要填写.$goods['unit'],'.$goods['tags'].'
        foreach ($orderGoods as $key => $goods) {
                $gnum=$key+1;
                if (empty($goods['brand'])){$goods['brand']='无';}
                $fc.='<OrderList>
                    <gnum>'.$gnum.'</gnum>
                    <itemNo>'.$goods['artno'].'</itemNo>
                    <itemName>'.$goods['title'].'</itemName>
                    <unit>011</unit>
                    <qty>'.$goods['total'].'</qty>
                    <price>'.$goods['price'].'</price>
                    <totalPrice>'.$goods['totalMoney'].'</totalPrice>
                    <currency>142</currency>
                    <country>'.$goods['country'].'</country>
                    <ciqGno>'.$goods['customssn'].'</ciqGno>
                    <gcode>'.$goods['goodssn'].'</gcode> 
                    <gmodel>'.$goods['unit'].'</gmodel>
                    <ciqGmodel>'.$goods['unit'].'</ciqGmodel>
                    <brand>'.$goods['brand'].'</brand>
                    </OrderList>';
            }
        $fc.='</Order>
            </CEB311Message>';
        //去除回车
        $fc = str_replace(PHP_EOL,'',$fc);   
        //报文文件名定义规则 ：CEB301 + 公司组织机构代码9位数 + 当前时间毫秒数 + 4位流水号  .xml 
        $filename = $MessageID.'.xml';
        //将文件保存到本地服务器
        file_put_contents($filePath.$filename, $fc);

        //将文件上传到海关客户端
        $file=$filePath.$filename;
        $ftp = new ftp(); // 打开FTP连接

        $result=$ftp->up_file($file,'SendFile/Send/'.$filename,false);
        $ftp->close(); // 关闭FTP连接
        //删除本地文件
        unlink($file);
        ajaxReturn(array('content'=>$result));
        exit;
    }

    //生成guid
    function guid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            $uuid = substr($charid, 0, 8).$hyphen
                    .substr($charid, 8, 4).$hyphen
                    .substr($charid,12, 4).$hyphen
                    .substr($charid,16, 4).$hyphen
                    .substr($charid,20,12);
            return $uuid;
        }
    }

    //获取当前时间毫秒数(毫秒数只取3位)
    public function getTotalMillisecond() {  
        $time = explode (" ", microtime () );   
        $time = date('YmdHis',$time [1]) . ($time [0] * 1000);   
        $time2 = explode ( ".", $time );   
        $time = $time2 [0];  
        return $time;  
    }

    //检测数据是否为空
    public function verifyIsNull($verifyData,$template){
        foreach ($template as $key => $item) {
            if (empty($verifyData[$key])) {
                return $key;
            }
        }
        return true;
    }

}