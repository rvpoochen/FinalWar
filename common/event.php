<?php

/**
 * 事件操作
 * addEvent 添加事件
 * isexist 事件是否存在
 * editEvent 编辑事件
 * deleteEvent 删除事件
 */

/**
 * 事件添加
 * @param array $eventdata
 * event {
 * 	eventid,
 * 	eventtype,
 * 	eventdesp,
 * 	money
 * }
 */
function addEvent($eventdata) {
	if (!is_array($eventdata)) {
		echo "事件信息错误或不存在.";
	}
	if (isexistEvent($eventdata)) {
		echo "事件已存在";
	}
	//生成日期
	$date = date ('Y-m-d H:i:s', time());
	//事件信息
	$data = array(
			'eventid' => $eventdata['eventid'],
			'eventtype' => $eventdata['eventtype'],
			'eventdesp' => $eventdata['eventdesp'],
			'money' => $eventdata['money'],
			'date' => $date
	);
	//获取数据库操作对象单例
	$db = &DB::getInstance();
	//插入数据库event表
	if(!$db->insert('event', $data)) {
		echo "插入数据库event表错误";
	} else {
		echo "事件添加成功";
		return true;
	}
}

/**
 * 事件是否已存在
 * @param array $eventdata
 */
function isexistEvent($eventdata) {
	if (!is_array($eventdata)) {
		echo "事件信息错误或不存在.";
	}
	$eventid = $eventdata['eventid'];

	//数据库操作对象
	$db = &DB::getInstance();
	$sql = "select * from event where eventid='{$eventid}';";
	//查询数据
	$result = $db->query($sql);
	if (!$result) {
		echo "查询event表信息错误.";
	}
	//记录条数
	$num = @$db->num_rows($result);
	if($num == 0) {
		return false;
	} else {
		return true;
	}
}

/**
 * 事件修改
 * @param unknown $editdata
 * editdata {
 *	eventid,
 *	.....
 * }
 */
function editEvent($editdata) {
	if (!is_array($editdata)) {
		echo "事件修改信息错误或不存在.";;
	}
	//用户修改条件
	$condition = "eventid='{$editdata['eventid']}'";
	//用户修改数据
	$data = array_slice($editdata, 1);
	//获取数据库操作单例
	$db = DB::getInstance();
	if($db->update('event', $data, $condition)) {
		return true;
	} else {
		echo "事件修改信息错误";
		return false;
	}
}

/**
 * 事件删除
 * @param array $deltedata
 * deltedata {
 * 	eventid
 * }
 */
function deleteEvent($deltedata) {
	if (!is_array($deltedata)) {
		return false;
	}
	//事件删除条件
	$condition = "eventid='{$deltedata['eventid']}'";
	//获取数据库操作单例
	$db = DB::getInstance();
	if($db->delete('event', $condition)) {
		return true;
	} else {
		return false;
	}
}

/**
 * 事件相关查询
 */

function getEvents($eventids) {
	if (!is_array($eventids)) {
		return false;
	}
	$cond = implode($eventids, ',');
	$sql = "select * from event where eventid in ({$cond});";
	$db = DB::getInstance();
	$result = $db->get_all($sql);
	$events = array();
	for ($i = 0; $i < count($result); $i++) {
		$event = array(
			'type' => $result[$i]['eventtype'],
			'desp' => $result[$i]['eventdesp'],
			'money' => $result[$i]['money']
		);
		$events[$i] = $event;
	}
	return $events;
}