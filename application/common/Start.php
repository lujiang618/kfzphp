<?php
/**
 * 公共入口文件
 */

// 版本信息
const KFZPHP_VERSION      =   '1.0.0';
// 类文件后缀
const CLASS_EXT           =   '.class.php';

// 定义系统主目录常量
define('WEB_PATH',        '../web');                    // 应用根目录
define('TEMPLATE_PATH',   '../template');               // 模板目录
define('RUNTIME_PATH',    '../runtime');                // 运行时目录

// 定义系统子目录常量
define('COMMON_PATH',     APP_PATH . '/common');        // 应用公共目录
define('CONFIG_PATH',     APP_PATH . '/config');        // 应用配置目录
define('FUNCTION_PATH',   APP_PATH . '/function');      // 函数库目录
define('LIBRARY_PATH',    APP_PATH . '/library');       // 业务类库目录
define('MODULE_PATH',     APP_PATH . '/module');        // 业务逻辑目录
define('MASTER_PATH',     LIBRARY_PATH . '/master');    // 自主类库目录
define('VENDOR_PATH',     LIBRARY_PATH . '/vendor');    // 第三方类库目录
define('CACHE_PATH',      RUNTIME_PATH . '/cache');     // 系统运行时目录
define('DATA_PATH',       RUNTIME_PATH . '/data');      // 系统文件目录
define('LOGS_PATH',       RUNTIME_PATH . '/logs');      // 系统日志目录
define('UPLOAD_PATH',     WEB_PATH . '/upload');        // 定义上传目录
define('PUBLIC_PATH',     WEB_PATH . '/public');        // 定义素材目录

// 定义自定义常量
define('BASE_URL',        'http://' . $_SERVER['HTTP_HOST']);
define('SITE_URL',        BASE_URL . $_SERVER['REQUEST_URI']);
define('TIMESTAMP',       $_SERVER['REQUEST_TIME']);      // 当前的时间戳
define('REQUEST_METHOD',  $_SERVER['REQUEST_METHOD']);
define('IS_AJAX',         isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
define('IS_POST',         (REQUEST_METHOD == 'POST') ? true : false);
define('IS_WECHAT',       strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);      // 是否是微信端
// define('IS_CGI',          (0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? true : false);
// define('IS_CLI',          PHP_SAPI=='cli'? true : false);
// define('IS_WIN',          strstr(PHP_OS, 'WIN') ? true : false);

// 初始化设置
@ini_set('memory_limit',          '128M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    1);
@ini_set('display_errors',        1);   // php开启与关闭错误提示
@ini_set('arg_separator.output',  '&amp;');
@ini_set('date.timezone',         'Asia/Shanghai');

// 报错级别
if(APP_DEBUG) {
    error_reporting(E_ALL ^ E_NOTICE);
} else {
    error_reporting(E_ALL);
}

// 加载基本配置文件
$_CFG = include CONFIG_PATH . '/config.php';
// 整合扩展配置文件
if(!empty($_CFG['APP_EXT_CONFIG'])) {
    // 循环加载配置文件
    foreach ($_CFG['APP_EXT_CONFIG'] as $k => $v) {
        $config = CONFIG_PATH . '/' . $v . '.php';
        if(file_exists($config)) {
            $_CFG = array_merge($_CFG, include $config);
        }
    }
}

// 加载公共函数文件
require FUNCTION_PATH . '/function.php';

// 对系统目录的生成
// generate_system_dir();

// 引入核心类
require COMMON_PATH . '/App' . CLASS_EXT;
require COMMON_PATH . '/Dispatch' . CLASS_EXT;
// 创建应用对象
$dispatch = new Dispatch();
$dispatch->route();  // 路由解析
$dispatch->start();  // 自动加载
$dispatch->run();    // 运行应用
