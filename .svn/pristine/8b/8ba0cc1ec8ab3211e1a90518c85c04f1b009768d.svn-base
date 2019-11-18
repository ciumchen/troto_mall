<?php
namespace common\extensions;

use Exception;

class Ueswt{

/*--------------------------------
功能:HTTP接口 发送短信

修改日期:   2013-04-08
请求地址 :
请求地址是客户接口程序调用时请求的url地址，采用的是http post 接口，地址是
http://inter.ueswt.com/sms.aspx 对应UTF-8
http://inter.ueswt.com/smsGBK.aspx 对应GB2312

说明: http://inter.ueswt.com/smsGBK.aspx?action=send&userid=12&account=账号&password=密码&mobile=手机号码(15023239810,13527576163)&content=内容&sendTime=&extno=
      2015-11-20下午五点跟优易客服（电话13689594989/QQ53261300）沟通，下面为老接口
返回值
    在接收到客户端发送的http请求后，返回以xml的方式返回处理结果。格式为：

    <?xml version="1.0" encoding="utf-8" ?>
    <returnsms>
    <returnstatus>status</returnstatus> ---------- 返回状态值：成功返回Success 失败返回：Faild

    <message>message</message> ---------- 返回信息：见下返回信息提示
    <remainpoint> remainpoint</remainpoint> ---------- 返回余额

    <taskID>taskID</taskID>  -----------  返回本次任务的序列ID
    <successCounts>successCounts</successCounts> --成功短信数：当成功后返回提交成功短信数
    </returnsms>
    
    返回信息提示:
    返回信息提示              说明
    ok      ---------------     提交成功
    用户名或密码不能为空  -------     提交的用户名或密码为空
    发送内容包含sql注入字符   -------     包含sql注入字符
    用户名或密码错误    -------     表示用户名或密码错误
    短信号码不能为空    ------      提交的被叫号码为空
    短信内容不能为空    ------      发送内容为空
    包含非法字符：     -------     表示检查到不允许发送的非法字符
    对不起，您当前要发送的量大于您当前余额-----    当支付方式为预付费是，检查到账户余额不足
    其他错误        ---------------其他数据库操作方面的错误

--------------------------------*/
    public static function sendSMS($mobile = null, $content = null, $time='',$mid='') {
        $userid='11094';                        //企业ID
        $account = '';        //用户账号
        $password = '';  
        if($mobile == null) {
            return -111;
        }

        if($content == null) {
            return -404;
        }

        $http = 'http://inter.ueswt.com/sms.aspx';
        $action='send';

        $data = [
                'action'=>$action,       //发送任务命令
                'userid'=>$userid,       //企业ID
                'account'=>$account,     //用户账号
                'password'=>$password,   //密码
                'mobile'=>$mobile,       //号码
                'content'=>$content,     //内容 如果对方是utf-8编码，则需转码iconv('gbk','utf-8',$content); 如果是gbk则无需转码
                'time'=>$time,           //定时发送
                'extno'=>$mid            //子扩展号
            ];

        $re = self::postSMS($http, $data);

        if(strstr($re,'ok')) {
            return 200;
        } else {
            return $re;
        }
    }

    public function postSMS($url, $data='') {
        $post= '';
        $row = parse_url($url);
        $host = $row['host'];
        $port = isset($row['port']) ? $row['port'] : 80;
        $file = $row['path'];

        while (list($k,$v) = each($data)){
            $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
        }

        $post = substr( $post , 0 , -1 );
        $len = strlen($post);
        $fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
        if (!$fp) {
        var_dump($fp);
            return "$errstr ($errno)\n";
        } else {
            $receive = '';
            $out = "POST $file HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Content-type: application/x-www-form-urlencoded\r\n";
            $out .= "Connection: Close\r\n";
            $out .= "Content-Length: $len\r\n\r\n";
            $out .= $post;      
            fwrite($fp, $out);
            while (!feof($fp)) {
                $receive .= fgets($fp, 128);
            }
            fclose($fp);
            $receive = explode("\r\n\r\n",$receive);
            unset($receive[0]);
        var_dump($receive);
            return implode("",$receive);
        }
    }

}