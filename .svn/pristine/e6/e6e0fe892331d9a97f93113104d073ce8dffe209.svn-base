<?php
/*
  +----------------------------------------------------------------------+
  | FROM: http://pecl.php.net/get/memcache-3.0.8.tgz                     |
  +----------------------------------------------------------------------+
  | Author:  Harun Yayli <harunyayli at gmail.com>                       |
  +----------------------------------------------------------------------+
*/

$VERSION='$Id: memcache.php 326707 2012-07-19 19:02:42Z ab $';

define('ADMIN_USERNAME','yunji'); 	// Admin Username
define('ADMIN_PASSWORD','yunjiadmin');  	// Admin Password
define('DATE_FORMAT','Y-m-d H:i:s');
define('GRAPH_SIZE',200);
define('MAX_ITEM_DUMP',50);

$MEMCACHE_SERVERS[] = '10.0.1.150:11211'; // add more as an array


////////// END OF DEFAULT CONFIG AREA /////////////////////////////////////////////////////////////

///////////////// Password protect ////////////////////////////////////////////////////////////////
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
           $_SERVER['PHP_AUTH_USER'] != ADMIN_USERNAME ||$_SERVER['PHP_AUTH_PW'] != ADMIN_PASSWORD) {
			Header("WWW-Authenticate: Basic realm=\"Memcache Login\"");
			Header("HTTP/1.0 401 Unauthorized");

			echo <<<EOB
				<html><body>
				<h1>Rejected!</h1>
				<big>Wrong Username or Password!</big>
				</body></html>
EOB;
			exit;
}

///////////MEMCACHE FUNCTIONS /////////////////////////////////////////////////////////////////////

function get_host_port_from_server($server){
	$values = explode(':', $server);
	if (($values[0] == 'unix') && (!is_numeric( $values[1]))) {
		return array($server, 0);
	}
	else {
		return $values;
	}
}

function sendMemcacheCommands($command){
    global $MEMCACHE_SERVERS;
	$result = array();

	foreach($MEMCACHE_SERVERS as $server){
		$strs = get_host_port_from_server($server);
		$host = $strs[0];
		$port = $strs[1];
		$result[$server] = sendMemcacheCommand($host,$port,$command);
	}
	return $result;
}
function sendMemcacheCommand($server,$port,$command){

	$s = @fsockopen($server,$port);
	if (!$s){
		die("Cant connect to:".$server.':'.$port);
	}

	fwrite($s, $command."\r\n");

	$buf='';
	while ((!feof($s))) {
		$buf .= fgets($s, 256);
		if (strpos($buf,"END\r\n")!==false){ // stat says end
		    break;
		}
		if (strpos($buf,"DELETED\r\n")!==false || strpos($buf,"NOT_FOUND\r\n")!==false){ // delete says these
		    break;
		}
		if (strpos($buf,"OK\r\n")!==false){ // flush_all says ok
		    break;
		}
	}
    fclose($s);
    return parseMemcacheResults($buf);
}
function parseMemcacheResults($str){
    
	$res = array();
	$lines = explode("\r\n",$str);
	$cnt = count($lines);
	for($i=0; $i< $cnt; $i++){
	    $line = $lines[$i];
		$l = explode(' ',$line,3);
		if (count($l)==3){
			$res[$l[0]][$l[1]]=$l[2];
			if ($l[0]=='VALUE'){ // next line is the value
			    $res[$l[0]][$l[1]] = array();
			    list ($flag,$size)=explode(' ',$l[2]);
			    $res[$l[0]][$l[1]]['stat']=array('flag'=>$flag,'size'=>$size);
			    $res[$l[0]][$l[1]]['value']=$lines[++$i];
			}
		}elseif($line=='DELETED' || $line=='NOT_FOUND' || $line=='OK'){
		    return $line;
		}
	}
	return $res;

}

function dumpCacheSlab($server,$slabId,$limit){
    list($host,$port) = get_host_port_from_server($server);
    $resp = sendMemcacheCommand($host,$port,'stats cachedump '.$slabId.' '.$limit);

   return $resp;

}

