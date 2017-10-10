<?php
// 检测PHP环境
if(version_compare(PHP_VERSION, '5.4.0', '<'))  die('require PHP > 5.4.0 !');
// 定义应用目录
define('APP_PATH', '../application');
// 开启调试模式
define('APP_DEBUG', true);
// 载入框架入口文件
require APP_PATH . '/common/Start.php';
