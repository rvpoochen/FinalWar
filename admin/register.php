<?php
// 定义管理后台路径
defined ( 'ADMIN_PATH' ) or define ( 'ADMIN_PATH', dirname ( __FILE__ ) );
// 加载公共文件
require ADMIN_PATH . '/admin.php';

$token =@ isset($_GET['token'])?$_GET['token']:null;
$country =@ isset($_GET['country'])?$_GET['country']:null;

//解析token
$uid = analysisToken($token);
//更多信息
$more = array(
	'country' => $country
);
//用户注册信息
$userdata = array_merge_recursive($uid, $more);
//注册用户
$result = addUser($userdata);
//返回结果处理
if ($result) {
	haltsuccess();
} else {
	halterror();
}
