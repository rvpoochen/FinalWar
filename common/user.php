<?php
/**
 * 用户操作
 *
 * login 用户登陆
 * isexist 用户存在
 * addUser 添加用户
 * edit 修改用户信息
 * delete 删除用户
 * getInfo 获取用户信息
 * getCode 获取邀请码
 */

/**
 * 用户登录
 * @param json $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 * @return string token
 */
function login($userdata) {
	//检验是否注册
	if (isexistUser($userdata)) {
		//已注册
		$isregister = 0;
		//获取登录令牌
		$token = getToken($userdata);
		//登录相关处理
		solveLogin($userdata);
		return array(
			'isregister' => $isregister,
			'token' => $token
		);
	} else {
		//未注册
		$isregister = 1;
		//获取登录令牌
		$token = getToken($userdata);
		return array(
				'isregister' => $isregister,
				'token' => $token
		);
	}
}

/**
 *	用户是否存在
 * @param json $userdata 用户信息
 * @return boolean
 */
function isexistUser($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	$info = getInfo($userdata);
	if($info) {
		return true;
	} else {
		return false;
	}
}

/**
 *	添加用户
 * @param json $userdata
 * userdata {
 * 	udid,
 * 	appid,
 * 	nickname,
 *	country,
 *	time
 * }
 *
 */
