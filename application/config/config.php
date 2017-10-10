<?php
/**
 * web 应用配置信息
 */

return [

    // 服务器设置
    'SERVER_HOST' => 'kfzphp.test.cn',    // 服务器访问地址

    // HTTP配置
    'HTTP_CONTENT_TYPE' => 'text/html', // 媒体格式
    'HTTP_CHARSET' => 'UTF-8',  // 字符集
    'HTTP_CACHE_CONTROL' => 'no-cache, no-store, must-revalidate',  // 页面缓存控制
    'HTTP_X_POWERED_BY' => 'Kfzer',  // X-Powered-By

    // 应用设置
    'APP_EXT_CONFIG' => array('db', 'word'),

    // Cookie设置
    // 'COOKIE_' => '',

    // 默认设定
    'DEFAULT_MODULE' => 'mobile',   // 默认模块
    'DEFAULT_CONTROLLER' => 'index',    // 默认控制器
    'DEFAULT_ACTION' => 'index',    // 默认操作

    // 数据缓存设置
    // 'CACHE_' => '',

    // 错误设置
    // 'ERROR_' => '',

    // 日志设置
    // 'LOG_' => '',

    // SESSION设置
    // 'SESSION_' => '',

    // 模板引擎设置
    'TPL_L_DELIM' => '{#:', // 模板引擎普通标签开始标记
    'TPL_R_DELIM' => '}',   // 模板引擎普通标签结束标记
    'TPL_DEBUG_ON' => false,    // 开启调试
    'TPL_CACHE_ON' => false,    // 开启缓存
    'TPL_CACHE_TIME' => 120,    // 缓存存活时间（秒）
    'TPL_SUFFIX' => '.html',    // 模板后缀
    'TPL_STATUS' => true,   // 开启模板引擎

    // 调试设置
    // 'DEBUG_' => '',

    // URL设置
    'URL_REWRITE_SUFFIX' => '.do',    // url 伪静态扩展

    // 系统变量名称设置
    // 'VAR_' => '',

    // 文件对应
    'FILE_MESSAGE_IMG' => [
        'PROMPT' => 'prompt.png',     // 提示标志
        'ERROR' => 'error.png',     // 错误标志
        'WARN' => 'warn.png',    // 警告标志
    ],

];