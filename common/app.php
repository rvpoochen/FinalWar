<?php
/**
 * app操作
 * addApp 添加app
 * isExistApp app是否存在
 * editApp 编辑app
 * deleteApp 删除app
 */

/**
 * 登录app
 * @param string $appkey
 */
function loginApp($appkey){
	if (isExistApp($appkey)) {
		$appid = getIdByKey($appkey);
		if ($appid) {
			return $appid ;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

/**
 * app添加
 * @param array $appdata
 * event {
 * 	appid,
 * 	appkey
 * }
 */
function addApp($appdata) {
	if (!is_array($appdata)) {
		return false;
	}
	//生成日期
	$date = date ('Y-m-d H:i:s', time());
	//app信息
	$data = array(
			'appkey' => $appdata['appkey'],
			'date' => $date
	);
	//获取数据库操作对象单例
	$db = &DB::getInstance();
	//插入数据库event表
	if($db->insert('app', $data)) {
		return true;
	} else {
		return false;
	}
}

/**
 * app是否已存在
 * @param string $appkey app的key
 */
function isExistApp($appkey) {
	if (!is_string($appkey)) {
		return false;
	}

	//数据库操作对象
	$db = &DB::getInstance();
	$sql = "select * from `app` where appkey='{$appkey}';";
	//查询数据
	$result = @$db->query($sql);
	if (!$result) {
		return false;
	}
	//记录条数
	$count = @$db->num_rows($result);
	if($count == 0) {
		return false;
	} else {
		return true;
	}
}


/**
 * app相关查询
 * @param string $appkey
 *
 */

function getIdByKey($appkey) {
	if (!is_string($appkey)) {
		return false;
	}
	$sql = "select `appid` from app where appkey='{$appkey}';";
	$db = DB::getInstance();
	$result = @$db->get_one($sql);
	if ($result) {
		return $result['appid'];
	} else {
		return false;
	}
}