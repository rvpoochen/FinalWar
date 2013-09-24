<?php
// 定义管理后台路径
defined ( 'ADMIN_PATH' ) or define ( 'ADMIN_PATH', dirname ( __FILE__ ) );
// 加载公共文件
require ADMIN_PATH . '/admin.php';

$token =@ isset($_GET['token'])?$_GET['token']:null;
$code =@ isset($_GET['code'])?$_GET['code']:null;
//转化成大写
$parcode = array(
	'parcode' => strtoupper($code)
);
//解析token
$uid = analysisToken($token);
//用户信息
$userdata = array_merge_recursive($uid, $parcode);
//post邀请码
$result = postCode($userdata);

if ($result) {
	haltsuccess($result);
} else {
	halterror();
}
