<?php
/**
 * 常用函数
 */

/**
 * 页面跳转
 *
 * @param string $url
 * @param int $time
 * @param string $msg
 * @return void
 */
function redirect($url, $time = 0, $msg) {
	// 多行URL地址支持
	$url = str_replace ( array (
			"\n",
			"\r"
	), '', $url );
	if (! headers_sent ())
		header ( "Content-Type:text/html; charset=utf-8" );
	$data = array (
			'Location' => $url
	);
	if ($time)
		$data = array_merge ( $data, array (
				'Time' => $time
		) );
	if ($msg)
		$data = array_merge ( $data, array (
				'Message' => $msg
		) );
	if (! headers_sent ()) {
		if (0 === intval ( $time )) {
			header ( "Location: {$url}" );
		}
	}
	$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	$html .= '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	$html .= '<meta http-equiv="refresh" content="' . $time . ';url=' . $url . '" />';
	$html .= '<title>' . Redirecting . '</title>';
	$html .= '<script type="text/javascript" charset="utf-8">';
	$html .= 'window.setTimeout(function(){location.replace("' . $url . '");}, ' . ($time * 1000) . ');';
	$html .= '</script>';
	$html .= '</head><body>';
	$html .= 0 === $time ? null : $msg;
	$html .= '</body></html>';
	exit ( $html );
}

/**
 * array转化json加密
 */
function arraytojson($array) {
	//转化成json字串
	$json = json_encode($array);
	//解决json中中文显示
	$json = preg_replace("#\\\u([0-9a-f]+)#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $json);
	//base64加密
// 	$json = base64_encode($json);
	return $json;
}

/**
 * 错误提示
 */
function halterror() {
	$error = array(
		'code' => ERROR
	);
	echo arraytojson($error);
}

/**
 * 正确返回
 * @param array $message 返回信息
 */
function haltsuccess($message) {
	$result = array(
		'code' => SUCCESS
	);
	if (!is_null($message)) {
		$result = array_merge_recursive($result, $message);
	}
	echo arraytojson($result);
}

/**
 * 解析token
 */
function analysisToken($token) {
	if (!is_string($token)) {
		return false;
	}
	//解密token
	$str = base64_decode($token);
	//拆分字符串
	$array = explode('|', $str);
	if (count($array) != 3) {
		return false;
	}
	return array(
		'udid' => $array[0],
		'appid' => $array[1]
	);
}

/**
 * 生成guid
 *
 * @param $randid 字符串
 * @return string guid
 */
function guid($mix = null) {
	if (is_null ( $mix )) {
		$randid = uniqid ( mt_rand (), true );
	} else {
		if (is_object ( $mix ) && function_exists ( 'spl_object_hash' )) {
			$randid = spl_object_hash ( $mix );
		} elseif (is_resource ( $mix )) {
			$randid = get_resource_type ( $mix ) . strval ( $mix );
		} else {
			$randid = serialize ( $mix );
		}
	}
	$randid = strtoupper ( md5 ( $randid ) );
	$hyphen = chr ( 45 );
	$result = array ();
	$result [] = substr ( $randid, 0, 8 );
	$result [] = substr ( $randid, 8, 4 );
	$result [] = substr ( $randid, 12, 4 );
	$result [] = substr ( $randid, 16, 4 );
	$result [] = substr ( $randid, 20, 12 );
	return implode ( $hyphen, $result );
}

/**Code highlighting produced by Actipro CodeHighlighter (freeware)http://www.CodeHighlighter.com/--> 1 /**
  * @param string $string 原文或者密文
  * @param string $operation 操作(ENCODE | DECODE), 默认为 DECODE
  * @param string $key 密钥
  * @param int $expiry 密文有效期, 加密时候有效， 单位 秒，0 为永久有效
  * @return string 处理后的 原文或者 经过 base64_encode 处理后的密文
  *
  * @example
  *
  *  $a = authcode('abc', 'ENCODE', 'key');
  *  $b = authcode($a, 'DECODE', 'key');  // $b(abc)
  *
  *  $a = authcode('abc', 'ENCODE', 'key', 3600);
  *  $b = authcode('abc', 'DECODE', 'key'); // 在一个小时内，$b(abc)，否则 $b 为空
*/
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 3600) {

	$ckey_length = 4;
	// 随机密钥长度 取值 0-32;
	// 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
	// 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
	// 当此值为 0 时，则不产生随机密钥

	$key = md5($key ? $key : 'default_key'); //这里可以填写默认key值
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