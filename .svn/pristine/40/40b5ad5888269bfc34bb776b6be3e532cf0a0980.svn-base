<?php

define('IN_GW', true);

if(in_array($action, array('profile', 'device', 'callback', 'appstore'))) {
	$do = $action;
	$action = 'redirect';
}
if($action == 'touch') {
	exit('success');
}
setting_load('site');
