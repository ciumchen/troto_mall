<?php
defined('IN_IA') or exit('Access Denied');

function d($var){
	if(is_array($var) || is_object($var)){
		echo '<pre>';
		print_r($var);
		echo '</pre>';
	}else{
		var_dump($var);
	}
	exit();
}

function ver_compare($version1, $version2) {
	if(strlen($version1) <> strlen($version2)) {
		$version1_tmp = explode('.', $version1);
		$version2_tmp = explode('.', $version2);
		if(strlen($version1_tmp[1]) == 1) {
			$version1 .= '0';
		}
		if(strlen($version2_tmp[1]) == 1) {
			$version2 .= '0';
		}
	}
	return version_compare($version1, $version2);
}


function istripslashes($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[stripslashes($key)] = istripslashes($value);
		}
	} else {
		$var = stripslashes($var);
	}
	return $var;
}


function ihtmlspecialchars($var) {
	if (is_array($var)) {
		foreach ($var as $key => $value) {
			$var[htmlspecialchars($key)] = ihtmlspecialchars($value);
		}
	} else {
		$var = str_replace('&amp;', '&', htmlspecialchars($var, ENT_QUOTES));
	}
	return $var;
}


function isetcookie($key, $value, $maxage = 0) {
	global $_W;
	$expire = $maxage != 0 ? (TIMESTAMP + $maxage) : 0;
	return setcookie($_W['config']['cookie']['pre'] . $key, $value, $expire, $_W['config']['cookie']['path'], $_W['config']['cookie']['domain']);
}


function getip() {
	$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
        foreach ($matches[0] AS $xip) {
            if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                $ip = $xip;
                break;
            }
        }
    }
	return $ip;
}


function token($specialadd = '') {
	global $_W;
	$hashadd = defined('IN_MANAGEMENT') ? 'for management' : '';
	return substr(md5($_W['config']['setting']['authkey'] . $hashadd . $specialadd), 8, 8);
}


