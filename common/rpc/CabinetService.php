<?php
/**
 * 图片轮播广告 cabinet/ads
 * 机柜状态上报 cabinet/status
 * 机柜故障上报 cabinet/breakdown
 * 机柜商品     cabinet/goods
 * 取货验证     cabinet/verifycode
 * 取货结果     cabinet/subpick
 * 机柜下单     cabinet/suborder
 */
namespace common\rpc;

use Yii;
use common\models\Cabinet;
use common\models\CabinetPathway;

use frontend\models\ImsShoppingAdv;
use frontend\models\ShoppingGoods;
use common\models\ShoppingOrder;
use common\models\ShoppingOrderGoods;

class CabinetService {

    public function name() { return 'CabinetService'; }

    /**
     * 机柜查询
     * @param  int $deviceid 终端设备编号
     * @return array
     */
    public function info(int $deviceid) {
        $cabinetInfo = Cabinet::find()->where(['cabinetid'=>$deviceid])->asArray()->one();
        if (isset($cabinetInfo['createdt'])) unset($cabinetInfo['createdt']);
        if (isset($cabinetInfo['updatedt'])) unset($cabinetInfo['updatedt']);
        if (isset($cabinetInfo['status'])) unset($cabinetInfo['status']);
        if (isset($cabinetInfo['stock'])) unset($cabinetInfo['stock']);
        if (isset($cabinetInfo['lat'])) unset($cabinetInfo['lat']);
        if (isset($cabinetInfo['lng'])) unset($cabinetInfo['lng']);
        if (isset($cabinetInfo['geohash'])) unset($cabinetInfo['geohash']);
        if (!isset($cabinetInfo['tel'])) $cabinetInfo['tel']='400-926-1888';
        return $cabinetInfo;
    }

    /**
     * 图片轮播广告列表 cabinet/ads
     * @param  int $deviceid 终端设备编号
     * @return array
     */
    public function ads($deviceid) {
        $ShoppingAdvmodel = new ImsShoppingAdv();
        $rest = $ShoppingAdvmodel->adv(1);
        $res = [];
        foreach ($rest as $item) {
            $res[] = ['url'=>$item['thumb'], 'startingdt'=>$item['starttime'], 'endingdt'=>$item['endtime']];
        }
        return $res;
    }

    /**
     * 机柜状态上报
     * @param  string $data json-data
     * @return bool
     */
    public function status($data) {
        return empty(json_decode($data));
    }

    /**
     * 机柜故障上报
     * @param  string $data json-data
     * @return bool
     */
    public function breakdown($data) {
        return empty(json_decode($data));
    }

    /**
     * 机柜商品 cabinet/goods
     * @param  int $deviceid 终端设备编号
     * @return array
     */
    public function goods($deviceid) {
        $res = [];
        if($deviceid && strlen($deviceid)>7) {
            if($deviceid==1923100002) $deviceid=1923000001;
            if($deviceid==1900000000) $deviceid=1923000001;

            $cabinetGoods = CabinetPathway::find()->select('goodsid,price,stock,pathway')
                                                ->where(['cabinetid'=>$deviceid, 'status'=>0])
                                                ->asArray()->all();
            $goodsids = [];
            foreach ($cabinetGoods as $goods) {
                if(!in_array($goods['goodsid'], $goodsids)) $goodsids[]=$goods['goodsid'];
            }

            $ShoppingGoodsModel = new ShoppingGoods();
            $goodsDetail = $ShoppingGoodsModel::find()->select('id,title,price,thumb,description,content,goodssn,productsn,total,timestart,timeend,fdesc')
                                                ->where(['in', 'id', $goodsids])->asArray()->all();
            $goodsDetail = array_column($goodsDetail, null, 'id');

            foreach ($cabinetGoods as $item) {
                $params = [];
                $goodsParams = $ShoppingGoodsModel->goodsParam($item['goodsid']);
                if ($goodsParams) {
                    foreach ($goodsParams as $param) {
                        $params[] = [
                            'paramkey' => $param['title'], 'paramvalue' => $param['value']
                        ];
                    }
                }
                $res[] = [
                    'id'      => $item['goodsid'],
                    'name'    => $goodsDetail[$item['goodsid']]['title'],
                    'desc'    => $goodsDetail[$item['goodsid']]['fdesc'],
                    'summary' => $goodsDetail[$item['goodsid']]['fdesc'],
                    'thumb'   => $goodsDetail[$item['goodsid']]['thumb'],
                    'sku'     => $goodsDetail[$item['goodsid']]['productsn'],
                    'sn'      => $goodsDetail[$item['goodsid']]['goodssn'],
                    'price'   => $item['price'],
                    'stock'   => $item['stock'],
                    'pathway' => $item['pathway'],
                    'limit'   => 6,
                    'params'  => $params,
                ];
            }
        }
        return $res;
    }

