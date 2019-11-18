<?php
namespace console\models; 

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class RepoRtecipient extends ActiveRecord
{
    public static function tableName(){
        return '{{%report_recipient}}';
    }
	
	/**
	 *获取需要推送邮件的用户列表
	 *@return array
	 */
	public function getReciveUserList(){
        $query = (new Query())
            ->select('*')
            ->from("{$this->tableName()}")
            ->All();
             //当天时间戳
          $begin = strtotime(date('Y-m-d',time()).' 00:00:00');
          $end = strtotime(date('Y-m-d',time()).' 23:59:59');
        $UserList = [];
        foreach ($query as $key => $User) {
            //判断收件人列表是否在有效期
            if($User['starttime'] >= $begin && $User['starttime'] <= $end){
              $UserList[] = $User;
            }
        }
       return $UserList;
    }

    /**
	 * 用户统计
	 */
   	public function userReportStat(){
   		$query = (new Query())
            ->select('*')
            ->from("{{%members}}")
            ->All();
      $userArray = [];
      $userArray['UserCount'] =  count($query); //用户总数

      //当天时间戳
      $begin = strtotime(date('Y-m-d',time()).' 00:00:00');
	  $end = strtotime(date('Y-m-d',time()).' 23:59:59');
	  $userCount = 0;
    $joinUser = 0;
      foreach ($query as $key => $value) {
      	//当天新增的用户
        if($value['createtime'] >= $begin && $value['createtime'] <= $end){
      		$userCount++;
      	}
        //当天登录用户数

        if($value['joindate'] >= $begin && $value['joindate'] <= $end){
            $joinUser++;
        }

      }
      $userArray['UserAddCount'] = $userCount;//当天新增加的用户
      $userArray['joinUser'] = $joinUser;//当天新增加的用户
      return $userArray;
       
   	}


   	/**
	 * 订单统计
	 *订单数据（下单总数，付款订单总数，发货订单总数，作废订单数量，每个仓库分别订单总数）
	 *财务数据（充值总额，消费总额）
	 */
   	public function orderReportStat(){
   		$query = (new Query())->select('*')->from("{{%shopping_order}}")->all();

   		$success = 0;
   		$delivery = 0;
   		$cancel = 0;
   		$consumption = 0;
   		foreach ($query as $key => $order) {
   			if($order['status']==1){
   				$success++;
   				$consumption+=$order['price'];
   			}
   			if($order['status']==2){
   				$delivery++;
   			}
   			if($order['status']==-1){
   				$cancel++;
   			}
   		}
        $orderArray = [];
   		//组合数组
   		  $orderArray['OrderCount'] = count($query);//下单总数
        $orderArray['success'] = $success;//付款订单总数
        $orderArray['delivery'] = $delivery;//付款订单总数
        $orderArray['cancel'] = $cancel;//付款订单总数
        $orderArray['consumption'] = $consumption;//消费总额
        return $orderArray;
   	}

   	/**
	 * 商品数据（当日后台新增商品）
	 *
	 */
   	public function productReportStat(){
   		$query = (new Query())
            ->select('*')
            ->from("{{%shopping_goods}}")
            ->where(['status'=>1])
            ->All();
      // 当天时间戳
      $begin = strtotime(date('Y-m-d',time()).' 00:00:00');
	  $end = strtotime(date('Y-m-d',time()).' 23:59:59');
	  	$productCount = 0;
	    foreach ($query as $key => $product) {
	    	if($product['createtime'] >= $begin && $product['createtime'] <= $end){
	    			$productCount++;
	    	}
	    }
      $productData = [];
	    $productData['ProductCount'] = $productCount;
	    return $productData;
   	}
   	
   	/**
	 * 订单仓库
	 *
	 */
   	public function OrderWarehouseStat(){
   		//仓库分别订单总数

			$warehouse = (new Query())
            ->select('w.name,COUNT(w.name) AS  COUNT')
            ->from("{{%shopping_goods}} g")
            ->leftJoin("{{%shopping_warehouse}} w", 'g.wid = w.`id`')
            ->leftJoin("{{%shopping_order_goods}} o", 'o.`goodsid` =  g.`id`')
           	->groupBy('name')
            ->having("count!=0")
            ->all();
            $warehouseArray = [];
            foreach($warehouse as $key=>$w){
              $warehouseArray[] = $w['name'].' '.$w['COUNT'];
            }
            return $warehouseArray;
   	}
   
}

//end file