function random($length, $numeric = FALSE) {
	$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
	if($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}


function checksubmit($var = 'submit', $allowget = 0) {
	global $_W, $_GPC;
	if (empty($_GPC[$var])) {
		return FALSE;
	}
	if ($allowget || (($_W['ispost'] && !empty($_W['token']) && $_W['token'] == $_GPC['token']) && (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
		return TRUE;
	}
	return FALSE;
}


function checkpermission($type, $target) {
	global $_W;
	if (!empty($_W['isfounder']) || empty($target)) {
		return true;
	}
	if ($type == 'wechats') {
		if (is_array($target)) {
			$weid = $target['weid'];
		} else {
			$weid = $target;
		}
		if (pdo_fetchcolumn("SELECT id FROM ".tablename('uni_account_users')." WHERE uniacid = :uniacid AND memberid = :memberid", array(':uniacid' => $weid, ':memberid' => $_W['uid']))) {
			return true;
		} else {
			return false;
		}
	}
	return true;
}



function tablename($table) {
	return "`{$GLOBALS['_W']['config']['db']['tablepre']}{$table}`";
}


function array_elements($keys, $src, $default = FALSE) {
	$return = array();
	if(!is_array($keys)) {
		$keys = array($keys);
	}
	foreach($keys as $key) {
		if(isset($src[$key])) {
			$return[$key] = $src[$key];
		} else {
			$return[$key] = $default;
		}
	}
	return $return;
}


function range_limit($num, $downline, $upline, $returnNear = true) {
	$num = intval($num);
	$downline = intval($downline);
	$upline = intval($upline);
	if($num < $downline){
		return empty($returnNear) ? false : $downline;
	} elseif ($num > $upline) {
		return empty($returnNear) ? false : $upline;
	} else {
		return empty($returnNear) ? true : $num;
	}
}

function ijson_encode($value) {
	if (empty($value)) {
		return false;
	}
	return addcslashes(json_encode($value), "\\\'\"");
}

function iserializer($value) {
	return serialize($value);
}

function iunserializer($value) {
	if (empty($value)) {
		return '';
	}
	if (!is_serialized($value)) {
		return $value;
	}
	$result = unserialize($value);
	if ($result === false) {
		$temp = preg_replace_callback('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $value);
		return unserialize($temp);
	}
	return $result;
}


function is_base64($str){
	if(!is_string($str)){
		return false;
	}
	return $str == base64_encode(base64_decode($str));
}


function is_serialized( $data, $strict = true ) {
	if (!is_string($data)) {
		return false;
	}
	$data = trim($data);
	if ('N;' == $data) {
		return true;
	}
	if (strlen( $data ) < 4) {
		return false;
	}
	if (':' !== $data[1]) {
		return false;
	}
	if ($strict) {
		$lastc = substr($data, -1);
		if (';' !== $lastc && '}' !== $lastc) {
			return false;
		}
	} else {
		$semicolon = strpos($data, ';');
		$brace = strpos($data, '}');
				if (false === $semicolon && false === $brace)
			return false;
				if (false !== $semicolon && $semicolon < 3)
			return false;
		if (false !== $brace && $brace < 4)
			return false;
	}
	$token = $data[0];
	switch ($token) {
		case 's' :
			if ($strict) {
				if ( '"' !== substr( $data, -2, 1 )) {
					return false;
				}
			} elseif (false === strpos( $data, '"')) {
				return false;
			}
					case 'a' :
		case 'O' :
			return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
		case 'b' :
		case 'i' :
		case 'd' :
			$end = $strict ? '$' : '';
			return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
	}
	return false;
}


function wurl($segment, $params = array()) {
	list($controller, $action, $do) = explode('/', $segment);
	$url = './index.php?';
	if(!empty($controller)) {
		$url .= "c={$controller}&";
	}
	if(!empty($action)) {
		$url .= "a={$action}&";
	}
	if(!empty($do)) {
		$url .= "do={$do}&";
	}
	if(!empty($params)) {
		$queryString = http_build_query($params, '', '&');
		$url .= $queryString;
	}
	return $url;
}


function murl($segment, $params = array(), $noredirect = true) {
	global $_W;
	list($controller, $action, $do) = explode('/', $segment);
	$url = './index.php?i=' . $_W['uniacid'] . '&';
	if (!empty($_W['acid'])) {
		$url .= "j={$_W['acid']}&";
	}
	if(!empty($controller)) {
		$url .= "c={$controller}&";
	}
	if(!empty($action)) {
		$url .= "a={$action}&";
	}
	if(!empty($do)) {
		$url .= "do={$do}&";
	}
	if(!empty($params)) {
		$queryString = http_build_query($params, '', '&');
		$url .= $queryString;
		if($noredirect === false) {
			$url .= '&wxref=mp.weixin.qq.com#wechat_redirect';
		}
	}
	return $url;
}


function pagination($total, $pageIndex, $pageSize = 15, $url = '', $context = array('before' => 5, 'after' => 4, 'ajaxcallback' => '')) {
	global $_W;
	$pdata = array(
		'tcount' => 0,
		'tpage' => 0,
		'cindex' => 0,
		'findex' => 0,
		'pindex' => 0,
		'nindex' => 0,
		'lindex' => 0,
		'options' => ''
	);
	if($context['ajaxcallback']) {
		$context['isajax'] = true;
	}

	$pdata['tcount'] = $total;
	$pdata['tpage'] = ceil($total / $pageSize);
	if($pdata['tpage'] <= 1) {
		return '';
	}
	$cindex = $pageIndex;
	$cindex = min($cindex, $pdata['tpage']);
	$cindex = max($cindex, 1);
	$pdata['cindex'] = $cindex;
	$pdata['findex'] = 1;
	$pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
	$pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
	$pdata['lindex'] = $pdata['tpage'];

	if($context['isajax']) {
		if(!$url) {
			$url = $_W['script_name'] . '?' . http_build_query($_GET);
		}
		$pdata['faa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['findex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['paa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['pindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['naa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['nindex'] . '\', ' . $context['ajaxcallback'] . ')"';
		$pdata['laa'] = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $pdata['lindex'] . '\', ' . $context['ajaxcallback'] . ')"';
	} else {
		if($url) {
			$pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
			$pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
			$pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
			$pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
		} else {
			$_GET['page'] = $pdata['findex'];
			$pdata['faa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['pindex'];
			$pdata['paa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['nindex'];
			$pdata['naa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
			$_GET['page'] = $pdata['lindex'];
			$pdata['laa'] = 'href="' . $_W['script_name'] . '?' . http_build_query($_GET) . '"';
		}
	}

	$html = '<div><ul class="pagination pagination-centered">';
	if($pdata['cindex'] > 1) {
		$html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
		$html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
	}
		if(!$context['before'] && $context['before'] != 0) {
		$context['before'] = 5;
	}
	if(!$context['after'] && $context['after'] != 0) {
		$context['after'] = 4;
	}

	if($context['after'] != 0 && $context['before'] != 0) {
		$range = array();
		$range['start'] = max(1, $pdata['cindex'] - $context['before']);
		$range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
		if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
			$range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
			$range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
		}
		for ($i = $range['start']; $i <= $range['end']; $i++) {
			if($context['isajax']) {
				$aa = 'href="javascript:;" onclick="p(\'' . $_W['script_name'] . $url . '\', \'' . $i . '\', ' . $context['ajaxcallback'] . ')"';
			} else {
				if($url) {
					$aa = 'href="?' . str_replace('*', $i, $url) . '"';
				} else {
					$_GET['page'] = $i;
					$aa = 'href="?' . http_build_query($_GET) . '"';
				}
			}
			$html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
		}
	}

	if($pdata['cindex'] < $pdata['tpage']) {
		$html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
		$html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
	}
	$html .= '</ul></div>';
	return $html;
}


function tomedia($src){
	global $_W;
	if (empty($src)) {
		return '';
	}
	if (strpos($src, './addons')===0) {
		return $_W['siteroot']. str_replace('./', '', $src);
	}
	$t = strtolower($src);
	if(!strexists($t, 'http://') && !strexists($t, 'https://')) {
		$src = $_W['attachurl'] . $src;
	}
	return $src;
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


function referer($default = '') {
	global $_GPC, $_W;

	$_W['referer'] = !empty($_GPC['referer']) ? $_GPC['referer'] : $_SERVER['HTTP_REFERER'];;
	$_W['referer'] = substr($_W['referer'], -1) == '?' ? substr($_W['referer'], 0, -1) : $_W['referer'];

	if(strpos($_W['referer'], 'member.php?act=login')) {
		$_W['referer'] = $default;
	}
	$_W['referer'] = $_W['referer'];
	$_W['referer'] = str_replace('&amp;', '&', $_W['referer']);
	$reurl = parse_url($_W['referer']);

	if(!empty($reurl['host']) && !in_array($reurl['host'], array($_SERVER['HTTP_HOST'], 'www.'.$_SERVER['HTTP_HOST'])) && !in_array($_SERVER['HTTP_HOST'], array($reurl['host'], 'www.'.$reurl['host']))) {
		$_W['referer'] = $_W['siteroot'];
	} elseif(empty($reurl['host'])) {
		$_W['referer'] = $_W['siteroot'].'./'.$_W['referer'];
	}
	return strip_tags($_W['referer']);
}


function strexists($string, $find) {
	return !(strpos($string, $find) === FALSE);
}


function cutstr($string, $length, $havedot = false, $charset='') {
	global $_W;
	if(empty($charset)) {
		$charset = $_W['charset'];
	}
	if(strtolower($charset) == 'gbk') {
		$charset = 'gbk';
	} else {
		$charset = 'utf8';
	}
	if(istrlen($string, $charset) <= $length) {
		return $string;
	}
	if(function_exists('mb_strcut')) {
		$string = mb_substr($string, 0, $length, $charset);
	} else {
		$pre = '{%';
		$end = '%}';
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

		$strcut = '';
		$strlen = strlen($string);

		if($charset == 'utf8') {
			$n = $tn = $noc = 0;
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$tn = 1; $n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$tn = 2; $n += 2; $noc++;
				} elseif(224 <= $t && $t <= 239) {
					$tn = 3; $n += 3; $noc++;
				} elseif(240 <= $t && $t <= 247) {
					$tn = 4; $n += 4; $noc++;
				} elseif(248 <= $t && $t <= 251) {
					$tn = 5; $n += 5; $noc++;
				} elseif($t == 252 || $t == 253) {
					$tn = 6; $n += 6; $noc++;
				} else {
					$n++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		} else {
			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t > 127) {
					$tn = 2; $n += 2; $noc++;
				} else {
					$tn = 1; $n++; $noc++;
				}
				if($noc >= $length) {
					break;
				}
			}
			if($noc > $length) {
				$n -= $tn;
			}
			$strcut = substr($string, 0, $n);
		}
		$string = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
	}

	if($havedot) {
		$string = $string . "...";
	}

	return $string;
}


function istrlen($string, $charset='') {
	global $_W;
	if(empty($charset)) {
		$charset = $_W['charset'];
	}
	if(strtolower($charset) == 'gbk') {
		$charset = 'gbk';
	} else {
		$charset = 'utf8';
	}
	if(function_exists('mb_strlen')) {
		return mb_strlen($string, $charset);
	} else {
		$n = $noc = 0;
		$strlen = strlen($string);

		if($charset == 'utf8') {

			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
					$n++; $noc++;
				} elseif(194 <= $t && $t <= 223) {
					$n += 2; $noc++;
				} elseif(224 <= $t && $t <= 239) {
					$n += 3; $noc++;
				} elseif(240 <= $t && $t <= 247) {
					$n += 4; $noc++;
				} elseif(248 <= $t && $t <= 251) {
					$n += 5; $noc++;
				} elseif($t == 252 || $t == 253) {
					$n += 6; $noc++;
				} else {
					$n++;
				}
			}

		} else {

			while($n < $strlen) {
				$t = ord($string[$n]);
				if($t>127) {
					$n += 2; $noc++;
				} else {
					$n++; $noc++;
				}
			}

		}

		return $noc;
	}
}


function emotion($message = '', $size = '24px') {
	$emotions = array(
		"/::)","/::~","/::B","/::|","/:8-)","/::<","/::$","/::X","/::Z","/::'(",
		"/::-|","/::@","/::P","/::D","/::O","/::(","/::+","/:--b","/::Q","/::T",
		"/:,@P","/:,@-D","/::d","/:,@o","/::g","/:|-)","/::!","/::L","/::>","/::,@",
		"/:,@f","/::-S","/:?","/:,@x","/:,@@","/::8","/:,@!","/:!!!","/:xx","/:bye",
		"/:wipe","/:dig","/:handclap","/:&-(","/:B-)","/:<@","/:@>","/::-O","/:>-|",
		"/:P-(","/::'|","/:X-)","/::*","/:@x","/:8*","/:pd","/:<W>","/:beer","/:basketb",
		"/:oo","/:coffee","/:eat","/:pig","/:rose","/:fade","/:showlove","/:heart",
		"/:break","/:cake","/:li","/:bome","/:kn","/:footb","/:ladybug","/:shit","/:moon",
		"/:sun","/:gift","/:hug","/:strong","/:weak","/:share","/:v","/:@)","/:jj","/:@@",
		"/:bad","/:lvu","/:no","/:ok","/:love","/:<L>","/:jump","/:shake","/:<O>","/:circle",
		"/:kotow","/:turn","/:skip","/:oY","/:#-0","/:hiphot","/:kiss","/:<&","/:&>"
	);
	foreach ($emotions as $index => $emotion) {
		$message = str_replace($emotion, '<img style="width:'.$size.';vertical-align:middle;" src="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/'.$index.'.gif" />', $message);
	}
	return $message;
}


function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key != '' ? $key : $GLOBALS['_W']['config']['setting']['authkey']);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}


function sizecount($size) {
	if($size >= 1073741824) {
		$size = round($size / 1073741824 * 100) / 100 . ' GB';
	} elseif($size >= 1048576) {
		$size = round($size / 1048576 * 100) / 100 . ' MB';
	} elseif($size >= 1024) {
		$size = round($size / 1024 * 100) / 100 . ' KB';
	} else {
		$size = $size . ' Bytes';
	}
	return $size;
}


function array2xml($arr, $level = 1) {
	$s = $level == 1 ? "<xml>" : '';
	foreach($arr as $tagname => $value) {
		if (is_numeric($tagname)) {
			$tagname = $value['TagName'];
			unset($value['TagName']);
		}
		if(!is_array($value)) {
			$s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
		} else {
			$s .= "<{$tagname}>" . array2xml($value, $level + 1)."</{$tagname}>";
		}
	}
	$s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
	return $level == 1 ? $s."</xml>" : $s;
}

function scriptname() {
	global $_W;
	$_W['script_name'] = basename($_SERVER['SCRIPT_FILENAME']);
	if(basename($_SERVER['SCRIPT_NAME']) === $_W['script_name']) {
		$_W['script_name'] = $_SERVER['SCRIPT_NAME'];
	} else {
		if(basename($_SERVER['PHP_SELF']) === $_W['script_name']) {
			$_W['script_name'] = $_SERVER['PHP_SELF'];
		} else {
			if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $_W['script_name']) {
				$_W['script_name'] = $_SERVER['ORIG_SCRIPT_NAME'];
			} else {
				if(($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
					$_W['script_name'] = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $_W['script_name'];
				} else {
					if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
						$_W['script_name'] = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
					} else {
						$_W['script_name'] = 'unknown';
					}
				}
			}
		}
	}
	return $_W['script_name'];
}


function utf8_bytes($cp) {
	if ($cp > 0x10000){
				return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
		chr(0x80 | (($cp & 0x3F000) >> 12)).
		chr(0x80 | (($cp & 0xFC0) >> 6)).
		chr(0x80 | ($cp & 0x3F));
	}else if ($cp > 0x800){
				return	chr(0xE0 | (($cp & 0xF000) >> 12)).
		chr(0x80 | (($cp & 0xFC0) >> 6)).
		chr(0x80 | ($cp & 0x3F));
	}else if ($cp > 0x80){
				return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
		chr(0x80 | ($cp & 0x3F));
	}else{
				return chr($cp);
	}
}

function media2local($media_id){
	global $_W;
	if(empty($media_id)) {
		return '';
	}
	$data = pdo_fetchcolumn('SELECT attachment FROM ' . tablename('core_wechats_attachment') . ' WHERE uniacid = :uniacid AND media_id = :id', array(':uniacid' => $_W['uniacid'], ':id' => $media_id));
	if(!empty($data)) {
		return tomedia($data);
	}
	return '';
}

/**
 * 
 */
function https_post($url, $data=null){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	
	if(!empty($data)){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$output = curl_exec($ch);
	curl_close($ch);

	return $output;
}

/** 彭镇炜版
 * 查询某个人的下级会员
 * $pid 该会员的uid
 * $level 第几级会员
 */
function getProxys1($pid,$level=1,$nickname=''){
	global $page;
	global $pageSize;
	global $offset;

	$proxys[1]['list'] = array();
	$proxys[1]['list'] = getProxysBySql($pid);
	$proxys[1]['total'] =count($proxys[1]['list']);
	if($level > 1){
		$proxys[2]['list'] = array();
		$proxys[2]['total'] = 0;
		foreach($proxys[1]['list'] as $k => $v){
			$temp = array();
			$temp = getProxys1($v['uid']);
			$proxys[2]['total'] +=count($temp[1]['list']);
			$proxys[2]['list'] = array_merge($proxys[2]['list'], $temp[1]['list']);
			if($level > 2){
				$proxys[3]['list'] = array();
				$proxys[3]['total'] = 0;
				foreach ($proxys[2]['list'] as $key => $value) {
					$temp = getProxys1($value['uid']);
					$proxys[3]['total'] += $temp[1]['total'];
					$proxys[3]['list'] = array_merge($proxys[3]['list'], $temp[1]['list']);

				}
			}

		}
	}
	return $proxys;
}
/**
 * 查找我的下级的下级情况
 */
function getProxysAddProxys($proxys = array()){
	for ($i=1; $i <= count($proxys); $i++) { 
		foreach($proxys[$i]['list']  as $key =>$value){
			$proxys[$i]['list'][$key]['proxys'] = array();
			$proxys[$i]['list'][$key]['proxys'] = getProxys1($value['uid'],3);
		}
	}

	return $proxys;
}
function getProxysBySql($pid){
	$sql = 'SELECT uid,nickname,createtime,status,avatar FROM ' . tablename('mc_members') . " WHERE `pid` = :pid ";
	return pdo_fetchAll($sql, array(':pid' => $pid));
}

/**
 * 获取上级
 */
function getPid($uid,$level=1){
	$sql = 'select pid from '.tablename('mc_members').' where uid = :uid limit 1';
	$temp = pdo_fetch($sql, array(':uid'=>$uid));
	$res[1] = ($temp['pid'] != -1) ? $temp['pid'] : 0;
	if($res[1] > 0 && $level > 1){
		$temp = getPid($res[1], 1);
		$res[2] = $temp[1];
		if($res[2] > 0 && $level > 2){
			$temp = getPid($res[2], 1);
			$res[3] = $temp[1];
		}
	}
	return $res;
}

/**
 * 编译为微信json
 *
 */
function json_encodes($arr) {
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
                $parts [] = json_encodes ( $value ); /* :RECURSION: */
            else
                $parts [] = '"' . $key . '":' . json_encodes ( $value ); /* :RECURSION: */
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

/**
 * 设置信息 
 */
function setOpenWxInfo($uid, $type = false){
	global $_W;
	$user = pdo_fetch('SELECT a.openid, a.fanid, a.updatetime, b.nickname, b.gender, b.residecity, b.resideprovince, b.nationality, b.avatar 
		FROM ' . tablename('mc_mapping_fans') . ' a, '. tablename('mc_members'). ' b WHERE a.`uid`=:uid and a.uid = b.uid', array(':uid'=>$uid));
	// if(!empty($user) && $user['updatetime']+60*60*24*2 < time()){
	if(!empty($user)){
  		$openid = $user['openid'];
  		$fanid = $user['fanid'];
		$acid = $_W['account']['uniacid'];
		$account = WeAccount::create($acid);
		$fan = $account->fansQueryInfo($openid, true);
		if(!is_error($fan)) {
			$group = $account->fetchFansGroupid($openid);
			$record = array();
			if(!is_error($group)) {
				$record['groupid'] = $group['groupid'];
			}
			$record['updatetime'] = TIMESTAMP;
			$record['followtime'] = $fan['subscribe_time'];
			$fan['nickname'] = stripcslashes($fan['nickname']);
			$record['nickname'] = stripslashes($fan['nickname']);
			$record['tag'] = iserializer($fan);
			$record['tag'] = base64_encode($record['tag']);
			pdo_update('mc_mapping_fans', $record, array('fanid' => $fanid));
			
			if(!empty($uid)) {
				$rec = array();
				if(!empty($fan['nickname'])) {
					$rec['nickname'] = stripslashes($fan['nickname']);
				}
				if(empty($user['gender']) && !empty($fan['sex'])) {
					$rec['gender'] = $fan['sex'];
				}
				if(empty($user['residecity']) && !empty($fan['city'])) {
					$rec['residecity'] = $fan['city'] . '市';
				}
				if(empty($user['resideprovince']) && !empty($fan['province'])) {
					$rec['resideprovince'] = $fan['province'] . '省';
				}
				if(empty($user['nationality']) && !empty($fan['country'])) {
					$rec['nationality'] = $fan['country'];
				}
				if(!empty($fan['headimgurl'])) {
					$rec['avatar'] = rtrim($fan['headimgurl'], '0') . 132;
				}
				if(!empty($rec)) {

					pdo_update('mc_members', $rec, array('uid' => $uid));
				}
			}
		}
		return $fan;	
	}
}

/**
 * 用户图片 filepath
 * mediaid
 * createtime
 * uid
 * keyword = '二维码'
 */
function uploadApiImages($srcfile='',$token=''){
	global $_W;
	$media = array(
		'type' => 'images',
		'media' => $srcfile
	);
	if(empty($token)){
		$account = WeAccount::create($_W['account']['uniacid']);
		$token = $account->fetch_token();
	}
	$sendapi = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$token}&type={$media['type']}";
	$data = array(
		'media' => '@'.$media['media']
	);

	load()->func('communication');
	$response = ihttp_request($sendapi, $data);
	return json_decode($response['content'], true);

}

/**
 * 二维码合成
 */
function setGdImg($nickname='',$qrcodeurl,$app='',$uid='',$type='1'){
	global $_W;
	if(empty($uid)){
		$uid = $_W['member']['uid'];	
	}
	
	$root = $_W['siteroot'].'app/';
	//获取背景图
	if($type == 1){
		$raw = $root.'resource/images/user/view.jpg';
	}elseif(in_array($type, array(2,3,4,5,6))){
		$raw = $root.'resource/images/user/view'.$type.'.jpg';
	}else{
		$raw = $root.'resource/images/user/view.jpg';
	}
	$path = dirname($raw);
	$info = getimagesize($raw);

	if($info){
		$imgtype = image_type_to_extension($info[2], false);
		$fun = 'imagecreatefrom'.$imgtype;
		$image = $fun($raw);
		
		$content = $nickname;
		// $content = '我是测试';
		if($type == 1){
			$col = imagecolorallocatealpha($image, 255, 255, 255, 10);
		}else if($type == 2){
			$col = imagecolorallocatealpha($image, 0, 0, 0, 10);	
		}
		
		
		//文字位置
		if($type == 1){
			$font = $app.'resource/fonts/SIMHEI.TTF';	
			imagettftext($image, 13, 0, 302-(strlen($content)-7)*(45/12)-(strlen($content)-11), 670, $col, $font, $content);
		}elseif($type == 2){
			$font = $app.'resource/fonts/dongqing.otf';	
			imagettftext($image, 13, 0, 308-(strlen($content)-7)*(45/12)-(strlen($content)-11), 260, $col, $font, $content);
		}
		/**
		 * 二维码水印
		 * 258
		 */
		$img = $qrcodeurl;
		$info2 = getimagesize($img);
		if(empty($info2)){
			$account_token = account_fetch($_W['account']['uniacid']);
			$data = http_build_query(array('access_token'=>$account_token['access_token']['token']));
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?'.$data;
			$qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$uid.'}}}';
			$post = https_post($url,$qrcode);
			$post = json_decode($post,true);
			$qrcodeurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($post['ticket']);
			$img = $qrcodeurl;
			$info2 = getimagesize($img);
		}
		$imgtype2 = image_type_to_extension($info2[2], false);
		$fun2 = 'imagecreatefrom'.$imgtype2;
		$image2 = $fun2($img);
		
		/**
		 * 压缩大小
		 */
		if($type == 1){
			$thumbArr = array(210,210);
		}elseif($type == 2){
			$thumbArr = array(206,206);
		}
		$image_thumb = imagecreatetruecolor($thumbArr[0], $thumbArr[1]);
		imagecopyresampled($image_thumb, $image2, 0, 0, 0, 0, $thumbArr[0], $thumbArr[1], $info2[0], $info2[1]);
		
		$file = $app.'resource/images/user/';
		$avatar = $file.'avatars/user_'.$uid.'.jpg';

		$infoava = getimagesize($avatar);
		$imgtype2ava = image_type_to_extension($infoava[2],false);
		$fun2ava = 'imagecreatefrom'.$imgtype2ava;
		$image2ava = $fun2ava($avatar);

		if($type == 1){
			$thumbArr2 = array(138,138);
		}elseif($type == 2){
			$thumbArr2 = array(158,160);
		}
		$image2_thumb = imagecreatetruecolor($thumbArr2[0], $thumbArr2[1]);

		imagecopyresampled($image2_thumb, $image2ava, 0, 0, 0, 0, $thumbArr2[0], $thumbArr2[1], $infoava[0], $infoava[1]);

		if($type == 1){
			imagecopymerge($image, $image2_thumb, 265, 530, 8, 10, 120, 120, 100);
			imagecopymerge($image, $image_thumb, 226, 712, 12, 12, 188, 188, 100);
		}elseif($type == 2){
			imagecopymerge($image, $image2_thumb, 252, 580, 8, 10, 145, 145, 100);
			imagecopymerge($image, $image_thumb, 230, 680, 11.5, 11.5, 186, 186, 100);	
		}
		$func = 'image'.$imgtype;
		$file1 = $app.'resource/images/user/user_'.$uid.$type.'.'.$imgtype;

		// header('Content-type:'.$info['mime']);
		// $func($image);

		if($imgtype == 'png'){
			imagejpeg($image, $file1, 100);
		}else{
			$func($image, $file1, 100);
		}
		
		imagedestroy($image2ava);
		imagedestroy($image2);
		imagedestroy($image);
		
		unset($image2_thumb);
		unset($thumbArr2);
		return $file1;
	}
	return false;
}

function get_lt_rounder_corner($radius) {  
    $img     = imagecreatetruecolor($radius, $radius);  // 创建一个正方形的图像  
    $bgcolor    = imagecolorallocate($img, 223, 0, 0);   // 图像的背景  
    $fgcolor    = imagecolorallocate($img, 0, 0, 0);  
    imagefill($img, 0, 0, $bgcolor);  
    // $radius,$radius：以图像的右下角开始画弧  
    // $radius*2, $radius*2：已宽度、高度画弧  
    // 180, 270：指定了角度的起始和结束点  
    // fgcolor：指定颜色  
    imagefilledarc($img, $radius, $radius, $radius*2, $radius*2, 180, 270, $fgcolor, IMG_ARC_PIE);  
    // 将弧角图片的颜色设置为透明  
    imagecolortransparent($img, $fgcolor);  
    // 变换角度  
    // $img = imagerotate($img, 90, 0);  
    // $img = imagerotate($img, 180, 0);  
    // $img = imagerotate($img, 270, 0);  
    // header('Content-Type: image/png');  
    // imagepng($img);  
    return $img;  
} 

function reply_expand($uid='',$type=1){
	global $_W;

	if(empty($uid)){
		$uid = $_W['member']['uid'];
	}
	
	$account_token = account_fetch($_W['account']['uniacid']);
	$data = http_build_query(array('access_token'=>$account_token['access_token']['token']));
	$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?'.$data;
	$qrcode = '{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": '.$uid.'}}}';
	$post = https_post($url,$qrcode);
	$post = json_decode($post,true);
	if(empty($post['ticket'])){
		return false;
	}
	$qrcodeurl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($post['ticket']);

	$pars = array();
	$sql = 'SELECT * FROM ' . tablename('images_reply_expand') . ' WHERE  `uid`=:uid and title = :title and ftype = :type limit 1';// and createtime+60*60*24*1 >  unix_timestamp(now())

	$pars[':uid'] =$uid;
	$pars[':title'] = '二维码';
	$pars[':type'] = $type;
	$reply = pdo_fetch($sql, $pars);
	$returnfile = '';
	
	if(!empty($reply)){
		if($reply['createtime']+60*60*24*1 > time()){
			if(!empty($reply['file'])){
				return  $reply['file'];
			}
		}
	}
	if(empty($reply)){
		pdo_delete('images_reply_expand', array('uid' => $uid,'ftype'=>$type));
	}

	$profile=pdo_fetch('select a.nickname, a.avatar,a.gender, b.updatetime from '.tablename('mc_members').' a, '.tablename('mc_mapping_fans').' b where a.uid = b.uid and a.uid = :uid', array(':uid'=>$uid));
	if(empty($profile)){ return false; }

	$content = $profile['avatar'];
	$nickname = $profile['nickname'];

	$file = $basedir.'resource/images/user/avatars';
	$userheader = $file.'/user_'.$uid.'.jpg';		//头像地址

	$choose = true;
	if(TIMESTAMP > $profile['updatetime']+60*60*24*15 || !file_exists($userheader)){
		file_put_contents($userheader, file_get_contents($content));
	}

	if(filesize($userheader) < 100){
		//自定义头像
		if($profile['gender'] == 1){
			$content = $file.'/avatar_3.jpg';
		}else{
			$content = $file.'/avatar_7.jpg';
		}
		file_put_contents($userheader, file_get_contents($content));
	}

	$path = setGdImg($nickname,$qrcodeurl,'',$uid,$type);//$nickname='',$qrcodeurl,$app='',$uid='',$type='1'
	if($path){
		$ret = uploadApiImages($path, $account_token['access_token']['token']);
		
		if(isset($ret['errcode'])){
			$ret = uploadApiImages($path, $account_token['access_token']['token']);
			if(isset($ret['errcode'])){
				$choose = false;
			}
		}
		if($choose){
			pdo_insert('images_reply_expand',
				array('title'=>"二维码",
					'uid'=>$uid,
					'mediaid'=>$ret['media_id'],
					'createtime'=>$ret['created_at'],
					'file'=>$path,
					'ftype'=>$type));	
		}
	}
	$returnfile = $path;
	
	return  $returnfile;	
}

#====================================================================================
/**
 * 创建个人二维码
 * @param $userres 用户信息 {uid, nickname}
 * @param $keyres {filepath:背景图, keyword:'图片标识', type:'类型', byname:'图片命名'}
 * @param $createtype|configure {action_name:QR_LIMIT_SCENE, keyword:'图片标识', type:'类型'}
 */
function createqrcode($userres, $keyres, $configure = array('action_name'=>'QR_LIMIT_SCENE')){
	global $_W;
	$uid 		= $userres['uid'] ? $userres['uid'] : $_W['member']['uid'];
	$nickname 	= $userres['nickname'] ? $userres['nickname'] : '';
	$qrcodeurl = $userres['qrcodeurl'] ? $userres['qrcodeurl'] : '';

	$keyrule = array('二维码');
	$keytitle	= !empty($keyres['title']) ? $keyres['title'] : '二维码';
	$type 		= $keyres['type'] ? $keyres['type'] : 1;
	$raw 		= $keyres['pathfile'] ? $keyres['pathfile'] : '';

	if(empty($uid))
		return false;

	$app = $_W['siteroot'].'app/';
	if(!$raw){
		$raw = $app.'resource/images/user/view.jpg';	# 获取背景图
	}else{
		$raw = tomedia($raw);	# 其他图片
	}
	$content = $nickname;

	$path = dirname($raw);
	$info = getimagesize($raw);
	if($info){
		# =============================================================================
		# 背景图模板渲染 ==============================================================
		$imgtype = image_type_to_extension($info[2], false);
		$fun = 'imagecreatefrom'.$imgtype;
		$image = $fun($raw);
		# 背景图模板渲染 ==============================================================
		# =============================================================================


		if(in_array($keytitle, $keyrule)){
			// file_put_contents('keytitle1.txt', date('Y-m-d H:i:s')."\n\t".$keytitle."\n\n",FILE_APPEND);
			# =============================================================================
			# 文字位置 ====================================================================
			if($type == 1){
				$col = imagecolorallocatealpha($image, 255, 255, 255, 10);
			}else if($type == 2){
				$col = imagecolorallocatealpha($image, 0, 0, 0, 10);	
			}
			if($type == 1){
				$font = $app.'resource/fonts/SIMHEI.TTF';	
				imagettftext($image, 13, 0, 302-(strlen($content)-7)*(45/12)-(strlen($content)-11), 670, $col, $font, $content);
			}elseif($type == 2){
				$font = $app.'resource/fonts/dongqing.otf';	
				imagettftext($image, 13, 0, 308-(strlen($content)-7)*(45/12)-(strlen($content)-11), 260, $col, $font, $content);
			}
			# 文字位置 ====================================================================
			# =============================================================================
		}

		# =============================================================================
		# 二维码水印(258) =============================================================
		// $configure['qrcodeurl'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQH17zoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL1VFejdVUFRtMXZhdm5nLWt5R0xmAAIEKrcXVgMEPAAAAA%3D%3D';
		$infoQrcodeurl = getimagesize($configure['qrcodeurl']);
		if(empty($infoQrcodeurl)){						
			# $account_token = account_fetch($_W['account']['uniacid']);
			# $data = http_build_query(array('access_token'=>$account_token['access_token']['token']));

			if(!isset($configure['access_token'])){
				$configure['access_token'] = WeAccount::token();
			}
			$data = http_build_query(array('access_token' => $configure['access_token']));
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?'.$data;
			$qrcode = '{"action_name": "'.$configure['action_name'].'", "action_info": {"scene": {"scene_id": '.$uid.'}}}';
			$post = https_post($url,$qrcode);
			
			$post = json_decode($post, true);
			$configure['qrcodeurl'] = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($post['ticket']);
			$infoQrcodeurl = getimagesize($configure['qrcodeurl']);
		}
		$imgtypeQrcodeurl = image_type_to_extension($infoQrcodeurl[2], false);
		$funQrcodeurl = 'imagecreatefrom'.$imgtypeQrcodeurl;

		
		$imageQrcodeurl = $funQrcodeurl($configure['qrcodeurl']);
		
		if(in_array($keytitle, $keyrule)){
			# 压缩大小参数
			if($type == 1){
				$thumbArr = array(210,210);
			}elseif($type == 2){
				$thumbArr = array(206,206);
			}
		}else{
			# $thumbArr = array(145,145);
			$thumbArr = array(160,160);
		}
		$image_thumb = imagecreatetruecolor($thumbArr[0], $thumbArr[1]);
		imagecopyresampled($image_thumb, $imageQrcodeurl, 0, 0, 0, 0, $thumbArr[0], $thumbArr[1], $infoQrcodeurl[0], $infoQrcodeurl[1]);

		# 二维码水印(258) =============================================================
		# =============================================================================


		if(in_array($keytitle, $keyrule)){
			# =============================================================================
			# 用户头像 ====================================================================
			$file = $app.'resource/images/user/';
			$avatar = $file.'avatars/user_'.$uid.'.jpg';
			$infoava = getimagesize($avatar);

			if(!$infoava){
				getHeadimgurl($uid);  #设置头像
				$infoava = getimagesize($avatar);
			}
			
			$imgtype2ava = image_type_to_extension($infoava[2],false);
			$fun2ava = 'imagecreatefrom'.$imgtype2ava;
			$image2ava = $fun2ava($avatar);
			if($type == 1){
				$thumbArr2 = array(138,138);
			}elseif($type == 2){
				$thumbArr2 = array(158,160);
			}
			
			$image2_thumb = imagecreatetruecolor($thumbArr2[0], $thumbArr2[1]);

			imagecopyresampled($image2_thumb, $image2ava, 0, 0, 0, 0, $thumbArr2[0], $thumbArr2[1], $infoava[0], $infoava[1]);
			# 用户头像 ====================================================================
			# =============================================================================
			if($type == 1){
				imagecopymerge($image, $image2_thumb, 265, 530, 8, 10, 120, 120, 100);
				imagecopymerge($image, $image_thumb, 226, 712, 12, 12, 188, 188, 100);
			}elseif($type == 2){
				imagecopymerge($image, $image2_thumb, 252, 580, 8, 10, 145, 145, 100);
				imagecopymerge($image, $image_thumb, 230, 680, 11.5, 11.5, 186, 186, 100);	
			}

		}else{
			imagecopymerge($image, $image_thumb, 448, 880, 2, 2, 155, 155, 100);
		}

		$func = 'image'.$imgtype;
		$userfile = 'app/resource/images/user/'.$keyres['byname'].'user_'.$uid.$type.'.'.$imgtype;

		// header('Content-type:'.$info['mime']);
		// $func($image);
		
		if($imgtype == 'png'){
			imagejpeg($image, $userfile, 100);
		}else{
			$func($image, $userfile, 100);
		}
		imagedestroy($image2ava);
		imagedestroy($imageQrcodeurl);
		imagedestroy($image);
		return $userfile;
	}
	return false;
}

/**
 * 获取个人二维码
 * @param $userres 用户信息 {uid, nickname}
 * @param $keyres {filepath:背景图, title:'图片标识', type:'类型'}
 */
function getqrcode($userres, $keyres, $configure){
	global $_W;
	$uid 		= $userres['uid'] ? $userres['uid'] : $_W['member']['uid'];
	$nickname 	= $userres['nickname'] ? $userres['nickname'] : '';

	$keyres['title']	= !empty($keyres['title']) ? $keyres['title'] : '二维码';
	$keyres['type'] = intval($keyres['type']) ? intval($keyres['type']) : 1;

	load()->model('images.reply.expand');
	$reply = Rexpand_getUserQrcode($uid, $keyres['title'], $keyres['type']);

	if($keyres['title'] == '二维码'){
		$keyres['byname'] = '';
		$status = ($reply['createtime']+24*60*60*3 > time()) ? true : false;
	}else{
		$keyres['byname'] = 'ads_';
		$status = (date('d', $reply['createtime']) != date('d')) ? false : true;
	}

	#时间限制

	#时间限制
	if(!empty($reply['mediaid']) && $status){
		return $reply['mediaid'];
	}else{
		Rexpand_delUserQrcode($reply['fid']);
	}

	if(!isset($configure['action_name'])){
		$configure['action_name'] = 'QR_SCENE';
	}

	if(!isset($configure['access_token'])){
		// $configure['access_token'] = WeAccount::token();
		$configure['access_token'] = $_W['account']['access_token']['token'];
	}

	$createRes = createqrcode($userres, $keyres, $configure);
	
	if($createRes !== false){
		$ret = uploadApiImages($createRes, $configure['access_token']);
		if(!isset($ret['errcode'])){
			$qrinfo = array(
					'title' => $keyres['title'],
					'uid' => $uid,
					'mediaid' => $ret['media_id'],
					'createtime' => $ret['created_at'],
					'file' => $createRes,
					'ftype' => $keyres['type']
				);	
			Rexpand_postUserQrcode($qrinfo);
			return $ret['media_id'];
		}
	}
	return false;
}

/**
 * 设置头像
 *
 */
function getHeadimgurl($uid){
	global $_W;
	$Headimgurl = $_W['member']['avatar'];

	$file = $basedir.'resource/images/user/avatars';
	$imgpath = $file.'/user_'.$uid.'.jpg';		//头像地址

	if(!file_exists($imgpath)){
		file_put_contents($imgpath, file_get_contents($Headimgurl));
	}

	if(filesize($imgpath) < 100){
		//自定义头像
		if($_W['member']['gender'] == 1){
			$Headimgurl = $file.'/avatar_3.jpg';
		}else{
			$Headimgurl = $file.'/avatar_7.jpg';
		}
		file_put_contents($imgpath, file_get_contents($Headimgurl));
	}
	return true;
}
#====================================================================================
function GetIpLookup($ip = ''){
    if(empty($ip)){  
        $ip = GetIp();  
    }  
    $res = @file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip);  
    if(empty($res)){ return false; }  
    $res = json_decode($res, true);
    if(isset($res['code']) && $res['code'] == 0){
    	return $res['data']['region'];
    	// return empty($res['data']['city']) ? $res['data']['region'] : $res['data']['city'];
    }else{
    	return false;
    }  
}

/**
 * 校验用户和上下家的关系是否存在
 * @return true (不存在)
 */
function QueryPid($uid = 0, $may = 0){
	global $_W;

	$res = pdo_fetchcolumn('select pid from ims_mc_members where uid = :uid', array(':uid' => $uid));
	# 查看上家是否存在

	if($res)
		return false;

	#用户或者上家为空返回false
	if(empty($uid) || empty($may) || $uid == $may)
		return false;
	$pars = array(':uid'=>$uid,':may'=>$may);

	# 往上查(存在返回false)
	$sql = "SELECT count(fid)
			FROM ims_mc_relation 
			where uid=:uid and (uid1=:may OR uid2=:may OR uid3=:may)";
	$up = pdo_fetchcolumn($sql, $pars);
	if($up)
		return false;
	

	# 往下查(存在返回false)
	$sql = "SELECT count(fid)
			FROM ims_mc_relation 
			where uid=:may and (uid1=:uid OR uid2=:uid OR uid3=:uid)";
	$down = pdo_fetchcolumn($sql, $pars);
	if($down)
		return false;

	return true;
}

/**
 * 推送封装
 * @param $type = 1 :messages = array('touser'=>'','msgtype'=>'text','text'=>array('content'=>''));
 * @param $type = 1 :messages = array('touser'=>'','msgtype'=>'news','news'=>array('articles'=>array(array('title'=>'','description'=>'','picurl'=>'','url'=>''))));
 * @param $type = 1 :messages = array('touser'=>'','msgtype'=>'image','image'=>array('media_id'=>''));
 *
 * @param $type = 2 :messages = array('touser'=>'','template_id'=>'','url'=>'','topcolor'=>'','data'=>array('first'=>array('value'=>'','color'=>''),'keynote1'=>array('value'=>'','color'=>''),'remark'=>array('value'=>'','color'=>'')));
 */
function wechatPush($id, $messages = array(), $type = '1'){
	global $_W;
	if(is_numeric($id)){
		$id = pdo_fetchcolumn('select openid from '.tablename('mc_mapping_fans').' where `uid` = :uid' ,array(':uid'=>$id));
	}
	$messages['touser'] = $id;
	$acid = $_W['account']['uniacid'];
	$account = WeAccount::create($acid);

	if($type == 1){
		//文字推送
		if(isset($messages['text'])){
			$messages['msgtype'] = 'text';
		}
		if(isset($messages['news'])){
			$messages['msgtype'] = 'news';
		}
		if(isset($messages['image'])){
			$messages['msgtype'] = 'image';
		}
		return $account->sendCustomNotice(json_encodes($messages),false);
	}else if($type == 2){
		//图文推送
		// $messages['template_id'] = $account->getTemplateId(array('template_id_short' => ''));
		// return $account->sendTplNotice($messages['touser'], $messages['template_id'], $messages['postdata'], $messages['url'], $messages['topcolor']);
	}
}

/**
 * Ajax return 
 *
 */
function ajaxReturn($messages='', $type='json'){
	
	if($type == 'json'){
		echo json_encode($messages);
	}else{
		if(is_array($messages)){
			echo json_encode($messages);	
		}else{
			echo $messages;
		}
	}
	exit();
}

/**
 * 获取昵称
 *
 */
function getNickname(){
	$strNickname = <<<EOF
	谏书稀,纯洁的骚年！,湮酔,海于咸,谁旳掌纹赎得回谁旳罪゜,水墨泪i,ペ泪落弦音シ,维多利亚港恋人无归期,情爱纷乱复杂丶﹏,面朝阳心亦朝阳,久居深海蓝透人心,午夜里的布娃娃,新眼泪,醉歌离人,凉巷少年与狸猫,凝眸う,恰似一缕微光,住进苹果心脏,海湾城--梁曼玲,我叫黎梓泓,杨杨杨伟欣,梁~欣妍O(∩_∩)O,邵挺*干货分享,A黄晓梅,领航城000杨蕊链,卓越城（何）,张鹏飞,服装批发：冯寸,店小二陈梅艳,黄健文007,A113雷国伟,梁嘉文1985,曾毅2007,黄丽恒~,袁玉仪,杜海燕,梁允仪,李子敏,李秋姜,黄德藩OK,黄萍hp,李禹函888,chen-钟琪琛,陈诗铭vic,叶苇,张聪,陈晓雯,韦海霞,胡志川,戴俊文,邹冠茹,宋玏,梁倩玲,陈永昌,吴俊武,叶立勋,李德华,任静茵,肖绮雯,莫晓君,GoodnIght 淡年华°,孤岛　Re- Cwert,Dream ×又一梦,Demon゛ 空,Empty。心境△,lilac。】错•过,seven° 昔年 ||,流年碎　jonathan,/冷淡丨desolate。,Sadness″　　逝言,___Martin丶迷茫。,放梦。Life◥。,刺心°Promise,醉眼　WhiteIn゜,I 'm okay,. Caitin纯洁,ペForever love.,Uranus°pices、,# i love you,lose≯（失去。）,幽魅 |▍A foreboding,Minemine╰无心无痛、,Afflictionˇ ≡ 苦寡言　　GUAYAN,_____Candy,未待续,流年碎,jonathan,Asphyxia - 窒息,Flustered.,心慌成性,刺心°,Promise,囙魂 Layoomiety,北仑色°latitude ,ะllure˜ 倾城,醉眼　WhiteIn゜,Prostitute 入戏 =,
不即不离, 经年未变,生活是一场無奈的闹剧, 几番轮回,诠释忧伤,一个人的长大,不再让梦枯萎 ,不经沧桑怎成男,涉世深交,我会尽全力 乐观而坚强つ, 凡尘清心,欲握玫瑰必承其痛,为消逝的时光默哀,换了心情,覆水亦难收, 地老天荒,试着放弃丶,一世终苍老,放下所有痛,小成熟I,潇洒的微笑。,那是完全的覺悟丶,淡漠安然,我想稳定,迷茫散盡,比以前懂事了, 烟花沼泽,骄傲不羁//,因为看清 ╔╗,从此，记忆里只有你。, 玉颜粉骨,ぎ小女人的妩媚妖娆,男人必须傲↑,学会、伪装, 尘埃未定,好马不吃回头草#,悄然改变,经历多了自然就成熟了, 流光易断忘年祭陌,过去不重要,好好爱自已,领悟”,如果不忘记留着给谁看,≈　舍心人,好男人不止曾小贤〃,甜甜, 旧事重提,浮生如梦,沦陷的痛, 徒增伤悲,尘缘而已,血色玫瑰,泄气的爱,真的爱你,安之若素,随遇而安,本末倒置, 杳无音信
EOF;
	$nickname = explode(',', $strNickname);
	$ret = $nickname[rand(0,155)];
	unset($strNickname);
	unset($nickname);
	return $ret;
}

function getAutoAvatar(){
	global $_W;
	return $_W['attachurl'].'/images/global/avatar1/'.rand(1,215).'.jpg';
}

function replaceStartFilter($string, $start = 0, $end = 0) {
    $count = mb_strlen($string, 'UTF-8'); //此处传入编码，建议使用utf-8。此处编码要与下面mb_substr()所使用的一致
    if (!$count) {
        return $string;
    }
    if ($end == 0) {
        $end = $count-1;
        if($end == $start){
        	$end = $count;
        }
    }

    $i = 0;
    $returnString = '';
    while ($i < $count) {
        $tmpString = mb_substr($string, $i, 1, 'UTF-8'); // 与mb_strlen编码一致
        if ($start <= $i && $i < $end) {
            $returnString .= '*';
        } else {
            $returnString .= $tmpString;
        }
        $i ++;
    }
    return $returnString;
}

/**
 * 获取离结束的倒计时
 * @param time the_time
 */
function time_tran($the_time) {
		$timediff = $the_time - time();
		$days = intval($timediff / 86400);
		if (strlen($days) <= 1) {
			$days = "0" . $days;
		}
		$remain = $timediff % 86400;
		$hours = intval($remain / 3600);
		;
		if (strlen($hours) <= 1) {
			$hours = "0" . $hours;
		}
		$remain = $remain % 3600;
		$mins = intval($remain / 60);
		if (strlen($mins) <= 1) {
			$mins = "0" . $mins;
		}
		$secs = $remain % 60;
		if (strlen($secs) <= 1) {
			$secs = "0" . $secs;
		}
		$ret = "";
		if ($days > 0) {
			$ret.=$days . " 天 ";
		}
		if ($hours > 0) {
			$ret.=$hours . ":";
		}
		if ($mins > 0) {
			$ret.=$mins . ":";
		}
		$ret.=$secs;
		return array("倒计时 " . $ret, $timediff);
	}

/**
 * 打印
 *
 */
function pre($arr = array(), $title = ''){
	echo '<pre>';
	if(!empty($title)){
		echo $title,':';
	}
	var_dump($arr);
	echo '</pre><br>';
}

/**
 * 售后状态
 * AftermarketStatus
 */
function AftermarketStatus($status = 0, $type = 1, $choose = false){
	if($choose){
		if($type == 1){
			return '换货';
		}elseif ($type == 2){
			return '退货';
		}
	}
	if($status == 0){
		return '审核中';
	}else if($status == 1){
		return '审核通过';
	}else if($status == 2){
		if($type == 1){
			return '商品换货中';
		}elseif ($type == 2){
			return '商品退货中';
		}
	}else if($status == 3){
		if($type == 1){
			return '换货成功';
		}elseif ($type == 2){
			return '退货成功';
		}
	}
}

/**
 * 订单状态
 * 
 */
function OrderType($status = 0, $cancelgoods = 0, $accomplish = 0){
	if($accomplish == 0){
		if($cancelgoods == 0){
			if($status == -2){
				return '已退货';//待定
			}elseif($status == -1){
				return '已取消';//可启用
			}elseif($status == 0){
				return '未付款';
			}else if($status == 1){
				return '待发货';
			}else if($status == 2){
				return '待收货';
			}else if($status == 3 || $status == 4){
				return '交易成功';
			}else{
				return '订单异常';
			}
		}else if($cancelgoods == 1){
			return '售后申请中';
		}else if($cancelgoods == 2){
			return '售后完成';
		}
	}else if($accomplish == 1){
		if($status == -1){
			return '订单取消';
		}
		return '订单完结';
	}
}

/**
 * 订单商品状态
 *
 */
function OrderGoodState($cancelgoods = 0, $status = 0){
	$ret = '';
	if($cancelgoods == 1){
		$ret .= '换货';
	}else if($cancelgoods == 2){
		$ret .= '退货';
	}
	if($ret){
		if($status == 1){
			$ret .= '申请中';
		}else if($status == 2){
			$ret = '申请通过';
		}else if($status == 3){
			$ret .= '处理中';
		}else if($status == 4){
			$ret .= '完成';
		}
	}
	return $ret;
}

/**
 * 设置红包随机数
 * @param activityMoney	总金额
 * @param surplus		余额				
 * @param sendnum       可抢数量
 * @param getnum        已抢
 * @param setMoney      配置金额
 */
function redpacketOperation($activityMoney, $surplus, $sendnum, $getnum, $setMoney){
	if($setMoney == '0'){
		if($sendnum == 0){
			$rand = $activityMoney/100;
			if($rand < 4){
				$rand = $activityMoney/50;
				if($rand < 2){
					$rand = $activityMoney/30;
				}
			}
		}else{
			$rand = $activityMoney/$sendnum;
		}
		$minrand = ceil($rand/10);
		$maxrand = ceil($rand/10*11);
		//随机金额
		$money = rand($minrand, $maxrand);
	}else if(strstr($setMoney,',')){
		$settingMoney = explode(',', $setMoney);
		//配置金额
		$money = $settingMoney[rand(0,count($settingMoney)-1)];
	}else{
		$money = floatval($setMoney);
		//固定金额
	}
	//最后一位幸运还是不幸
	if($surplus < $money){
		$money = $surplus;
	}
	return $money;
}

/**
 * @param surplus		余额				
 * @param sendnum       可抢数量
 * @param getnum        已抢
 * @param setMoney      最小红包金额
 */
function randRedBond($surplus, $sendnum, $getnum, $setMoney=1, $settingMoney = 0){
	$redBoundMoney = 0;
	if($surplus <= 0){
		return $redBoundMoney;
	}
	if($settingMoney == 0){		//随机金额
		// console($setMoney);
		// console($sendnum);
		// console($surplus);
		// if ($setMoney*$sendnum > $surplus) {
		// 	return false;
		// }
		if ($sendnum==1) {
			$redBoundMoney = $surplus;
		}elseif($sendnum != 0 && $sendnum - $getnum == 1){
			$redBoundMoney = $surplus;
		} else {
			// $maxRandBase = $surplus-$setMoney*$sendnum;  //也可乘sendNum-1
			// $minRandBase = $setMoney;
			$maxRandBase = $setMoney*1.4;;
			$minRandBase = $setMoney*0.6;
			$redBoundMoney =rand($minRandBase, $maxRandBase);
		}
	}else if(strstr($settingMoney,',')){
		$settingMoney = explode(',', $settingMoney);
		# 配置金额
		$redBoundMoney = $settingMoney[rand(0,count($settingMoney)-1)];
		if($redBoundMoney>$surplus){
			return $surplus;
		}
	}else{
		$redBoundMoney = floatval($settingMoney);
		# 固定金额
	}
	return $redBoundMoney;
}

/**
 * 生成二维码并打印logo(logo失真，看不清)
 * @param url 		 'http://www.jb51.net/article/52569.htm';
 * @param qrcode 	 '../attachment/cardqrcode/';
 *
 */
function qrJionLogo($url, $qrcode){
	if(empty($url) || empty($qrcode))
		return false;
	
	require_once('../framework/library/qrcode/phpqrcode.php');
	$logo = 'resource/images/static/Forward.png';				//logo 路径地址

	$errorCorrectionLevel = "L";
	$matrixPointSize = "6";
	$res = QRcode::png($url, $qrcode, $errorCorrectionLevel, $matrixPointSize, 2);
	// if($logo !== FALSE){ 
	// 	$QR = imagecreatefromstring(file_get_contents($qrcode));   
	//     $logo = imagecreatefromstring(file_get_contents($logo));   
	//     $QR_width = imagesx($QR);//二维码图片宽度   
	//     $QR_height = imagesy($QR);//二维码图片高度   
	//     $logo_width = imagesx($logo);//logo图片宽度   
	//     $logo_height = imagesy($logo);//logo图片高度   
	//     $logo_qr_width = $QR_width / 5;   
	//     $scale = $logo_width/$logo_qr_width;   
	//     $logo_qr_height = $logo_height/$scale;   
	//     $from_width = ($QR_width - $logo_qr_width) / 2;   
	//     //重新组合图片并调整大小   
	//     imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,   
	//     $logo_qr_height, $logo_width, $logo_height);  
	// }
	# 输出图片   
	// imagepng($QR, $qrcode);   
	return $res;
}

/**
 * 随机数
 * 
 */
function random2(){
	$_No = rand(0,8174).rand(0,9999);
	$year = (date('Y')-2015)*365;
	$_No = $_No+$year*10000;
	$_No_f = substr($_No, 0,4).substr($_No,4,4);
	return $_No_f;
}

/**
 * 多重加密
 * @param str value
 * @param str key
 */
function EncryptMd5($value, $key){
	$keyVal = '';
	if(!empty($key)){
		$keyVal = token($key);
	}
	return substr(md5(md5($value).$keyVal), 6, 26);
}

function console($value){
	echo '<script>console.log("'.json_encode($value).'")</script>';
}


/**
 * 把一个一维数组的value 向上移一位，并删除最后一个key
 * @param array $arr
 */
function array_rebound($arr){
	$arrVals = array_values($arr);
	$arrKeys = array_keys($arr);	
	array_shift($arrKeys);
	array_pop($arrVals);
	if($arrVals[0]){
		$res = array_combine($arrKeys, $arrVals);
		return $res;
	}
	return false;
}

/**
 * 订单随机数
 * @param int $length 长度
 * @param str $prefix 前缀（特殊订单区分）
 * 	      1-订单{$suffix：9-正常订单、1-手动订单、2-自动订单}
 *        2-充值{$suffix：9-正常充值}
 * @param str $suffix 后缀（特殊订单区分）
 * @param date $key   时间（唯一）
 */
function randNumByOrder($length = 4, $prefix = '1', $suffix = '9', $key = 'md'){
    $date = date($key).substr(microtime() , 2 , $length);
    $rand = mt_rand(1, str_pad(9,$length,'9',STR_PAD_LEFT));
    $oid = str_pad($rand, $length, '0', STR_PAD_LEFT);
    return $prefix.$date.$oid.$suffix;    
}

/*
 * 事件日志记录
 * @param $uid int 事件发生主角
 * @param $type int 事件类型
 * @param $data array 事件记录数据
 * @return bool
 * type: 
 *   1-抢红包, 2-充值, 3-积分兑换余额, 4-发红包, 5-签到兑换商品
 *   6-生成订单, 7-退换货, 8-修改上家, 9-优惠券关注领取, 10-优惠券关注激活
*/
function recordEventLog($uid, $type, $data=array()) {
	$recordData = array();
	$recordData['eventUser'] = intval($uid);
	$recordData['eventType'] = intval($type);
	$recordData['eventDt'] = time();
	$recordData['eventData'] = serialize($data);
	return pdo_insert('event_log', $recordData);
}


/*
 * 向消息队列添加推送消息
 * @param $uid int 用户
 * @param $openId int 用户openid
 * @param $type int 消息类型（image,等,text）
 * @param $data array 消息数据
 * @return bool
*/
function addMsgIntoQueen($uid, $openId, $type, $data=array()) {
	require_once('../framework/class/MemcacheClient.class.php');

	if (!memc()->get('msg_key_list')) {
	  memc()->add('msg_key_list', '');
	}

	$data = array();
	$data['uid'] = $uid;
	$data['openid'] = $openId;
	if (intval($type)==2) {
		$data['type'] = 2;
	} else {
		$data['type'] = 1;
	}
	$data['text'] = $data['text'];
	$data['createDt'] = time();
	$addMsgKey = memc()->set($data);
	//加入队列消息失败则插入库
	if ($addMsgKey) {
		return memc()->update('msg_key_list', memc()->get('msg_key_list').','.$addMsgKey);
	} else {
		return pdo_insert('msg_queen', array('uid'=>$uid, 'openid'=>$openid, 'create_dt'=>time(), 'push_type'=>$type, 'push_txt'=>$data['text']));
	}

}

/**
 * [根据数组获取随机数]
 *
 * @param [type] $[name] [<description>]
 * @example [array] [<array(10 => 89, 20 => 2, 30 => 2, 50 => 2, 60 => 2, 80 => 2, 100 => 1)>, 
 * 				<数组key是随机获取的值，value是随机概率>]
 * @version $Id$
 */
function get_rand($arr){
	if(empty($arr)) 
		return false;

    $pro_sum = array_sum($arr);
    $rand_num = mt_rand(1,$pro_sum);
    $tmp_num = 0;
    foreach($arr as $k => $val){
        if($rand_num <= $val + $tmp_num){
            $n = $k;
            break;
        }else{
            $tmp_num += $val;
        }
    }
    return $n;
}