    /**
     * 取货验证 cabinet/verifycode
     * 说明：deviceid传入时候会做强验证，即取胎码与机柜必须绑定一致
     * @param  int $code     取货验证码(用户或者服务人员)，6bit
     * @param  int $deviceid 取货机柜，10bit
     * @return array
     */
    public function verifycode($code, $deviceid='') {
        //无效验证码
        $res = [
            'code' => 0,
            'msg'  => 'Invalid Code.',
            'data' => [],
        ];

        if (is_numeric($code) && strlen($code)==6) {
          if ($code=='123456') {
            $res['code']    = 1;
            $res['msg']     = 'OK';
            $res['timeout'] = 180;
            $res['data']    = [
                ['name'=>'三力轮胎 15R', 'pathway'=>6, 'sn'=>'2908090880', 'total'=>5, 'sku'=>'SZ9-893-6685-1', 'price'=>'850.66', 'num'=>0, 'orderdt'=>1565580000, 'pickdt'=>1565580000, 'status'=>'已取胎'], 
                ['name'=>'三力轮胎 15R', 'pathway'=>5, 'sn'=>'2908090880', 'total'=>5, 'sku'=>'SZ9-893-6685-1', 'price'=>'899.00', 'num'=>2, 'orderdt'=>1565580000, 'pickdt'=>1565580000, 'status'=>'未取完'],
            ];
          }
        }
        return $res;
    }

    /**
     * 取货结果上报 终端根据取货码提交成功处理的商品(按轨道编号)数量 
     * @param  int $ordersn 单号
     * @param  array $pickRes 取胎结果
     * {
     *   "pathway": 6,
     *   "sku": "SZ9-893-6685-1",
     *   "num": 2,
     * },
     * @return bool
     */
    public function subpick($ordersn, $pickRes) {
        return $ordersn&&$pickRes;
    }

    /**
     * 机柜下单(机柜是临时单) cabinet/suborder
     * @param  array  $orderGoods 数据格式取决于下单设备
     * @param  int   $deviceid   设备编号 整形-机柜、默认来自于微信商城
     * @return mixed ERROR_STOCK_NOT_ENOUGH、ERROR_MAINTENANCE、array
     */
    public function suborder($orderGoods, $deviceid=null) {
        $res = 'ERROR_SYSTEM';

        $ordersn = ShoppingOrder::generateOrderSn();

        $OrderModel      = new ShoppingOrder();
        $OrderGoodsModel = new ShoppingOrderGoods();
        //创建订单
        if ($deviceid && isset($orderGoods['pathway'])) {
            //查询机柜内商品
            $cabinetGoods = CabinetPathway::find()->where([
                                'cabinetid'=>$deviceid, 'pathway'=>$orderGoods['pathway']
                                                ])->orderBy('pathwayid DESC')->one();
            if (!$cabinetGoods) {
                return 'ERROR_MAINTENANCE';
            }
            $goodsInfo = ShoppingGoods::find()->where(['id'=>$cabinetGoods['goodsid']])->one();
            // $cabinetGoods = array_column($cabinetGoods, null, 'goodsid');

            //库存不足无法下单
            if ($cabinetGoods->stock < $orderGoods['num']) {
                return 'ERROR_STOCK_NOT_ENOUGH';
            //维护和缺货状态无法提交订单
            } else if ($cabinetGoods->status!==0) {
                return 'ERROR_MAINTENANCE';
            } else {
                $OrderModel->weid       = 0;
                $OrderModel->uid        = 0;
                $OrderModel->source     = 'BOX';  //WX-微信商城，BOX-机柜
                $OrderModel->cabinetid  = $deviceid;
                $OrderModel->ordersn    = $ordersn;
                $OrderModel->status     = 0;
                $OrderModel->sendtype   = 2; //1上门安装，2自提
                $OrderModel->paytype    = 2; //1余额，2在线
                $OrderModel->goodstype  = 1;
                $OrderModel->addressid  = 0;
                $OrderModel->createtime = time();
                $OrderModel->ext        = "机柜用户创建订单(机柜号{$deviceid})";  //订单日志记录
                //订单金额
                $OrderModel->taxtotal   = 0;
                $OrderModel->price      = $cabinetGoods->price*$orderGoods['num'];  //实付
                $OrderModel->goodsprice = $cabinetGoods->price*$orderGoods['num'];  //订单总价
                if($OrderModel->save()) {
                    $OrderGoodsModel->orderid    = $OrderModel->id;
                    $OrderGoodsModel->goodsid    = $cabinetGoods->goodsid;
                    $OrderGoodsModel->price      = $cabinetGoods->price;
                    $OrderGoodsModel->total      = $orderGoods['num'];
                    $OrderGoodsModel->pathway    = $orderGoods['pathway'];
                    $OrderGoodsModel->optionid = $OrderGoodsModel->optionname = '';
                    $OrderGoodsModel->createtime  = time();
                    $OrderGoodsModel->cancelgoods = 0;
                    $OrderGoodsModel->status      = 0;
                    $OrderGoodsModel->deleted     = 0;
                    $OrderGoodsModel->sid         = 0;
                    $OrderGoodsModel->save();
                    if ($OrderGoodsModel->save()) {
                        $res = [
                            'timeout' => 15*60,
                            'name'    => $goodsInfo->title,
                            'sku'     => $goodsInfo->productsn,
                            'num'     => $orderGoods['num'],
                            'price'   => strval($cabinetGoods->price),
                            'amount'  => strval($cabinetGoods->price*$orderGoods['num']),
                            'ordersn' => $ordersn,
                            'qrurl'   => 'http://mall.troto.com.cn/order/prepay?sn='.$ordersn,
                            'qrtext'  => '请使用微信扫描二维码进行支付',
                        ];
                    }
                }
            }
        }

        return $res;
    }

}