function flushServer($server){
    list($host,$port) = get_host_port_from_server($server);
    $resp = sendMemcacheCommand($host,$port,'flush_all');
    return $resp;
}
function getCacheItems(){
 $items = sendMemcacheCommands('stats items');
 $serverItems = array();
 $totalItems = array();
 foreach ($items as $server=>$itemlist){
    $serverItems[$server] = array();
    $totalItems[$server]=0;
    if (!isset($itemlist['STAT'])){
        continue;
    }

    $iteminfo = $itemlist['STAT'];

    foreach($iteminfo as $keyinfo=>$value){
        if (preg_match('/items\:(\d+?)\:(.+?)$/',$keyinfo,$matches)){
            $serverItems[$server][$matches[1]][$matches[2]] = $value;
            if ($matches[2]=='number'){
                $totalItems[$server] +=$value;
            }
        }
    }
 }
 return array('items'=>$serverItems,'counts'=>$totalItems);
}
function getMemcacheStats($total=true){
	$resp = sendMemcacheCommands('stats');
	if ($total){
		$res = array();
		foreach($resp as $server=>$r){
			foreach($r['STAT'] as $key=>$row){
				if (!isset($res[$key])){
					$res[$key]=null;
				}
				switch ($key){
					case 'pid':
						$res['pid'][$server]=$row;
						break;
					case 'uptime':
						$res['uptime'][$server]=$row;
						break;
					case 'time':
						$res['time'][$server]=$row;
						break;
					case 'version':
						$res['version'][$server]=$row;
						break;
					case 'pointer_size':
						$res['pointer_size'][$server]=$row;
						break;
					case 'rusage_user':
						$res['rusage_user'][$server]=$row;
						break;
					case 'rusage_system':
						$res['rusage_system'][$server]=$row;
						break;
					case 'curr_items':
						$res['curr_items']+=$row;
						break;
					case 'total_items':
						$res['total_items']+=$row;
						break;
					case 'bytes':
						$res['bytes']+=$row;
						break;
					case 'curr_connections':
						$res['curr_connections']+=$row;
						break;
					case 'total_connections':
						$res['total_connections']+=$row;
						break;
					case 'connection_structures':
						$res['connection_structures']+=$row;
						break;
					case 'cmd_get':
						$res['cmd_get']+=$row;
						break;
					case 'cmd_set':
						$res['cmd_set']+=$row;
						break;
					case 'get_hits':
						$res['get_hits']+=$row;
						break;
					case 'get_misses':
						$res['get_misses']+=$row;
						break;
					case 'evictions':
						$res['evictions']+=$row;
						break;
					case 'bytes_read':
						$res['bytes_read']+=$row;
						break;
					case 'bytes_written':
						$res['bytes_written']+=$row;
						break;
					case 'limit_maxbytes':
						$res['limit_maxbytes']+=$row;
						break;
					case 'threads':
						$res['rusage_system'][$server]=$row;
						break;
				}
			}
		}
		return $res;
	}
	return $resp;
}

//////////////////////////////////////////////////////

//
// don't cache this page
//
header("Cache-Control: no-store, no-cache, must-revalidate");  // HTTP/1.1
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");                                    // HTTP/1.0

function duration($ts) {
    global $time;
    $years = (int)((($time - $ts)/(7*86400))/52.177457);
    $rem = (int)(($time-$ts)-($years * 52.177457 * 7 * 86400));
    $weeks = (int)(($rem)/(7*86400));
    $days = (int)(($rem)/86400) - $weeks*7;
    $hours = (int)(($rem)/3600) - $days*24 - $weeks*7*24;
    $mins = (int)(($rem)/60) - $hours*60 - $days*24*60 - $weeks*7*24*60;
    $str = '';
    if($years==1) $str .= "$years year, ";
    if($years>1) $str .= "$years years, ";
    if($weeks==1) $str .= "$weeks week, ";
    if($weeks>1) $str .= "$weeks weeks, ";
    if($days==1) $str .= "$days day,";
    if($days>1) $str .= "$days days,";
    if($hours == 1) $str .= " $hours hour and";
    if($hours>1) $str .= " $hours hours and";
    if($mins == 1) $str .= " 1 minute";
    else $str .= " $mins minutes";
    return $str;
}

// create graphics
//
function graphics_avail() {
	return extension_loaded('gd');
}

