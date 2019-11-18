<?php
/*********************************************************************************************
 * 订单清理
 * 1、删除生成、超过12h未支付订单
 * 2、发货超过7天未收货订单自动收货
 * 3、解冻完单后七天未申请退换货订单分成
 ********************************************************************************************/
error_reporting(0);  //E_ALL
set_time_limit(0);
ini_set('memory_limit','1024M');
////////////////////////////////

//循环检查最大次数，防止异常导致死循环
define('LOOP_SIZE', 10000);
define('DB_CONN', getDBConn());

//1-删除12小时未支付订单
//先执行 UPDATE ims_shopping_order SET paymenttime=0 WHERE STATUS=0; 整理历史数据
$sql_del = 'DELETE FROM ims_shopping_order WHERE status=0 AND createtime<(unix_timestamp()-43200);'; //12*3600
if(mysql_query($sql_del)) {
  $delNums = mysql_affected_rows(DB_CONN);
  if ($delNums) {
    file_put_contents('../data/logs/cleanDuprationOrders_'.date('Ymd').'.log', "删除支付超时订单 {$delNums} 条！\n");
  }
}

//2-发货7天未收货订单自动收货
$sql_rec = 'UPDATE ims_shopping_order SET status=4,receipttime='.time().' WHERE status=2 AND sendexpress<(now()-604800);'; //3600*24*7
if(mysql_query($sql_rec)) {
  $recNums = mysql_affected_rows(DB_CONN);
  if ($recNums) {
    file_put_contents('../data/logs/cleanDuprationOrders_'.date('Ymd').'.log', "处理自动收货订单 {$recNums} 条！\n");
  }
}

//3-收货后7天订单：无退换货则解冻业绩分成；有则回退
$sql_final = 'SELECT * ims_shopping_order WHERE status=4 AND accomplish=0 AND receipttime<(unix_timestamp()-604800);'; //3600*24*7
$orderList = fetchResult(mysql_query($sql_final));
foreach ($orderList as $orderOne) {
  $recNums = mysql_affected_rows(DB_CONN);
  if ($recNums) {
    file_put_contents('../data/logs/cleanDuprationOrders_'.date('Ymd').'.log', "处理自动收货订单 {$recNums} 条！\n");
}


//查询需要处理的所有订单
//截止订单（uid-1784, orderid-528，2015-08-25 13:22:16）
$sql = "SELECT * from ims_shopping_order where status>0 and accomplish=0 and weid=17 and id < 528 ";
$orderList = fetchResult(mysql_query($sql));
foreach ($orderList as $orderOne) {
  $parUids = getPids($orderOne['uid']);
  $sql = 'SELECT tba.goodsid,tba.price,tba.total,tba.goodsid,tbb.price as price2
          FROM
            ims_shopping_order_goods AS tba LEFT JOIN ims_shopping_order AS tbb ON tba.orderid = tbb.id where tba.orderid='.$orderOne['id'];
  $orderGoods = fetchResult(mysql_query($sql));
  foreach ($orderGoods as $orderGoodsOne) {
    $goodsDetail = getGoodsDetail($orderGoodsOne['goodsid']);
    foreach ($parUids as $level=>$parUid) {
      if ($parUid) {
        $pluscredit3 = $goodsDetail['comm'.$level]*$orderGoodsOne['total'];
        $pluscredit3results = $orderGoodsOne['price2'];
        $sql = "update ims_mc_members set credit3=credit3+{$pluscredit3},credit3results=credit3results+{$pluscredit3results}, credit5=credit5+{$pluscredit3} where uid={$parUid}";
        $plusCredit = mysql_query($sql);
        if ($plusCredit) {
          $sql = "insert into ims_mc_comm_log (uid,pid,level,fmoney,gid,orderid,status) values (".$orderOne['uid'].",{$parUid},{$level},'{$pluscredit3}',".$orderGoodsOne['goodsid'].",".$orderOne['id'].",0)";
          $rs = mysql_query($sql);
        }
        echo "订单号：".$orderOne['id'].",{$level}级上家{$parUid}, 增加{$pluscredit3}\n";
      }
    }
  }
  echo "\n";
}


exit();
//////////////////////////////////////////////////
function getDBConn() {
  define('IN_IA', true);
  require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'config.php');
  //$config['db']['database'] = 'test';
  $conn = mysql_connect($config['db']['host'].':'.$config['db']['port'], $config['db']['username'], $config['db']['password']) or die(mysql_error());
  mysql_select_db($config['db']['database'], $conn);
  mysql_query('set names '.$config['db']['charset']);
  return $conn;
}

function fetchResult($data) {
  $result = array();
  if ($data) {
    while($row = mysql_fetch_assoc($data)) {
      $result[] = $row;
    }
  }
  return $result;
}

function getPids($uid){
  $data = array();
  $rs = fetchResult(mysql_query('select pid from ims_mc_members where uid='.$uid));
  if (!empty($rs)) {
    $data[1] = $rs[0]['pid'];
    $rs = fetchResult(mysql_query('select pid from ims_mc_members where uid='.$data[1]));
    if (!empty($rs)) {
      $data[2] = $rs[0]['pid'];
      $rs = fetchResult(mysql_query('select pid from ims_mc_members where uid='.$data[2]));
      if (!empty($rs)) {
        $data[3] = $rs[0]['pid'];
      }
    }
  }
  return $data;
}

function getRecodsetLimitedBySQL($sql, $limit=3000) {
  $sql .= ' limit '.$limit;
  $data = fetchResult(mysql_query($sql));
  return $data;
}

function getGoodsDetail($goodsid) {
  $rs = fetchResult(mysql_query('select marketprice,comm1,comm2,comm3 from ims_shopping_goods where id='.$goodsid));
  return empty($rs) ? array() : $rs[0];
}