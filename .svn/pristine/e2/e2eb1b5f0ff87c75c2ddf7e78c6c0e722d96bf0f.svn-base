<?php
$sn = isset($_POST['sn'])&&is_numeric($_POST['sn']) ? $_POST['sn'] : null ;
// $sn = '880304372718783196'; //测试

$response = array();
$response['status'] = -200;
$response['msg'] = '非法请求';
$response['data'] = array();


if (validateSameDomain()) {
    $queryUrl = 'https://sp0.baidu.com/9_Q4sjW91Qh3otqbppnN2DJv/pae/channel/data/asyncqury?cb=YJKD&appid=4001&com=&vcode=&token=';
    $queryUrl.= '&nu='.$sn.'&_='.time();
    $result = curlBaiduContent($queryUrl);
    $result2 = str_replace('/**/YJKD(', '', $result);
    $result2 = substr($result2, 0, strlen($result2)-1);
    $result2 =  json_decode(iconv("GBK", "UTF-8//IGNORE", $result2));

    $response['msg'] = $result2->msg;
    if ($result2->status=='0') {
        $response['status'] = 200;
        if ($result2->data->info->status) {
            $response['msg'] = '查询成功！';
            $response['data']['process'] = logisticsDataFilter($result2->data->info->context);
            $response['data']['company'] = array(
                    'icon' => $result2->data->company->icon->normal,
                    'name' => $result2->data->company->fullname,
                    'tel'  => $result2->data->company->tel,
                    'website' => $result2->data->company->website->url
                );
            $response['data']['service'] = $result2->data->company->auxiliary;
        }
    } else {
        $response['status'] = 300;
    }
}

echo json_encode($response);
exit();

////////////////////////////////////////////////////////////////////////////////////////

/**
 * 验证是否为同域请求
 * @return bool
 */
function validateSameDomain() {
    $serverName = strtolower($_SERVER['SERVER_NAME']);
    if (isset($_SERVER["HTTP_REFERER"]) && strrpos(strtolower($_SERVER["HTTP_REFERER"]), '//'.$serverName) !== FALSE) {
        return true;
    } else {
        return false;
    }
}

/**
 * 模拟访问抓取百度api结果
 * @param  string $url 需要抓取的百度页面链接
 * @return string 正常访问百度链接获得的数据
 */
function curlBaiduContent($url) {
    $cookie_jar = tmpfile();

    //第一次获取百度会话数据
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.baidu.com/index.php?'.microtime());
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

/**
 * 自定义过滤格式化百度接口返回的快递进度数据
 * @param  object $data 百度平台返回的完整原始数据
 * @return array 过滤处理后的数据
 */
function logisticsDataFilter($data) {
    foreach ($data as $metaKey => $metaOne) {
        $data[$metaKey] = array(
                'time'=>date('Y-m-d H:i:s', $metaOne->time),
                'desc'=>$metaOne->desc
            );
    }
    return $data;
}