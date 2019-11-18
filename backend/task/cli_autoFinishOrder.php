<?php
/*********************************************************************************************
 * 自动完单
 * SECTION1、正常订单：检查发货超过7天
 * SECTION2、换货订单：检查二次发货超过7天
 * SECTION3、退货订单：检查买家发货超过7天
 ********************************************************************************************/
error_reporting(0);  //E_ALL
set_time_limit(0);
ini_set('memory_limit','1024M');
////////////////////////////////

$dbConn = getDBConn();

//查询需要自动完单的总数
$total = waitAutoFinishOrderTotal($dbConn);

/** SECTION1 处理正常订单 **/
$loopExpectation = 10000; //循环检查最大次数，防止异常导致死循环
//收货超过七天或者发货超过14天无退换货申请
if ($total[1]) {
  $sql = "SELECT id from ims_shopping_order where cancelgoods=0 and accomplish=0 and sendexpress>0 and (receipttime<(now()-86400*7) OR sendexpress<(now()-86400*14))";
  $orders=getRecodsetLimitedBySQL($sql);
  while (!empty($orders) && $loopExpectation--) {
    foreach ($orders as $orderOne) {
      //正式发放佣金（修改佣金流水记录标识）
      mysql_query("update ims_mc_comm_log set status='1' where orderid='".$orderOne['id']."'");
      //解冻余额
      $sql = "select * from ims_mc_comm_log where orderid='".$orderOne['id']."'";
      $commLog = fetchResult(mysql_query($sql));
      foreach ($commLog as $commLogOne) {
        mysql_query('update ims_mc_members set credit5=credit5-'.$commLogOne['fmoney']." where uid='".$commLogOne['pid']."'");
      }
      //修改订单状态为终态
      mysql_query("update ims_shopping_order set accomplish=1,cancelgoods=2 where id='".$orderOne['id']."'");
    }
  }
  $orders=getRecodsetLimitedBySQL($sql);
}

/** SECTION2 处理换货订单 **/
$loopExpectation = 10000; //循环检查最大次数，防止异常导致死循环
//二次发货超过7天
if ($total[2]) {
  $sql = "SELECT DISTINCT tba.orderid,tba.uid
          FROM ims_shopping_order_aftermarket AS tba
          LEFT JOIN ims_shopping_order AS tbb ON tba.orderid = tbb.id
          WHERE
            tba.type=1 AND tbb.cancelgoods=1 AND tbb.accomplish=0
            AND tba.saexpresssn IS NOT NULL AND tba.saexpresstime<(NOW()-86400*7)";
  $orders=getRecodsetLimitedBySQL($sql);
  while (!empty($orders) && $loopExpectation--) {
    foreach ($orders as $orderOne) {
      //查询订单内是否还有二次发货未超过7天的商品
      $checkRs = fetchResult(mysql_query("select oaid,orderid from ims_shopping_order_aftermarket where orderid='".$orderOne['orderid']."' and type=1 and saexpresssn IS NOT NULL AND saexpresstime between (NOW()-86400*7) and now()"));
      if (empty($checkRs)) {
        //正式发放佣金（修改佣金流水记录标识）
        mysql_query("update ims_mc_comm_log set status='1' where orderid='".$orderOne['orderid']."'");
        //解冻余额
        $sql = "select * from ims_mc_comm_log where orderid='".$orderOne['orderid']."'";
        $commLog = fetchResult(mysql_query($sql));
        foreach ($commLog as $commLogOne) {
          mysql_query('update ims_mc_members set credit5=credit5-'.$commLogOne['fmoney']." where uid='".$commLogOne['pid']."'");
        }
        //修改售后商品状态
        mysql_query("update ims_shopping_order_aftermarket set accomplish=1 where orderid='".$orderOne['orderid']."'");
        mysql_query("update ims_shopping_order_goods set status=4 where orderid='".$orderOne['orderid']."'");
        //修改订单状态为终态
        mysql_query("update ims_shopping_order set accomplish=1,cancelgoods=2 where id='".$orderOne['orderid']."'");
      }
    }
  }
  $orders=getRecodsetLimitedBySQL($sql);
}