function addUser($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	//统计user表中记录总数
	$num = getNumbyappid($userdata['appid']);
	//四位邀请码
	$bit = 4;
	//超过则变成5位
	if ($num > 36*36*36*36) {
		$bit = 5;
		$num = $num - 36*36*36*36;
	}
	//生成code
	$mycode = getCode($num, $bit);
	//生成日期
	$date = date ('Y-m-d H:i:s', time());
	//用户信息
	$data = array(
		'udid' => $userdata['udid'],
		'appid' => $userdata['appid'],
		'mycode' => $mycode,
		'country' => $userdata['country'],
		'date' => $date
	);
	//获取数据库操作对象单例
	$db = &DB::getInstance();
	//插入数据库user表
	if($db->insert('user', $data)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 修改用户信息
 * @param array $editdata
 * editdata {
 * 	udid,
 * 	appid,
 * 	...
 * }
 */
function editUser($editdata) {
	if (!is_array($editdata)) {
		return false;
	}
	//用户修改条件
	$condition = "udid='{$editdata['udid']}' and appid='{$editdata['appid']}'";
	//用户修改数据
	$data = array_slice($editdata, 2);
	//获取数据库操作单例
	$db = DB::getInstance();
	if($db->update('user', $data, $condition)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 修改用户登录时间
 * @param array $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 */
function editDate($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	//生成日期
	$date = array( 'date' => date('Y-m-d H:i:s', time()));
	//修改参数
	$editdata = array_merge_recursive($userdata, $date);
	//修改登录时间
	if (editUser($editdata)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 删除用户
 * @param array $deletedata
 * deletedata {
 * 	udid,
 * 	appid
 * }
 */
function deleteUser($deletedata) {
	if (!is_array($deletedata)) {
// 		echo "用户删除信息错误或者不存在.";
		return false;
	}
	//用户删除条件
	$condition = "udid='{$deletedata['udid']}' and appid='{$deletedata['appid']}'";
	//获取数据库操作单例
	$db = DB::getInstance();
	if($db->delete('user', $condition)) {
		return true;
	} else {
// 		echo "用户删除错误";
		return false;
	}
}

/**
 * 生成邀请码
 * @param $num userid
 * @param $bit 生成位数
 * @return string 返回邀请码数组
 */
function getCode($num, $bit = 4) {
	//邀请码字符集
	$chars = array('0','1','2','3','4','5','6','7','8','9',
			'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
	//字符集总数
	$base = count($chars);
	for ($i = 0; $i < $bit; $i += 1) {
		$ch = $chars[ $num % $base];
		$num /= $base;
		$code .= $ch;
	}
	return $code;
}

/**
 * post邀请码
 * $userdata {
 * 	udid,
 * 	appid,
 * 	parcode
 * }
 */
function postCode($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	//不能存自己的邀请码
	$mycode = @get_mycode(array_slice($userdata, 0, 2));
	if ($mycode['mycode'] == $userdata['parcode']) {
		return array(
			'success' => 1
		);
	}
	//邀请码集
	$codes = @getallcode($userdata['appid']);
	if (!$codes) {
		return false;
	}
	//遍历邀请码集，看是否有相同的
	for ($i = 0; $i < count($codes); $i++) {
		//如果相同则已post
		if ((string)$userdata['parcode'] == (string)$codes[$i]['parcode']) {
			return array(
				'success' => 1
			);
		}
	}
	for ($i = 0; $i < count($codes); $i++) {
		//如果相同则已post
		if ((string)$userdata['parcode'] == (string)$codes[$i]['mycode']) {
			editUser($userdata);
			return array(
				'success' => 0
			);
		}
	}
	return array(
		'success' => 1
	);
}

/**
 * 登录相关处理
 * @param array $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 */
function solveLogin($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	//获取上次登录时间
	$last = getLastDate($userdata);
	//上次登录的时间戳
	$lasttimestamp = strtotime($last);
	//最近一次登录的第一天的时间戳
	$one = mktime(0, 0, 0, date("m", $lasttimestamp), date("d", $lasttimestamp) + 1, date("Y", $lasttimestamp));
	//最近一次登录的第二天的时间戳
	$two =  mktime(0, 0, 0, date("m", $lasttimestamp), date("d", $lasttimestamp) + 2, date("Y", $lasttimestamp));
	//当前时间
	$nowdate = date ('Y-m-d H:i:s', time());
	//当前时间戳
	$now = strtotime($nowdate);
	//获取已登录天数
	$days = getDays($userdata);
	if ($now < $one) {
		$editdate = array(
			'date' => $nowdate
		);
	}
	if ($now >= $one && $now <= $two) {
		$editdate = array(
			'date' => $nowdate,
			'days' => $days + 1
		);
		require_once 'reward.php';
		//添加奖励
		addRewardbysystem(array(
			'toudid' => $userdata['udid'],
			'appid' => $userdata['appid'],
			'eventid' => 20 + $editdate['days']
		));
	}
	if ($now > $two) {
		$editdate = array(
			'date' => $nowdate,
			'days' => 0
		);
	}
	//更新登录时间、次数
	$edit = array_merge_recursive($userdata, $editdate);
	$result = editUser($edit);
	if ($result) {
		return true;
	} else {
		return false;
	}
}

/**
 * 获取信息
 */

/**
 * 获取用户信息
 * @param array $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 *
 * @return array $info
 */
function getInfo($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	$sql = "select * from user where udid='{$userdata['udid']}' and appid='{$userdata['appid']}';";
	$db = DB::getInstance();
	$result = @$db->get_one($sql);
	$num = count($result);
	if ($num == 0) {
		return false;
	} else {
		return $result;
	}
}


/**
 * 获取用户mycode
 * @param array $userdata
 * userdata {
 * udid,
 * appid
 * }
 * @return array
 */
function get_mycode($userdata){
	if (!is_array($userdata)) {
		return false;
	}
	//获取用户信息集
	$result = @getInfo($userdata);
	if (!$result) {
		return false;
	}
	return array(
		'mycode' => $result['mycode']
	);
}


/**
 * 获取用户parcode
 * @param array $userdata
 * userdata {
 * udid,
 * appid
 * }
 */
function get_parcode($userdata){
	if (!is_array($userdata)) {
		return false;
	}
	$result = @getInfo($userdata);
	if (!$result) {
		return false;
	}
	return (string)$result['parcode'];
}


/**
 * 获取子类mycode
 */
function get_child_mycode($parcode) {
	if(!is_string($parcode)) {
		return false;
	}
	//实例数据库操作
	$db = DB::getInstance();
	$sql = "select * from user where parcode='{$parcode}';";
	//查询数据
	$result = @$db->get_one($sql);
	if (!$result) {
		return false;
	}
	return (string)$result['mycode'];
}


/**
 * 获得子类的udid
 * @param array $pardata
 * pardata {
 * udid,
 * appid
 * }
 */
function get_child_udid($pardata) {
	if(!is_array($pardata)) {
		return false;
	}
	//父类的code
	$parcode = get_mycode($pardata);
	//实例数据库操作
	$db = DB::getInstance();
	$sql = "select * from user where parcode='{$parcode}';";
	//查询数据
	$result = @$db->get_one($sql);
	if (!$result) {
		return false;
	}
	return (string)$result['udid'];
}


/**
 * 获得父类的udid
 * @param array $pardata
 * pardata {
 * udid,
 * appid
 * }
 */
function get_par_udid($chddata) {
	if(!is_array($chddata)) {
		return false;
	}
	//父类的code
	$parcode = get_parcode($chddata);
	//实例数据库操作
	$db = DB::getInstance();
	$sql = "select * from user where mycode='{$parcode}';";
	//查询数据
	$result = @$db->get_one($sql);
	if (!$result) {
		return false;
	}
	return (string)$result['udid'];
}


/**
 * 获取用户的昵称
 * @param array $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 */
function getNickname($userdata) {
	$info = getInfo($userdata);
	if (!$info) {
		return false;
	}
	return $info['nickname'];
}


/**
 * 获取所用code集
 * @param $appid
 *
 */
function getallcode($appid) {
	//实例数据库操作
	$db = DB::getInstance();
	$sql = "select `mycode`,`parcode` from `user` where appid='{$appid}';";
	//查询数据
	$result = @$db->get_all($sql);
	if (!$result) {
		return false;
	}
	return $result;
}


/**
 * 获取用户在游戏中的人数
 * @param  $appid
 *
 **/
function getNumbyappid($appid) {
	$sql = "select * from `user` where appid='{$appid}';";
	$db = DB::getInstance();
	$result = @$db->query($sql);
	if (!$result) {
		return false;
	}
	$count = $db->num_rows($result);
	if ($count) {
		return $count;
	} else {
		return false;
	}
}


/**
 * 获取登录时间
 * @param array $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 */
function getLastDate($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	$sql = "select * from `user` where udid='{$userdata['udid']}' and appid='{$userdata['appid']}';";
	$db = DB::getInstance();
	$result = @$db->get_one($sql);
	if ($result) {
		return $result['date'];
	} else {
		return false;
	}
}

/**
 * 获取登录天数
 * @param array $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 */
function getDays($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	$sql = "select `days` from `user` where udid='{$userdata['udid']}' and appid='{$userdata['appid']}';";
	$db = DB::getInstance();
	$result = $db->get_one($sql);
	if ($result) {
		return $result['days'];
	} else {
		return false;
	}
}

/**
 * 获取时区
 * @param array $userdata
 *  userdata {
 *   udid,
 *   appid
 *  }
 */
function getTimeZone($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	$sql = "select `days` from user where udid='{$userdata['udid']}' and appid='{$userdata['appid']}';";
	$db = DB::getInstance();
	$result = $db->get_one($sql);
	if ($result) {
		return $result['time'];
	} else {
		return false;
	}
}

/**
 * 通过token获取udid及appid
 * @param string $token
 */
function getUdidByToken($token) {
	if (!is_string($token)) {
		return false;
	}
	$sql = "select `udid`,`appid` from user where token='{$token}';";
	$db = DB::getInstance();
	$result = $db->get_one($sql);
	if ($result) {
		return array(
			'udid' => $result['udid'],
			'appid' => $result['appid']
		);
	} else {
		return false;
	}
}