<?php
// 定义项目物理跟路径
define('ABS_PATH', dirname(__FILE__));

// 定义项目物理公共目录
define('COM_PATH', ABS_PATH.'/common');

define('ADMIN_PATH', ABS_PATH.'/admin');

require 'defines.php';

require 'functions.php';

require COM_PATH."/user.php";

require COM_PATH.'/event.php';

require COM_PATH.'/reward.php';

require COM_PATH.'/token.php';

require COM_PATH.'/app.php';

require ABS_PATH.'/db.php';