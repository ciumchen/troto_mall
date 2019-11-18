<?php
defined('IN_IA') or exit('Access Denied');

$config = [];

$config['customs']      = 'SHENZHEN';
$config['site']['name'] ='多轮多服务商城';

$config['db']['host']     = '127.0.0.1';
$config['db']['username'] = 'root';
/*$config['db']['password'] = 'DBP82E81a806defc596DFf2c968d';*/
$config['db']['password'] = '';
$config['db']['port']     = '3306';
$config['db']['database'] = 'troto_mall';
$config['db']['charset']  = 'utf8';
$config['db']['pconnect'] = 0;
$config['db']['tablepre'] = 'ims_';


//=============  CONFIG COOKIE  =============//
$config['cookie']['pre']    = '9c2a_';
$config['cookie']['domain'] = '';
$config['cookie']['path']   = '/';


//=============  CONFIG SETTING  =============//
$config['setting']['charset']      = 'utf-8';
$config['setting']['cache']        = 'mysql';
$config['setting']['timezone']     = 'Asia/Shanghai';
$config['setting']['memory_limit'] = '256M';
$config['setting']['filemode']     = 0644;
$config['setting']['authkey']      = '1a024426';
$config['setting']['founder']      = '1';
$config['setting']['development']  = true;
$config['setting']['referrer']     = 0;


//=============  CONFIG UPLOAD  =============//
$config['upload']['attachdir']  = 'web/uploads';
//如果配置http://cdn-domain/，否则设置为 uploads
$config['upload']['cdnuri']     = 'http://assets.troto.com.cn/';
$config['upload']['image']['limit']      = 5000;
$config['upload']['image']['extentions'] = ['gif', 'jpg', 'jpeg', 'png'];
$config['upload']['audio']['extentions'] = ['mp3'];
$config['upload']['audio']['limit']      = 5000;
$config['upload']['file']['extentions']  = ['doc', 'docx', 'xls', 'xlsx'];
$config['upload']['file']['limit']       = 15000;


$config['memcache'][1]['host'] = '10.0.1.200';
$config['memcache'][1]['port'] = '11211';

$config['qiniu'][1]['accessKey'] = '19aZemJHKlubUxAGaeucGqbQ5818U8IxmFakxOHv';
$config['qiniu'][1]['secretKey'] = '3uCenqvufYjb1CwPHOMLhegyw3fjF0b_Xv6Vznjd';
$config['qiniu'][1]['site']      = '7xlo0x.com1.z0.glb.clouddn.com';

//gh_8457daa16c72
$config['wechat']['appid']       = 'wx2102b045a17e5774';
$config['wechat']['mchid']       = '1335901701';
$config['wechat']['mchkey']      = 'e67b4adb893934c5c4f83de9666ac065';
$config['wechat']['ord_upd_url'] = 'https://api.mch.weixin.qq.com/cgi-bin/mch/customs/customdeclareorder';
$config['access_token_url'] ='http://mall.troto.com.cn/wechat/get-access-token';
$config['mch_customs_no']   = '4403160UTK';