function bsize($s) {
	foreach (array('','K','M','G') as $i => $k) {
		if ($s < 1024) break;
		$s/=1024;
	}
	return sprintf("%5.1f %sBytes",$s,$k);
}

// create menu entry
function menu_entry($ob,$title) {
	global $PHP_SELF;
	if ($ob==$_GET['op']){
	    return "<li><a class=\"child_active\" href=\"$PHP_SELF&op=$ob\">$title</a></li>";
	}
	return "<li><a class=\"active\" href=\"$PHP_SELF&op=$ob\">$title</a></li>";
}

function getHeader(){
    $header = <<<EOB
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head><title>MEMCACHE INFO</title>
<style type="text/css"><!--
body { background:white; font-size:100.01%; margin:0 auto; padding:0; }
body,p,td,th,input,submit { font-size:0.8em;font-family:arial,helvetica,sans-serif; }
* html body   {font-size:0.8em}
* html p      {font-size:0.8em}
* html td     {font-size:0.8em}
* html th     {font-size:0.8em}
* html input  {font-size:0.8em}
* html submit {font-size:0.8em}
td { vertical-align:top }
a { color:black; font-weight:none; text-decoration:none; }
a:hover { text-decoration:underline; }
div.content { padding:1em 1em 1em 1em; position:absolute; width:97%; z-index:100; }

h1.memcache { background:rgb(153,153,204); margin:0; padding:0.5em 1em 0.5em 1em; }
* html h1.memcache { margin-bottom:-7px; }
h1.memcache a:hover { text-decoration:none; color:rgb(90,90,90); }
h1.memcache span.logo {
	background:rgb(119,123,180);
	color:black;
	border-right: solid black 1px;
	border-bottom: solid black 1px;
	font-style:italic;
	font-size:1em;
	padding-left:1.2em;
	padding-right:1.2em;
	text-align:right;
	display:block;
	width:130px;
	}
h1.memcache span.logo span.name { color:white; font-size:0.7em; padding:0 0.8em 0 2em; }
h1.memcache span.nameinfo { color:white; display:inline; font-size:0.4em; margin-left: 3em; }
h1.memcache div.copy { color:black; font-size:0.4em; position:absolute; right:1em; }
hr.memcache {
	background:white;
	border-bottom:solid rgb(102,102,153) 1px;
	border-style:none;
	border-top:solid rgb(102,102,153) 10px;
	height:12px;
	margin:0;
	margin-top:1px;
	padding:0;
}

ol,menu { margin:1em 0 0 0; padding:0.2em; margin-left:1em;}
ol.menu li { display:inline; margin-right:0.7em; list-style:none; font-size:85%}
ol.menu a {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	margin-left: 5px;
	}
ol.menu a.child_active {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	border-left: solid black 5px;
	margin-left: 0px;
	}
ol.menu span.active {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:black;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	border-left: solid black 5px;
	}
ol.menu span.inactive {
	background:rgb(193,193,244);
	border:solid rgb(182,182,233) 2px;
	color:white;
	font-weight:bold;
	margin-right:0em;
	padding:0.1em 0.5em 0.1em 0.5em;
	text-decoration:none;
	margin-left: 5px;
	}
ol.menu a:hover {
	background:rgb(193,193,244);
	text-decoration:none;
	}


div.info {
	background:rgb(204,204,204);
	border:solid rgb(204,204,204) 1px;
	margin-bottom:1em;
	}
div.info h2 {
	background:rgb(204,204,204);
	color:black;
	font-size:1em;
	margin:0;
	padding:0.1em 1em 0.1em 1em;
	}
div.info table {
	border:solid rgb(204,204,204) 1px;
	border-spacing:0;
	width:100%;
	}
div.info table th {
	background:rgb(204,204,204);
	color:white;
	margin:0;
	padding:0.1em 1em 0.1em 1em;
	}
div.info table th a.sortable { color:black; }
div.info table tr.tr-0 { background:rgb(238,238,238); }
div.info table tr.tr-1 { background:rgb(221,221,221); }
div.info table td { padding:0.3em 1em 0.3em 1em; }
div.info table td.td-0 { border-right:solid rgb(102,102,153) 1px; white-space:nowrap; }
div.info table td.td-n { border-right:solid rgb(102,102,153) 1px; }
div.info table td h3 {
	color:black;
	font-size:1.1em;
	margin-left:-0.3em;
	}
.td-0 a , .td-n a, .tr-0 a , tr-1 a {
    text-decoration:underline;
}
div.graph { margin-bottom:1em }
div.graph h2 { background:rgb(204,204,204);; color:black; font-size:1em; margin:0; padding:0.1em 1em 0.1em 1em; }
div.graph table { border:solid rgb(204,204,204) 1px; color:black; font-weight:normal; width:100%; }
div.graph table td.td-0 { background:rgb(238,238,238); }
div.graph table td.td-1 { background:rgb(221,221,221); }
div.graph table td { padding:0.2em 1em 0.4em 1em; }

div.div1,div.div2 { margin-bottom:1em; width:35em; }
div.div3 { position:absolute; left:40em; top:1em; width:580px; }
//div.div3 { position:absolute; left:37em; top:1em; right:1em; }

div.sorting { margin:1.5em 0em 1.5em 2em }
.center { text-align:center }
.aright { position:absolute;right:1em }
.right { text-align:right }
.ok { color:rgb(0,200,0); font-weight:bold}
.failed { color:rgb(200,0,0); font-weight:bold}

span.box {
	border: black solid 1px;
	border-right:solid black 2px;
	border-bottom:solid black 2px;
	padding:0 0.5em 0 0.5em;
	margin-right:1em;
}
span.green { background:#60F060; padding:0 0.5em 0 0.5em}
span.red { background:#D06030; padding:0 0.5em 0 0.5em }

div.authneeded {
	background:rgb(238,238,238);
	border:solid rgb(204,204,204) 1px;
	color:rgb(200,0,0);
	font-size:1.2em;
	font-weight:bold;
	padding:2em;
	text-align:center;
	}

input {
	background:rgb(153,153,204);
	border:solid rgb(102,102,153) 2px;
	color:white;
	font-weight:bold;
	margin-right:1em;
	padding:0.1em 0.5em 0.1em 0.5em;
	}
//-->
</style>
</head>
<body>
<div class="head">
	<h1 class="memcache">
		<span ><a href="http://pecl.php.net/package/memcache"><img src="data:image/png;base64,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"></a></span>
	</h1>
	<hr class="memcache">
</div>
<div class=content>
EOB;

    return $header;
}
function getFooter(){
    global $VERSION;
    $footer = '</div><!-- Based on apc.php '.$VERSION.'--></body>
</html>
';

    return $footer;

}
function getMenu(){
    global $PHP_SELF;
echo "<ol class=menu>";
if ($_GET['op']!=4){
echo <<<EOB
    <li><a href="$PHP_SELF&op={$_GET['op']}">Refresh Data</a></li>
EOB;
}
else {
echo <<<EOB
    <li><a href="$PHP_SELF&op=2}">Back</a></li>
EOB;
}
echo
	menu_entry(1,'View Host Stats'),
	menu_entry(2,'Variables');

echo <<<EOB
	</ol>
	<br/>
EOB;
}

// TODO, AUTH

$_GET['op'] = !isset($_GET['op'])? '1':$_GET['op'];
$PHP_SELF= isset($_SERVER['PHP_SELF']) ? htmlentities(strip_tags($_SERVER['PHP_SELF'],'')) : '';

$PHP_SELF=$PHP_SELF.'?';
$time = time();
// sanitize _GET

foreach($_GET as $key=>$g){
    $_GET[$key]=htmlentities($g);
}


// singleout
// when singleout is set, it only gives details for that server.
if (isset($_GET['singleout']) && $_GET['singleout']>=0 && $_GET['singleout'] <count($MEMCACHE_SERVERS)){
    $MEMCACHE_SERVERS = array($MEMCACHE_SERVERS[$_GET['singleout']]);
}

// display images
if (isset($_GET['IMG'])){
    $memcacheStats = getMemcacheStats();
    $memcacheStatsSingle = getMemcacheStats(false);

    if (!graphics_avail()) {
		exit(0);
	}

	function fill_box($im, $x, $y, $w, $h, $color1, $color2,$text='',$placeindex='') {
		global $col_black;
		$x1=$x+$w-1;
		$y1=$y+$h-1;

		imagerectangle($im, $x, $y1, $x1+1, $y+1, $col_black);
		if($y1>$y) imagefilledrectangle($im, $x, $y, $x1, $y1, $color2);
		else imagefilledrectangle($im, $x, $y1, $x1, $y, $color2);
		imagerectangle($im, $x, $y1, $x1, $y, $color1);
		if ($text) {
			if ($placeindex>0) {

				if ($placeindex<16)
				{
					$px=5;
					$py=$placeindex*12+6;
					imagefilledrectangle($im, $px+90, $py+3, $px+90-4, $py-3, $color2);
					imageline($im,$x,$y+$h/2,$px+90,$py,$color2);
					imagestring($im,2,$px,$py-6,$text,$color1);

				} else {
					if ($placeindex<31) {
						$px=$x+40*2;
						$py=($placeindex-15)*12+6;
					} else {
						$px=$x+40*2+100*intval(($placeindex-15)/15);
						$py=($placeindex%15)*12+6;
					}
					imagefilledrectangle($im, $px, $py+3, $px-4, $py-3, $color2);
					imageline($im,$x+$w,$y+$h/2,$px,$py,$color2);
					imagestring($im,2,$px+2,$py-6,$text,$color1);
				}
			} else {
				imagestring($im,4,$x+5,$y1-16,$text,$color1);
			}
		}
	}


    function fill_arc($im, $centerX, $centerY, $diameter, $start, $end, $color1,$color2,$text='',$placeindex=0) {
		$r=$diameter/2;
		$w=deg2rad((360+$start+($end-$start)/2)%360);


		if (function_exists("imagefilledarc")) {
			// exists only if GD 2.0.1 is avaliable
			imagefilledarc($im, $centerX+1, $centerY+1, $diameter, $diameter, $start, $end, $color1, IMG_ARC_PIE);
			imagefilledarc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color2, IMG_ARC_PIE);
			imagefilledarc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color1, IMG_ARC_NOFILL|IMG_ARC_EDGED);
		} else {
			imagearc($im, $centerX, $centerY, $diameter, $diameter, $start, $end, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($start)) * $r, $centerY + sin(deg2rad($start)) * $r, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($start+1)) * $r, $centerY + sin(deg2rad($start)) * $r, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($end-1))   * $r, $centerY + sin(deg2rad($end))   * $r, $color2);
			imageline($im, $centerX, $centerY, $centerX + cos(deg2rad($end))   * $r, $centerY + sin(deg2rad($end))   * $r, $color2);
			imagefill($im,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2, $color2);
		}
		if ($text) {
			if ($placeindex>0) {
				imageline($im,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2,$diameter, $placeindex*12,$color1);
				imagestring($im,4,$diameter, $placeindex*12,$text,$color1);

			} else {
				imagestring($im,4,$centerX + $r*cos($w)/2, $centerY + $r*sin($w)/2,$text,$color1);
			}
		}
	}
	$size = GRAPH_SIZE; // image size
	$image = imagecreate($size+50, $size+10);

	$col_white = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
	$col_red   = imagecolorallocate($image, 0xD0, 0x60,  0x30);
	$col_green = imagecolorallocate($image, 0x60, 0xF0, 0x60);
	$col_black = imagecolorallocate($image,   0,   0,   0);

	imagecolortransparent($image,$col_white);

    switch ($_GET['IMG']){
        case 1: // pie chart
            $tsize=$memcacheStats['limit_maxbytes'];
    		$avail=$tsize-$memcacheStats['bytes'];
    		$x=$y=$size/2;
    		$angle_from = 0;
    		$fuzz = 0.000001;

            foreach($memcacheStatsSingle as $serv=>$mcs) {
    			$free = $mcs['STAT']['limit_maxbytes']-$mcs['STAT']['bytes'];
    			$used = $mcs['STAT']['bytes'];


                if ($free>0){
    			// draw free
    			    $angle_to = ($free*360)/$tsize;
                    $perc =sprintf("%.2f%%", ($free *100) / $tsize) ;

        			fill_arc($image,$x,$y,$size,$angle_from,$angle_from + $angle_to ,$col_black,$col_green,$perc);
        			$angle_from = $angle_from + $angle_to ;
                }
    			if ($used>0){
    			// draw used
        			$angle_to = ($used*360)/$tsize;
        			$perc =sprintf("%.2f%%", ($used *100) / $tsize) ;
        			fill_arc($image,$x,$y,$size,$angle_from,$angle_from + $angle_to ,$col_black,$col_red, '('.$perc.')' );
                    $angle_from = $angle_from+ $angle_to ;
    			}
    			}

        break;

        case 2: // hit miss

            $hits = ($memcacheStats['get_hits']==0) ? 1:$memcacheStats['get_hits'];
            $misses = ($memcacheStats['get_misses']==0) ? 1:$memcacheStats['get_misses'];
            $total = $hits + $misses ;

	       	fill_box($image, 30,$size,50,-$hits*($size-21)/$total,$col_black,$col_green,sprintf("%.1f%%",$hits*100/$total));
		    fill_box($image,130,$size,50,-max(4,($total-$hits)*($size-21)/$total),$col_black,$col_red,sprintf("%.1f%%",$misses*100/$total));
		break;
		
    }
    header("Content-type: image/png");
	imagepng($image);
	exit;
}

