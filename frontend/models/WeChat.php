<?php
namespace frontend\models;

use Yii;

class WeChat {
    public $appId;
    public $appSecret;
    public $cache;
    public $logFile;

    public static $key_wx_token  = 'wx_access_token';
    public static $key_wx_ticket = 'wx_jsapi_ticket';

    public function __construct() {
        $this->appId     = Yii::$app->params['wechat']['AppID'];
        $this->appSecret = Yii::$app->params['wechat']['AppSecret'];
        $this->cache     = Yii::$app->cache;
        $this->logFile   = dirname(__DIR__) . '/logs/wxapi.log';
    }

    /**
     * 全局存储与更新access_token（接口权限：每天限制请求2000次）
     *PS.目前access_token的有效期通过返回的expire_in来传达，目前是7200秒之内的值。
     *   中控服务器需要根据这个有效时间提前去刷新新access_token。在刷新过程中，
     *   中控服务器对外输出的依然是老access_token，此时公众平台后台会保证在刷新短时间内，
     *   新老access_token都可用，这保证了第三方业务的平滑过渡
     *
     * access_token的有效时间可能会在未来有调整，所以中控服务器不仅需要内部定时主动刷新，
     * 还需要提供被动刷新access_token的接口，这样便于业务服务器在API调用获知access_token已超时的情况下，
     * 可以触发access_token的刷新流程。官方返回的数据有效期为7200s，重复获取将导致上次获取的access_token失效
     */
    public static function token() {
        $wechatObj = new self();
        //本地开发测试使用防止本地刷新了服务器上的token
        if (substr($_SERVER['SERVER_ADDR'], 0,5)=='10.0.') {
            return self::httpGet('http://mall.troto.com.cn/wechat/get-access-token');
        }
        $accessToken = $wechatObj->cache->get(self::$key_wx_token);

        if ($accessToken === false) {
            //更新token
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential'
                . '&appid='.$wechatObj->appId.'&secret='.$wechatObj->appSecret;
            $response = self::httpGet($url);
            $result = json_decode($response);
            if (isset($result->access_token)) {
                $accessToken = $result->access_token;
                $wechatObj->cache->set(self::$key_wx_token, $accessToken, 5400);
            } else {
                $logTxt = '[ERROR] '.date('Y-m-d H:i:s').' '.$response."\n";
                file_put_contents($wechatObj->logFile, $logTxt, FILE_APPEND);
            }

            //更新JsApiTicket
            $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token='.$accessToken;
            $response2 = self::httpGet($url);
            $result2 = json_decode($response2);
            if (isset($result2->ticket)) {
                //这个数据，不要大于token的频率，以免冲突失效。
                $wechatObj->cache->set(self::$key_wx_ticket, $result2->ticket, 5400);
            } else {
                $logTxt = '[ERROR] '.date('Y-m-d H:i:s').' '.$response2."\n";
                file_put_contents($wechatObj->logFile, $logTxt, FILE_APPEND);
            }
        }

        return $accessToken;
    }


    public static function followQrcode($uid, $type = '')
    {
        $token = WeChat::token();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $token;

        if ($type === '') {
            $post = [
                "expire_seconds" => 2592000, //30天
                "action_name" => "QR_SCENE",
                "action_info" => [
                    "scene" => ["scene_id" => $uid]
                ]
            ];

            $result = json_decode(WeChat::get_curl($url, $post));
        } else {
            $post = [
                "action_name" => "QR_LIMIT_STR_SCENE",
                "action_info" => [
                    "scene" => ["scene_str" => 'tomabcadfadsfasd123']
                ]
            ];

            $result = json_decode(WeChat::get_curl($url, $post));
        }

        //var_dump($result);

        $qrcodeurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=';
        //echo $qrcodeurl . $result->ticket;
        //echo '<br/>';

        return $result;
    }

    //$post = [];
    public static function get_curl($url, $post)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, WeChat::json_encode_cn($post));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }

    private static function json_encode_cn($array)
    {
        WeChat::arrayRecursive($array, 'urlencode', true);
        $json = json_encode($array);

        return urldecode($json);
    }

    private static function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
    {
        static $recursive_counter = 0;

        if (++$recursive_counter > 1000) {
            die('possible deep recursion attack');
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                WeChat::arrayRecursive($array[$key], $function, $apply_to_keys_also);
            } else {
                $array[$key] = $function($value);
            }

            if ($apply_to_keys_also && is_string($key)) {
                $new_key = $function($key);
                if ($new_key != $key) {
                    $array[$new_key] = $array[$key];
                    unset($array[$key]);
                }
            }
        }

        $recursive_counter--;
    }

    public function getSignPackage()
    {
        $jsapiTicket = self::getJsApiTicket();

        // 注意 URL 一定要动态获取，不能 hardcode.
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $timestamp = time();
        $nonceStr = $this->createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($string);

        $signPackage = array(
            "appId" => $this->appId,
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    /**
     *
     * @return Array
     */
    public function orderQuery($out_trade_no)
    {
        require_once "../wxpay/lib/WxPay.Api.php";

        $input = new WxPayOrderQuery();
        $input->SetOut_trade_no($out_trade_no);

        $result = WxPayApi::orderQuery($input);

        return $result;
    }

    private function createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    private function getJsApiTicket() {
        return $this->cache->get(WeChat::$key_wx_ticket);
    }

    public static function httpGet($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}