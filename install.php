<?php
//包含db.php文件
require 'db.php';

//创建数据库表
function create_tables() {
	//实例数据库操作类
	$db = new DB();
	if ($db) {
// 		//创建app表
// 		$db->query("create table app (
// 			`appid` int(10) unsigned not null auto_increment,
// 			`appkey` varchar(150) not null,
// 			`appname` varchar(100),
// 			`appdesp` text,
// 			`date` timestamp not null,
// 			primary key (`appid`),
// 			unique key `appkey` (`appkey`)
// 			)ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		//创建user表
		$db->query("create table user (
			`userid` bigint(20) unsigned not null auto_increment,
			`udid` varchar(50) not null,
			`nickname` varchar(100),
			`appid` int(10) not null,
			`mycode` varchar(10) not null,
			`parcode` varchar(10),
			`country` varchar(5) not null,
			`time` tinyint(2) ,
			`date` timestamp not null,
			`days` smallint(4) default '0' not null,
			primary key (`userid`),
			unique key `code` (`mycode`, `parcode`),
			foreign key (`appid`) references app(`appid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		//创建event表
		$db->query("create table event (
			`eventid` int(10) unsigned not null auto_increment,
			`eventtype` tinyint(1) not null,
			`eventdesp` text not null,
			`money` int(10) not null,
			`date` timestamp NOT NULL,
			primary key (`eventid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		//创建reward表
		$db->query("create table reward (
			`rewardid` bigint(20) unsigned auto_increment,
			`fromudid` varchar(50) not null,
			`toudid` varchar(50) not null,
			`appid` int(10) not null,
			`eventid` int(10) unsigned not null,
			`status` tinyint(1) default '0' not null,
			`date` timestamp not null,
			primary key (`rewardid`),
			foreign key (`eventid`) references event(`eventid`),
			foreign key (`appid`) references app(`appid`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
	} else {
		echo "实例数据库失败.";
	}
	//关闭连接
	$db->close();
}

create_tables();

