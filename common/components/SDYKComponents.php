<?php
namespace common\components;

use Yii;
use Exception;
use yii\base\Object;

/**
 * 封装的http request简单库
 */
class SDYKComponents extends Object{

	const ORDERSN_PREFIX = 'SDYK';

	public function __construct() {
		parent::__construct();
	}

	/*
	 * 商户订单
	 * @return string
	 */
	public static function generateSN(){
        return self::ORDERSN_PREFIX.date('YmdHis').substr(microtime(), 2, 3).rand(1000,9999);
	}

}