<?php
/**
 * 支付结果   order/payres
 * 临时单查询 order/detail
 */
namespace common\rpc;

use Yii;

use frontend\models\ShoppingGoods;
use common\models\ShoppingOrder;
use frontend\models\ShoppingOrderGoods;

class OrderService {

    public function name() { return 'OrderService'; }

    /**
     * 支付结果 order/payres
     * @param  string $ordersn int(16)-string
     * @return array
     */

    public function payres($ordersn, $result='PAY_FINISH') {
    	if (is_numeric($ordersn) && strlen($ordersn)==16) {
            $res = [
            	'ordersn' => $ordersn, 
                'result'  => $result,
                'skus'    => [],
            ];
        }
        return $res;
    }

    /**
     * 临时单查询
     * @param  string $ordersn int(16)-string
     * @return array
     */
    public function detail($ordersn) {
        $res = ['action'=>'order/detail', 'data'=>[]];
    	if (is_numeric($ordersn) && strlen($ordersn)==16) {
            $res['data'] = [
            	'ordersn'=>$ordersn,
            	'skus' => [
            		['pathway'=>6, 'num'=>2],
            		['pathway'=>6, 'num'=>2],
            	]
            ];
        }
        return $res;
    }

}
