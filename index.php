<?php
// 生成json
// $arr = array('a'=> 1, 'b'=> 2, 'c' => 3, 'd' => 4);
// echo json_encode($arr);

//解析json
// $json = '{"foo": 12345}';
// $obj = json_decode($json);
// echo $obj->{'foo'}; // 12345

//base64 加密
// $str = 'This is an encoded string';
// echo base64_encode($str);

//base64 解密
// $str = "VGhpcyBpcyBhbiBlbmNvZGVkIHN0cmluZw==";
// echo base64_decode($str);

//生成邀请码
// function getCode($num, $bit = 4) {
// 	$chars = array('0','1','2','3','4','5','6','7','8','9',
// 			'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
// 	$base = count($chars);
// 	for ($i = 0; $i < $bit; $i += 1) {
// 		$ch = $chars[ $num % $base];
// 		$num /= $base;
// 		$code .= $ch;
// 	}
// 	return $code;
// }

// //用户登录
// $user = array(
// 	"udid" => "cdefgh",
// 	"appid" => 1,
// 	"nickname" => "thanks",
// 	"country" => "cn",
// 	"time" => 1
// );

// $child = array(
// 	"udid" => "fghijk",
// 	"appid" => 1,
// 	"nickname" => "fff",
// 	"country" => "en",
// 	"time" => 0
// );

// //用户修改
// $userdata = array(
// 	"udid" => "bcdefg",
// 	"appid" => 1,
// 	"parcode" => "0000"
// );

// //事件添加
// $event1 = array(
// 	"eventid" => 1,
// 	"eventtype" => 1,
// 	"eventdesp" => "登录一天",
// 	"money" => 10
// );

// $event2 = array(
// 		"eventid" => 2,
// 		"eventtype" => 1,
// 		"eventdesp" => "登录两天",
// 		"money" => 20
// );

// $event3 = array(
// 		"eventid" => 3,
// 		"eventtype" => 1,
// 		"eventdesp" => "登录三天",
// 		"money" => 30
// );

//事件修改
// $event = array(
// 	"eventid" => 1,
// 	"eventtype" => 0,
// 	"money" => 10
// );

//事件删除
// $event = array(
// 	"eventid" => 1
// );

//奖励
// $rewarddata = array(
// 	"udid" => "bcdefg",
// 	"appid" => 1,
// );

//模块化 类
/**
 * 1 user login (udid,appid)
 * 		数据库判断是否存在 有 根据udid,appid,更新登录时间 返回错误码
 * 		没有 返回
 * 				接收到需要更多信息，再次发送(nickname,country,time) 注册user信息
 * 2 获取自己的邀请码 udid,appid
 * 		是否存在 。。
 *
 * 3 输入邀请码 udid,appid,code 更新用户信息
 *
 * 4 获取奖励 udid,appid
 *
 * 5 事件添加管理
 *
 * 6 根据用户所在时区自动更新奖励
 */
require 'functions.php';

//方法
$method =@ isset($_GET['method'])?$_GET['method']:null;
$udid =@ isset($_GET['udid'])?$_GET['udid']:null;
$appkey =@ isset($_GET['appkey'])?$_GET['appkey']:null;
$token =@ isset($_GET['token'])?$_GET['token']:null;
$code =@ isset($_GET['code'])?$_GET['code']:null;
$country =@ isset($_GET['country'])?$_GET['country']:null;
$eventid =@ isset($_GET['eventid'])?$_GET['eventid']:null;

switch ($method) {
	case 'login':
	redirect('admin/login.php?'.'udid='.$udid.'&appkey='.$appkey);
	break;
	case 'register':
	redirect('admin/register.php?'.'token='.$token.'&country='.$country);
	break;
	case 'getcode':
	redirect('admin/getcode.php?'.'token='.$token);
	break;
	case 'postcode':
	redirect('admin/postcode.php?'.'token='.$token.'&code='.$code);
	break;
	case 'addreward':
	redirect('admin/addreward.php?'.'token='.$token.'&eventid='.$eventid);
	break;
	case 'getreward':
	redirect('admin/getreward.php?'.'token='.$token);
	break;
	default:
	echo 'FinalWarExp:8845384B-E647-1628-5600-DD3B661926B5';
	echo '<br />';
	echo 'FinalWar3C:454D8C91-20B9-6649-58AB-E3B6C0D0CA08';
}
