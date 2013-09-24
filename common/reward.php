<?php

/**
 * 奖励操作
 *  addReward 添加奖励
 */

/**
 * 奖励添加
 * @param array $rewarddata
 * rewarddata {
 *	udid,
 *	appid,
 *	eventid
 * }
 */

function addReward($rewarddata) {
	if (!is_array($rewarddata)) {
		return false;
	}
	//实例数据库操作单例
	$db = DB::getInstance();
	$userdata = array_slice($rewarddata, 0, 2);
	//查询父类udid
	$toudid = @get_par_udid($userdata);
	if (!$toudid) {
		return false;
	}
	//添加奖励日期
	$date = date ('Y-m-d H:i:s', time());
	//奖励信息
	$data = array(
			'fromudid' => $rewarddata['udid'],
			'toudid' => $toudid,
			'appid' => $rewarddata['appid'],
			'eventid' => $rewarddata['eventid'],
			'status' => 0,
			'date' => $date
	);
	//插入数据库user表
	if($db->insert('reward', $data)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 添加系统奖励
 * @param array $rewarddata
 * 	rewarddata {
 * 		udid,
 * 		appid,
 * 		eventid
 * }
 */
function addRewardbysystem($rewarddata) {
	if (!is_array($rewarddata)) {
		return false;
	}
	//实例数据库操作单例
	$db = DB::getInstance();
	//添加奖励日期
	$date = date ('Y-m-d H:i:s', time());
	//奖励信息
	$data = array(
			'fromudid' => 'system',
			'toudid' => $rewarddata['toudid'],
			'appid' => $rewarddata['appid'],
			'eventid' => $rewarddata['eventid'],
			'status' => 0,
			'date' => $date
	);
	//插入数据库user表
	if($db->insert('reward', $data)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 修改领奖状态
 * @param string $tousr
 */
function editRewardStatusBycode($touser) {
	if (!is_string($touser)) {
		return false;
	}
	//修改条件
	$condition = "touser='{$touser}'";
	//奖励状态为1 ：已领取
	$data = array("status" => 1);
	//获取数据库操作单例
	$db = DB::getInstance();
	if($db->update('reward', $data, $condition)) {
		return true;
	} else {
		return false;
	}
}

function editRewardStatusByids($rewardids) {
	if (!is_array($rewardids)) {
// 		echo "奖励id集错误或不存在";
		return false;
	}
	$cond = implode($rewardids, ',');
	$condition = "rewardid in ({$cond})";
	//奖励状态为1 ：已领取
	$data = array("status" => 1);
	$db = DB::getInstance();
	if($db->update('reward', $data, $condition)) {
// 		echo "奖励状态通过id修改成功";
		return true;
	} else {
// 		echo "奖励状态通过id修改错误";
		return false;
	}
}

/**
 * 获取奖励
 * @param array $userdata
 * userdata {
 * 	udid,
 * 	appid
 * }
 * return array $nickname $eventid
 */
function getReward($userdata) {
	if (!is_array($userdata)) {
		return false;
	}
	$sql = "select `rewardid`,`eventid` from reward where toudid='{$userdata['udid']}' and appid='{$userdata['appid']}' and status='0';";
	$db = DB::getInstance();
	$res = @$db->get_all($sql);
	//没有奖励记录 return false
	if (!count($res)) {
		return array(
			'count' => 0
		);
	}
	//奖励id集
	$rewardids = array();
	//事件id集
	$eventids = array();
	for ($i = 0; $i < count($res); $i++) {
		$rewardids[$i] = $res[$i]['rewardid'];
		$eventids[$i] = $res[$i]['eventid'];
	}
	//事件集
	$events = array();
	if (count($eventids)) {
		$events = getEvents($eventids);
	}
	if (!$events) {
		return false;
	}
	//奖励数量
	$count = count($events);
	$rewards = array(
		'count' => $count,
		'rewards' => $events
	);
	if ($rewards) {
		//更新奖励状态
		editRewardStatusByids($rewardids);
		return $rewards;
	} else {
		return false;
	}
}

/**
 * 获取nickname集
 * @param array $fromudids
 * @param array $userdata
 * @return multitype:string unknown
 */
function getNicknames($fromudids, $userdata) {
	$nicknames = array();
	$system = array('system');
	$diff = array_diff($fromudids, $system);
	if (count($diff) != count($fromudids)) {
		for ($i = 0; $i < count($fromudids); $i++) {
			$nicknames[$i] = "system";
		}
	} else {
		$nickname = @getNickname(array('udid' => $diff[0], 'appid' => $userdata['appid']));
		for ($i = 0; $i < count($fromudids); $i++) {
			if ($fromudids[$i] == 'system') {
				$nicknames[$i] = "system";
			}
			$nicknames[$i] = $nickname;
		}
	}
	return $nicknames;
}
