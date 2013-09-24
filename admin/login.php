<?php
// 定义管理后台路径
defined ( 'ADMIN_PATH' ) or define ( 'ADMIN_PATH', dirname ( __FILE__ ) );
// 加载公共文件
require ADMIN_PATH . '/admin.php';

$udid =@ isset($_GET['udid'])?$_GET['udid']:null;
$appkey =@ isset($_GET['appkey'])?$_GET['appkey']:null;

$appid = loginApp($appkey);

if (!$appid) {
	halterror();
} else {
	$userdata = array(
			'udid' => $udid,
			'appid' => $appid
	);

	$result = login($userdata);

	if ($result) {
		haltsuccess($result);
	} else {
		halterror();
	}
}