echo getHeader();
echo getMenu();

switch ($_GET['op']) {

    case 1: // host stats
    	$phpversion = phpversion();
        $memcacheStats = getMemcacheStats();
        $memcacheStatsSingle = getMemcacheStats(false);

        $mem_size = $memcacheStats['limit_maxbytes'];
    	$mem_used = $memcacheStats['bytes'];
	    $mem_avail= $mem_size-$mem_used;
	    $startTime = time()-array_sum($memcacheStats['uptime']);

        $curr_items = $memcacheStats['curr_items'];
        $total_items = $memcacheStats['total_items'];
        $hits = ($memcacheStats['get_hits']==0) ? 1:$memcacheStats['get_hits'];
        $misses = ($memcacheStats['get_misses']==0) ? 1:$memcacheStats['get_misses'];
        $sets = $memcacheStats['cmd_set'];

       	$req_rate = sprintf("%.2f",($hits+$misses)/($time-$startTime));
	    $hit_rate = sprintf("%.2f",($hits)/($time-$startTime));
	    $miss_rate = sprintf("%.2f",($misses)/($time-$startTime));
	    $set_rate = sprintf("%.2f",($sets)/($time-$startTime));

	    echo <<< EOB
		<div class="info div1"><h2>General Cache Information</h2>
		<table cellspacing=0><tbody>
		<tr class=tr-1><td class=td-0>PHP Version</td><td>$phpversion</td></tr>
EOB;
		echo "<tr class=tr-0><td class=td-0>Memcached Host". ((count($MEMCACHE_SERVERS)>1) ? 's':'')."</td><td>";
		$i=0;
		if (!isset($_GET['singleout']) && count($MEMCACHE_SERVERS)>1){
    		foreach($MEMCACHE_SERVERS as $server){
    		      echo ($i+1).'. <a href="'.$PHP_SELF.'&singleout='.$i++.'">'.$server.'</a><br/>';
    		}
		}
		else{
		    echo '1.'.$MEMCACHE_SERVERS[0];
		}
		if (isset($_GET['singleout'])){
		      echo '<a href="'.$PHP_SELF.'">(all servers)</a><br/>';
		}
		echo "</td></tr>\n";
		echo "<tr class=tr-1><td class=td-0>Total Memcache Cache</td><td>".bsize($memcacheStats['limit_maxbytes'])."</td></tr>\n";

	echo <<<EOB
		</tbody></table>
		</div>

		<div class="info div1"><h2>Memcache Server Information</h2>
EOB;
        foreach($MEMCACHE_SERVERS as $server){
            echo '<table cellspacing=0><tbody>';
            echo '<tr class=tr-1><td class=td-1>'.$server.'</td><td><a href="'.$PHP_SELF.'&server='.array_search($server,$MEMCACHE_SERVERS).'&op=6">[<b>Flush this server</b>]</a></td></tr>';
    		echo '<tr class=tr-0><td class=td-0>Start Time</td><td>',date(DATE_FORMAT,$memcacheStatsSingle[$server]['STAT']['time']-$memcacheStatsSingle[$server]['STAT']['uptime']),'</td></tr>';
    		echo '<tr class=tr-1><td class=td-0>Uptime</td><td>',duration($memcacheStatsSingle[$server]['STAT']['time']-$memcacheStatsSingle[$server]['STAT']['uptime']),'</td></tr>';
    		echo '<tr class=tr-0><td class=td-0>Memcached Server Version</td><td>'.$memcacheStatsSingle[$server]['STAT']['version'].'</td></tr>';
    		echo '<tr class=tr-1><td class=td-0>Used Cache Size</td><td>',bsize($memcacheStatsSingle[$server]['STAT']['bytes']),'</td></tr>';
    		echo '<tr class=tr-0><td class=td-0>Total Cache Size</td><td>',bsize($memcacheStatsSingle[$server]['STAT']['limit_maxbytes']),'</td></tr>';
    		echo '</tbody></table>';
	   }
    echo <<<EOB

		</div>
		<div class="graph div3"><h2>Host Status Diagrams</h2>
		<table cellspacing=0><tbody>
EOB;

	$size='width='.(GRAPH_SIZE+50).' height='.(GRAPH_SIZE+10);
	echo <<<EOB
		<tr>
		<td class=td-0>Cache Usage</td>
		<td class=td-1>Hits &amp; Misses</td>
		</tr>
EOB;

	echo
		graphics_avail() ?
			  '<tr>'.
			  "<td class=td-0><img alt=\"\" $size src=\"$PHP_SELF&IMG=1&".(isset($_GET['singleout'])? 'singleout='.$_GET['singleout'].'&':'')."$time\"></td>".
			  "<td class=td-1><img alt=\"\" $size src=\"$PHP_SELF&IMG=2&".(isset($_GET['singleout'])? 'singleout='.$_GET['singleout'].'&':'')."$time\"></td></tr>\n"
			: "",
		'<tr>',
		'<td class=td-0><span class="green box">&nbsp;</span>Free: ',bsize($mem_avail).sprintf(" (%.1f%%)",$mem_avail*100/$mem_size),"</td>\n",
		'<td class=td-1><span class="green box">&nbsp;</span>Hits: ',$hits.sprintf(" (%.1f%%)",$hits*100/($hits+$misses)),"</td>\n",
		'</tr>',
		'<tr>',
		'<td class=td-0><span class="red box">&nbsp;</span>Used: ',bsize($mem_used ).sprintf(" (%.1f%%)",$mem_used *100/$mem_size),"</td>\n",
		'<td class=td-1><span class="red box">&nbsp;</span>Misses: ',$misses.sprintf(" (%.1f%%)",$misses*100/($hits+$misses)),"</td>\n";
		echo <<< EOB
	</tr>
	</tbody></table>
<br/>
	<div class="info"><h2>Cache Information</h2>
		<table cellspacing=0><tbody>
		<tr class=tr-0><td class=td-0>Current Items(total)</td><td>$curr_items ($total_items)</td></tr>
		<tr class=tr-1><td class=td-0>Hits</td><td>{$hits}</td></tr>
		<tr class=tr-0><td class=td-0>Misses</td><td>{$misses}</td></tr>
		<tr class=tr-1><td class=td-0>Request Rate (hits, misses)</td><td>$req_rate cache requests/second</td></tr>
		<tr class=tr-0><td class=td-0>Hit Rate</td><td>$hit_rate cache requests/second</td></tr>
		<tr class=tr-1><td class=td-0>Miss Rate</td><td>$miss_rate cache requests/second</td></tr>
		<tr class=tr-0><td class=td-0>Set Rate</td><td>$set_rate cache requests/second</td></tr>
		</tbody></table>
		</div>

EOB;

    break;

    case 2: // variables

		$m=0;
		$cacheItems= getCacheItems();
		$items = $cacheItems['items'];
		$totals = $cacheItems['counts'];
		$maxDump = MAX_ITEM_DUMP;
		foreach($items as $server => $entries) {

    	echo <<< EOB

			<div class="info"><table cellspacing=0><tbody>
			<tr><th colspan="2">$server</th></tr>
			<tr><th>Slab Id</th><th>Info</th></tr>
EOB;

			foreach($entries as $slabId => $slab) {
			    $dumpUrl = $PHP_SELF.'&op=2&server='.(array_search($server,$MEMCACHE_SERVERS)).'&dumpslab='.$slabId;
				echo
					"<tr class=tr-$m>",
					"<td class=td-0><center>",'<a href="',$dumpUrl,'">',$slabId,'</a>',"</center></td>",
					"<td class=td-last><b>Item count:</b> ",$slab['number'],'<br/><b>Age:</b>',duration($time-$slab['age']),'<br/> <b>Evicted:</b>',((isset($slab['evicted']) && $slab['evicted']==1)? 'Yes':'No');
					if ((isset($_GET['dumpslab']) && $_GET['dumpslab']==$slabId) &&  (isset($_GET['server']) && $_GET['server']==array_search($server,$MEMCACHE_SERVERS))){
					    echo "<br/><b>Items: item</b><br/>";
					    $items = dumpCacheSlab($server,$slabId,$slab['number']);
                        // maybe someone likes to do a pagination here :)
					    $i=1;
                        foreach($items['ITEM'] as $itemKey=>$itemInfo){
                            $itemInfo = trim($itemInfo,'[ ]');


                            echo '<a href="',$PHP_SELF,'&op=4&server=',(array_search($server,$MEMCACHE_SERVERS)),'&key=',base64_encode($itemKey).'">',$itemKey,'</a>';
                            if ($i++ % 10 == 0) {
                                echo '<br/>';
                            }
                            elseif ($i!=$slab['number']+1){
                                echo ',';
                            }
                        }
					}

					echo "</td></tr>";
				$m=1-$m;
			}
		echo <<<EOB
			</tbody></table>
			</div><hr/>
EOB;
}
		break;

    break;

    case 4: //item dump
        if (!isset($_GET['key']) || !isset($_GET['server'])){
            echo "No key set!";
            break;
        }
        // I'm not doing anything to check the validity of the key string.
        // probably an exploit can be written to delete all the files in key=base64_encode("\n\r delete all").
        // somebody has to do a fix to this.
        $theKey = htmlentities(base64_decode($_GET['key']));

        $theserver = $MEMCACHE_SERVERS[(int)$_GET['server']];
        list($h,$p) = get_host_port_from_server($theserver);
        $r = sendMemcacheCommand($h,$p,'get '.$theKey);
        echo <<<EOB
        <div class="info"><table cellspacing=0><tbody>
			<tr><th>Server<th>Key</th><th>Value</th><th>Delete</th></tr>
EOB;
        if (!isset($r['VALUE'])) {
            echo "<tr><td class=td-0>",$theserver,"</td><td class=td-0>",$theKey,
                 "</td><td>[The requested item was not found or has expired]</td>",
                 "<td></td>","</tr>";
        }
        else {

            echo "<tr><td class=td-0>",$theserver,"</td><td class=td-0>",$theKey,
                 " <br/>flag:",$r['VALUE'][$theKey]['stat']['flag'],
                 " <br/>Size:",bsize($r['VALUE'][$theKey]['stat']['size']),
                 "</td><td>",chunk_split($r['VALUE'][$theKey]['value'],40),"</td>",
                 '<td><a href="',$PHP_SELF,'&op=5&server=',(int)$_GET['server'],'&key=',base64_encode($theKey),"\">Delete</a></td>","</tr>";
        }
        echo <<<EOB
			</tbody></table>
			</div><hr/>
EOB;
    break;
    case 5: // item delete
    	if (!isset($_GET['key']) || !isset($_GET['server'])){
			echo "No key set!";
			break;
        }
        $theKey = htmlentities(base64_decode($_GET['key']));
		$theserver = $MEMCACHE_SERVERS[(int)$_GET['server']];
		list($h,$p) = get_host_port_from_server($theserver);
        $r = sendMemcacheCommand($h,$p,'delete '.$theKey);
        echo 'Deleting '.$theKey.':'.$r;
	break;
    
   case 6: // flush server
        $theserver = $MEMCACHE_SERVERS[(int)$_GET['server']];
        $r = flushServer($theserver);
        echo 'Flush  '.$theserver.":".$r;
   break;
}
echo getFooter();

?>