/** SECTION3 处理退货订单 **/
$loopExpectation = 10000; //循环检查最大次数，防止异常导致死循环
//买家发货超过7天
if ($total[3]) {
  $sql = "SELECT DISTINCT tba.oaid,tba.uid,tba.orderid
          FROM ims_shopping_order_aftermarket AS tba
          LEFT JOIN ims_shopping_order AS tbb ON tba.orderid = tbb.id
          WHERE
            tba.type=2 AND tbb.cancelgoods=1 AND tbb.accomplish=0
            AND tba.expresssn IS NOT NULL AND tba.expresstime<(NOW()-86400*7)";
  $orders=getRecodsetLimitedBySQL($sql);
  while (!empty($orders) && $loopExpectation--) {
    foreach ($orders as $orderOne) {
      //查询订单内是否还有买家发货未超过7天的商品
      $checkRs = fetchResult(mysql_query("select oaid,orderid from ims_shopping_order_aftermarket where orderid='".$orderOne['orderid']."' and type=2 and expresssn IS NOT NULL AND expresstime between (NOW()-86400*7) and now()"));
      if (empty($checkRs)) {
        //查询退货商品
        $orderGoods = fetchResult(mysql_query("select * from ims_shopping_order_goods where id='".$orderOne['oaid']."'"));
        $backGoodsList = array();
        foreach ($orderGoods as $goods) {
          $backGoodsList[] = $goods['goodsid'];
        }

        //正式发放佣金（修改佣金流水记录标识）
        mysql_query("update ims_mc_comm_log set status='1' where orderid='".$orderOne['orderid']."'");
        //解冻余额
        $sql = "select * from ims_mc_comm_log where orderid='".$orderOne['orderid']."'";
        $commLog = fetchResult(mysql_query($sql));
        foreach ($commLog as $commLogOne) {
          if (in_array($commLogOne['gid'], $backGoodsList)) {
            //减业绩
            $priceTotal=0;
            $orderGoodsDetail = fetchResult(mysql_query("select price,total from ims_shopping_order_goods where cancelgoods=2 and orderid='".$orderOne['orderid']."' and goodsid'".$commLogOne['gid']."'"));
            foreach ($orderGoodsDetail as $orderGoodsDetailOne) {
              $priceTotal = $priceTotal + $orderGoodsDetailOne['price']*$orderGoodsDetailOne['total'];
            }
            //减佣金
            mysql_query("update ims_mc_members set credit2=credit2-".$commLogOne['fmoney'].",credit3results=credit3results-{$priceTotal} where uid='".$commLogOne['pid']."'");
            mysql_query("insert into ims_mc_comm_log (uid,pid,level,fmoney,orderid,gid,status) values ('".$commLogOne['uid']."','".$commLogOne['pid']."','".$commLogOne['level']."','-".$commLogOne['fmoney']."','".$commLogOne['orderid']."','".$commLogOne['gid']."','1')");
          }
          //减冻结
          mysql_query("update ims_mc_members set credit5=credit5-".$commLogOne['fmoney']." where uid='".$commLogOne['pid']."'");
        }
        //修改售后商品状态
        mysql_query("update ims_shopping_order_aftermarket set accomplish=1 where orderid='".$orderOne['orderid']."'");
        mysql_query("update ims_shopping_order_goods set status=4 where orderid='".$orderOne['orderid']."'");
        //修改订单状态为终态
        mysql_query("update ims_shopping_order set accomplish=1,cancelgoods=2 where id='".$orderOne['orderid']."'");
      }
    }
  }
  $orders=getRecodsetLimitedBySQL($sql);
}

exit();
//////////////////////////////////////////////////
function getDBConn() {
  define('IN_IA', true);
  require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'config.php');
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

function waitAutoFinishOrderTotal($dbConn) {
  $setcion1 = mysql_query("SELECT count(id) as total from ims_shopping_order where cancelgoods=0 and accomplish=0 and sendexpress>0 and (receipttime<(now()-86400*7) OR sendexpress<(now()-86400*14))");
  $setcion1 = fetchResult($setcion1);
  $setcion1 = $setcion1[0]['total'] ? $setcion1[0]['total'] : 0;
  $setcion2 = mysql_query("SELECT count(DISTINCT orderid) as total from ims_shopping_order_aftermarket where type='1' AND accomplish='0' AND saexpresstime<(NOW()-'.86400*7.')");
  $setcion2 = fetchResult($setcion2);
  $setcion2 = $setcion2[0]['total'] ? $setcion2[0]['total'] : 0;
  $setcion3 = mysql_query("SELECT count(DISTINCT orderid) as total from ims_shopping_order_aftermarket where type='2' AND accomplish='0' AND expresstimes<(NOW()-'.86400*7.')");
  $setcion3 = fetchResult($setcion3);
  $setcion3 = $setcion3[0]['total']>0 ? $setcion3[0]['total'] : 0;
  return array('1'=>$setcion1,'2'=>$setcion2,'3'=>$setcion3,);
}

function getRecodsetLimitedBySQL($sql, $limit=3000) {
  $sql .= ' limit '.$limit;
  $data = fetchResult(mysql_query($sql));
  //print_r($data);
  return $data;
}