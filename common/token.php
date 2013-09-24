<?php

function getToken($userdata) {
	$udid = $userdata['udid'];
	$appid = $userdata['appid'];
	$timestamp = microtime(true);
	$str = $udid.'|'.$appid.'|'.$timestamp;
	$token = base64_encode($str);
	if (!$token) {
		halterror(TOKEN_NULL);
	}
	return $token;
}
