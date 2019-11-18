<?php
/**
 * 补货开门 maintenance/verify
 * 补货查询 maintenance/replenish
 * 补货结果 maintenance/subreplenish
 */
namespace common\rpc;

use Yii;

class MaintenanceService {

    public function name() { return 'MaintenanceService'; }

    /**
     * 补货开门 maintenance/verify
     * @param  int $acc 账号(电话为主)
     * @param  int $pwd 密码
     * @param  int $deviceId 设备编号
     * @return array
     */
    public function verify(int $acc, int $pwd, int $deviceId) {
        return ['mobile'=>$acc, 'verifyres'=> (substr($acc, 0,3)=='136' && strlen($acc)==11 && strlen($pwd)==6)? 'PASS' : 'DENY'];
    }

    /**
     * 补货查询 maintenance/replenish
     * @param  int $mobile 电话
     * @param  int $deviceId 设备编号
     * @return array
     */
    public function replenish(int $mobile, int $deviceId) {
        return  [
        	'replenishsn'=>'201984569574967',
        	'tyres'=>[
        		['pathway'=>1, 'sku'=>'SZ-134-98546-8956', 'num'=>2, 'name'=>'三力12R22.5/L812A', 'replenishdt'=>11111, 'status'=>'未补货'],
                ['pathway'=>3, 'sku'=>'SZ-135-98546-8006', 'num'=>8, 'name'=>'康威斯12.00R20/CPD68', 'replenishdt'=>11111, 'status'=>'未补完'],
        		['pathway'=>5, 'sku'=>'SZ-139-98546-1996', 'num'=>3, 'name'=>'三力12R22.5/L806A', 'replenishdt'=>11111, 'status'=>'已补货'],
        	]
        ];
    }

    /**
     * 补货结果
     * @param  string $data json-data
     * @return bool
     */
    public function subreplenish($data) {
    	return empty(json_decode($data));
    }

}
