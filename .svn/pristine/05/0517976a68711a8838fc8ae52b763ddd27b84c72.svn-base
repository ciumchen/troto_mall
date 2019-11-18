<?php
/*********************************************************************************************
 * 处理新老结算方式升级后未处理完订单的结算
 ********************************************************************************************/
error_reporting(0);  //E_ALL
set_time_limit(0);
ini_set('memory_limit','1024M');
////////////////////////////////

//循环检查最大次数，防止异常导致死循环
define('LOOP_SIZE', 10000);  

$dbConn = getDBConn();


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