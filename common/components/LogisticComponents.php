<?php
namespace common\components;

/**
 * 物流接口封装
 */
class LogisticComponents extends \yii\base\Object{

    /**
     * 百度开放平台快递查询抓取
     * @param string $sn 快递单号
     * @return array
     */
    public static function BdKuaidi($sn){
    	$response = [
    		'code'    => -200,
			'process' => [],
			'company' => [],
    	];
        $queryUrl = 'https://sp0.baidu.com/9_Q4sjW91Qh3otqbppnN2DJv/pae/channel/data/asyncqury?cb=SDYK&appid=4001&com=&nu=&vcode=&token=';
	    $queryUrl.= '&nu='.$sn.'&_='.time();
	    $result = self::curlBaiduContent($queryUrl);
	    $result2 = str_replace('/**/SDYK(', '', $result);
	    $result2 = substr($result2, 0, strlen($result2)-1);
	    $result2 =  json_decode(iconv("GBK", "UTF-8//IGNORE", $result2));
	    if ($result2->status=='0') {
	        if ($result2->data->info->status) {
	            $response['code'] = 200;
	            $response['process'] = self::logisticsDataFilter($result2->data->info->context);
	            $response['company'] = array(
	                    'icon' => $result2->data->company->icon->normal,
	                    'name' => $result2->data->company->fullname,
	                    'tel'  => $result2->data->company->tel,
	                    'website' => $result2->data->company->website->url
	                );
	            // $response['service'] = $result2->data->company->auxiliary;
	        }
	    }
	    return $response;
    }

	/**
	 * 自定义过滤格式化百度接口返回的快递进度数据
	 * @param  object $data 百度平台返回的完整原始数据
	 * @return array 过滤处理后的数据
	 */
	private static function logisticsDataFilter($data) {
	    foreach ($data as $metaKey => $metaOne) {
	        $data[$metaKey] = array(
	                'time'=>date('Y-m-d H:i:s', $metaOne->time),
	                'desc'=>$metaOne->desc
	            );
	    }
	    return $data;
	}

	/**
	 * 模拟访问抓取百度api结果
	 * @param  string $url 需要抓取的百度页面链接
	 * @return string 正常访问百度链接获得的数据
	 */
	private static function curlBaiduContent($url) {
	    $cookie_jar = tmpfile();
	    //第一次获取百度会话数据
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, 'https://www.baidu.com/');
	    curl_setopt($ch, CURLOPT_HEADER, false); //设定是否输出页面内容
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_HTTPGET, 1);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_jar); ////设置文件读取并提交的cookie路径
	    curl_exec($ch);
	    //curl_close($ch);
	    //$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_jar);
	    curl_setopt($ch, CURLOPT_HTTPGET, 1);
	    curl_setopt($ch, CURLOPT_REFERER, 'https://www.baidu.com/index.php');
	    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.73 Safari/537.36");
	    return curl_exec($ch);
	}

}