<?php
// 定义管理后台路径
defined ( 'ADMIN_PATH' ) or define ( 'ADMIN_PATH', dirname ( __FILE__ ) );
// 加载公共文件
require ADMIN_PATH . '/admin.php';

$token =@ isset($_GET['token'])?$_GET['token']:null;
$eventid =@ isset($_GET['eventid'])?$_GET['eventid']:null;

//解析token
$uid = analysisToken($token);
//更多信息
$more = array(
	'eventid' => $eventid
);
//用户数据
$userdata = array_merge_recursive($uid, $more);
//添加奖励事件
$result = addReward($userdata);

if ($result) {
	haltsuccess();
} else {
	halterror();
}