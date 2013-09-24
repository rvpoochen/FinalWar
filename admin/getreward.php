<?php
// 定义管理后台路径
defined ( 'ADMIN_PATH' ) or define ( 'ADMIN_PATH', dirname ( __FILE__ ) );
// 加载公共文件
require ADMIN_PATH . '/admin.php';

$token =@ isset($_GET['token'])?$_GET['token']:null;

//解析token
$userdata = analysisToken($token);

$result = getReward($userdata);
if ($result) {
	haltsuccess($result);
} else {
	halterror();
}