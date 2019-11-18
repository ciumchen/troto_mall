<?php
/*********************************************************************************************
 * 消息队列处理程序
 * 原理说明：
 * 1、检查memcached队列里msg_key_list待发消息的key并进行处理
 * 2、处理成功则将key移到msg_key_succ，并增加finishDt；处理失败则给原来消息数组中的sendTimes+1
 * 3、发送消息过程中遇到sendTimes超过5次依然失败的,key移到msg_key_fail中
 * 4、每天上午5点半将处理成功和失败的消息移入数据库ims_msg_queen表中，并清除这些消息数据
 * 5、每轮检查间隔时间1s；设立定时任务每分钟检查本程序进程，退出则利用bash启动（监控脚本monitor_clearMsgQueen.sh）
 * 脚本要求：
 *  php多进程、curl、memcache等库支持
 *  memcached监控(web可访问)：http://域名/task/memcache.php
 ********************************************************************************************/
error_reporting(E_ALL & ~E_DEPRECATED);  //E_ALL & ~E_DEPRECATED
set_time_limit(0);
ini_set('memory_limit','2048M');
////////////////////////////////
define('IN_IA', true);
define('UNACID', 16); //使用公众号
define('ConcurrentQuantity', 20); //并发处理的进程数量,每次查询队列取出任务数量
define('AppRoot', dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('TIMESTAMP', time());
define('DATECAHCE', date('Ymd'));
define('DEADTIME', 60); //检查token过期时间
define('DBCONN', getDBConn());  //取得一个数据库链接

///////////////////////////////////////////
include(AppRoot.'data/config.php');
$_W['config'] = $config;
require_once(AppRoot.'framework/class/MemcacheClient.class.php');

//判断key队列，如果不存在建立对应的空队列
if (!memc()->get('msg_key_list')) {
  memc()->add('msg_key_list', '');
}
if (!memc()->get('msg_key_succ')) {
  memc()->add('msg_key_succ', '');
}
if (!memc()->get('msg_key_fail')) {
  memc()->add('msg_key_fail', '');
}

/*
// 3  -- oGxhxt_I-4RkNCq1zzvVZ-ZXgCnc
// 37 -- opZZjs3o2cO1w9ztqdF4WEUELbu4
for ($i=1; $i < 31; $i++) {
  $msg = array(
      'uid' =>2555, 'openid'=>'oGxhxtw7umBfN2Au8ZShDydhKUNI',
      'type'=>1, 'createDt'=>time()-rand(0,10),
      'text' =>'No.'.$i." - 特大喜讯传来！\n有20元进入您钱包！您的 富一代 杜海燕 在2015-09-01 16:40:31 购买了阿克苏糖心苹果，快去查看吧！"
    );
  $key = memc()->set($msg);
  memc()->update('msg_key_list', memc()->get('msg_key_list').$key.',');
}
echo memc()->get('msg_key_list');
exit();
*/
//var_dump(memc()->get('msg_key_list'),memc()->get('msg_key_succ'),memc()->get('msg_key_fail'));

//每一轮处理间歇1s
do {
  //取得待发消息key队列
  $msgKeyListStr = memc()->get('msg_key_list');
  $msgKeyList = array_filter(explode(',', $msgKeyListStr));

  //处理待发消息
  $loopTimes = 0;
  while (!empty($msgKeyList)) {
    //获得全部消息队列数据
    $msgListTotal = count($msgKeyList);
    foreach ($msgKeyList as $msgKey) {
      $queen[] = memc()->get($msgKey);
    }

    //判断应该建立的进程数量
    if ($msgListTotal<ConcurrentQuantity) {
      $processTotal = $msgListTotal;
    } else {
      $processTotal = ConcurrentQuantity;
    }

    //获得微信平台通讯token
    $account = getAccount();
    $token = isset($account['access_token']['token']) ? $account['access_token']['token'] : '';
    //检查token是否过期, 比较时间为过期前十分钟;过期则刷新
    if( isset($account['access_token']) && ($account['access_token']['expire']-600)<time() ) { 
      $account = freshAccount();
      $token = $account['access_token']['token'];
    }

    for ($key=0; $key < $processTotal; $key++) { 
      $pids[$key] = pcntl_fork();
      if ($pids[$key] == -1) {
        die("could not fork");
      } else if ($pids[$key]==0) {
        $qkey = $loopTimes*$processTotal + $key;
        $content = array();
        if ($queen[$qkey]['type']=='2') {
          $content['image'] = $queen[$qkey]['text'];
        } else {
          $content['text'] = $queen[$qkey]['text'];
        }

        if ($token && trim($queen[$qkey]['openid']) && isset($queen[$qkey]['type']) && !empty($content)) {
          $rs = sendPostToOpenid($token, trim($queen[$qkey]['openid']), $queen[$qkey]['type'], $content);
          if ($rs) {
            $queen[$qkey]['finishDt'] = time();
            $ret = memc()->update($msgKey, $queen[$qkey]);
            //发送成功则更新消息队列待发送、成功的key
            if ($ret) {
              memc()->update('msg_key_succ', memc()->get('msg_key_succ').$msgKey.',');
              $msgKeyListStr = str_replace(array($msgKey.',', $msgKey), '', $msgKeyListStr);
              memc()->update('msg_key_list', $msgKeyListStr);
            } else if (!isset($queen[$qkey]['sendTimes']) || $queen[$qkey]['sendTimes']>5) {        
              memc()->update('msg_key_fail', memc()->get('msg_key_fail').$msgKey.',');
              $msgKeyListStr = str_replace(array($msgKey.',', $msgKey), '', $msgKeyListStr);
              memc()->update('msg_key_list', $msgKeyListStr);
            }
          }
        }
        exit;
      }
    }

    foreach ($pids as $pk=>$pid){
      if ($pid) {
        pcntl_waitpid($pid, $status);
      }
    }

    //再次检查待发消息key队列
    $msgKeyListStr = memc()->get('msg_key_list');
    $msgKeyList = array_filter(explode(',', $msgKeyListStr));

    $loopTimes++;  //进入下一轮
  }

  //每天五点半清理一次发送失败的消息到db中
  if (date('Hi')=='0530') {
    //转移处理失败的消息
    $msgKeyList = array_filter(explode(',', memc()->get('msg_key_fail')));
    $insertSQL = 'INSERT INTO ims_msg_queen (uid,openid,create_dt,push_dt,push_type,push_txt) values ';
    foreach ($msgKeyList as $msgKey) {
      $msgOne = memc()->get($msgKey);
      if (($msgOne['createDt']+3600)<time()) {
        $msgOne['finishDt'] = isset($msgOne['finishDt']) ? $msgOne['finishDt'] :'';
        $insertSQL .= "('{$msgOne['uid']}','{$msgOne['openid']}','{$msgOne['createDt']}','{$msgOne['finishDt']}','{$msgOne['type']}','{$msgOne['text']}'),";  //
      }
    }
    $insertSQL = substr($insertSQL, 0, -1);
    if (mysql_query($insertSQL)) {
      memc()->update('msg_key_fail', '');
      foreach ($msgKeyList as $key) {
        memc()->delete($key);
      }
    }

    //转移处理成功的消息
    $msgKeyList = array_filter(explode(',', memc()->get('msg_key_succ')));
    $insertSQL = 'INSERT INTO ims_msg_queen (uid,openid,create_dt,push_type,push_txt) values ';
    foreach ($msgKeyList as $msgKey) {
      $msgOne = memc()->get($msgKey);
      if (($msgOne['createDt']+3600)<time()) {
        $insertSQL .= "('{$msgOne['uid']}','{$msgOne['openid']}','{$msgOne['createDt']}','{$msgOne['type']}','{$msgOne['text']}'),";  //
      }
    }
    $insertSQL = substr($insertSQL, 0, -1);
    if (mysql_query($insertSQL)) {
      memc()->update('msg_key_succ', '');
      foreach ($msgKeyList as $key) {
        memc()->delete($key);
      }
    }
  }
} while ((sleep(3) || 1==1));


//////////////////////////////////////////////////
function getDBConn() {
  require_once(AppRoot.'data'.DIRECTORY_SEPARATOR.'config.php');
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

function waitPushMsgList($offset=1000) {
  $sql = "SELECT count(msgId) as total from ims_msg_queen where push_dt is null";
  $total = fetchResult(mysql_query($sql));
  $total = $total[0]['total'];

  $sql = 'SELECT tba.*, tbb.openid as openid2 FROM ims_msg_queen AS tba LEFT JOIN ims_mc_mapping_fans AS tbb ON tba.uid = tbb.uid WHERE tba.push_dt IS NULL';
  $sql.= ' limit '.$offset;
  $data = fetchResult(mysql_query($sql));
  return array('total'=>$total, 'data'=>$data);
}

function getAccount(){
  $sql = 'SELECT * from ims_account_wechats where uniacid='.UNACID;
  $rs = fetchResult(mysql_query($sql));
  $rs = isset($rs[0]) ? $rs[0] : array();
  if (!empty($rs)) {
    $rs['access_token'] = unserialize($rs['access_token']);
    $rs['jsapi_ticket'] = unserialize($rs['jsapi_ticket']);
  }
  return $rs;
}

function freshAccount(){
  $account = getAccount();
  if (empty($account['key']) || empty($account['secret'])) {
    exit('[ERROR] '.date('Y-m-d H:i:s').' 未填写公众号的 appid 及 appsecret！');
  } else {
    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$account['key']}&secret={$account['secret']}";
    $rs = httpRequest($url);
    $mpResponse = json_decode($rs);
    if ( (is_array($mpResponse) && isset($mpResponse['errcode']) && $mpResponse['errcode']!='0') ||  (is_object($mpResponse) && isset($mpResponse->errcode) && $mpResponse->errcode!='0')) {
      exit('[ERROR] '.date('Y-m-d H:i:s').' 获取微信公众号授权失败, 请稍后重试！错误详情:'.$mpResponse['errno'].' - '.$mpResponse['errmsg']);
    } else {
      $access_token = array();
      $access_token['token'] = $mpResponse->access_token;
      $access_token['expire'] = time() + $mpResponse->expires_in - 200;
      mysql_query("update ims_account_wechats set access_token='".serialize($access_token)."' where acid=".UNACID);
      return array('access_token'=>$access_token);
    }
  }
}

/**
 * 微信推送
 * @param $token str 有效token
 * @param $openid str 关注用户的openid
 * @param $type int 消息类型：1-文本,2-图片
 * @param $msg array
 */
function sendPostToOpenid($token, $openid, $type, $msg=array()){
  $message = array();
  $message['touser'] = trim($openid);
  //暂时只支持文本和图片，可扩展
  if ($type=='2') {
    $message['msgtype'] = 'image';
    $message['image']['media_id'] = $msg['image'];
  } else {
    $message['msgtype'] = 'text';
    $message['text']['content'] = $msg['text'];
  }

  $message = json_en($message);

  $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
  $response = httpRequest($url, 'post', $message);
  $result = json_decode($response);
  //var_dump(array($url, $response, $result));
  if (isset($result->errcode)&&$result->errcode=='0') {
    return true;
  } else {
    echo '[ERROR] errcode:'.$result->errcode.', errmsg:'.$result->errmsg."\n";
    return false;
  }
}

/*
 * http-curl操作封装
 * @param $url string
 * @param $postType bool 是否使用post方式请求
 * @param $postData array 请求数据
 * @param $timeout  int 操作超时时间
 * @return bool
 */
function httpRequest($url, $postType=true, $postData=array(), $timeout=600){
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');

  if ($postType) {
    if (is_array($postData) && !empty($postData)) {
      $filepost = false;
      foreach ($postData as $name => $value) {
        if (substr($value, 0, 1) == '@') {
          $filepost = true;
          break;
        }
      }
      if (!$filepost) {
        $postData = http_build_query($postData);
      }
    }
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
  }
  $data  = curl_exec($ch);
  $errno = curl_errno($ch);
  $error = curl_error($ch);
  curl_close($ch);
  if($errno) {
    echo '[ERROR] '.date('Y-m-d H:i:s').' CURL发生错误。'.$errno." - {$error}\r\n";
    return false;
  } else {
    return $data;
  }
}

/*
 * json编码（区别php库函数json_encode，因为这里编码后的数据中文不会转码）
 * @param $arr array
 * @return json_string
 */
function json_en($arr) {
  $parts = array ();
    $is_list = false;
    //Find out if the given array is a numerical array
    $keys = array_keys ( $arr );
    $max_length = count ( $arr ) - 1;
    if (($keys [0] === 0) && ($keys [$max_length] === $max_length )) { //See if the first key is 0 and last key is length - 1
        $is_list = true;
        for($i = 0; $i < count ( $keys ); $i ++) { //See if each key correspondes to its position
            if ($i != $keys [$i]) { //A key fails at position check.
                $is_list = false; //It is an associative array.
                break;
            }
        }
    }
    foreach ( $arr as $key => $value ) {
        if (is_array ( $value )) { //Custom handling for arrays
            if ($is_list)
                $parts [] = json_en ( $value ); // :RECURSION:
            else
                $parts [] = '"' . $key . '":' . json_en ( $value ); // :RECURSION:
        } else {
            $str = '';
            if (! $is_list)
                $str = '"' . $key . '":';
            //Custom handling for multiple data types
            if (is_numeric ( $value ) && $value<2000000000)
                $str .= $value; //Numbers
            elseif ($value === false)
                $str .= 'false'; //The booleans
            elseif ($value === true)
                $str .= 'true';
            else
                $str .= '"' . addslashes ( $value ) . '"'; //All other things
            // :TODO: Is there any more datatype we should be in the lookout for? (Object?)
            $parts [] = $str;
        }
    }
    $json = implode ( ',', $parts );
    if ($is_list)
        return '[' . $json . ']'; //Return numerical JSON
    return '{' . $json . '}'; //Return associative JSON
}

function error($errno, $message = '') {
  return array(
    'errno' => $errno,
    'message' => $message,
  );
}

function is_error($data) {
  if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
    return false;
  } else {
    return true;
  }
}


function generateKey(){
  static $randCharArr = array(
    'a','b','c','d','e','f','g','h','i','j','k','l','m',
    'n','o','p','q','r','s','t','u','v','w','x','y','z',
    '0','1','2','3','4','5','6','7','8','9'
  );
  //理论值可以生成key的数量 2,176,782,336
  $key = $randCharArr[rand(0,35)].$randCharArr[rand(0,35)].$randCharArr[rand(0,35)];
  $key .= $randCharArr[rand(0,35)].$randCharArr[rand(0,35)].$randCharArr[rand(0,35)];
  return $key;
}