<?php
/**
 * 数据库配置信息
 */

return [
    // 是否开启
    'MYSQL_STATUS' => true,
    'REDIS_STATUS' => false,

    // MySQL
    'MYSQL' => [
        // 主库
        'MASTER' => [
            'DB_TYPE' => 'mysql',  // 数据源名称
            'DB_NAME' => 'test',  // 数据库名称
            'DB_HOST' => '127.0.0.1',  // 主机名或ip地址
            'DB_USER' => 'root',  // 用户名
            'DB_PASS' => 'root',  // 密码
            'DB_PORT' => '3306',  // 端口号
            'DB_CHARSET' => 'utf8mb4',  // 字符集
            'DB_PREFIX' => '',  // 表前缀
            'DB_PCONNECT' => false,  // 是否长链接
            'DB_DEBUG' => false,  // 是否调试
        ],
        // 从库
        'SLAVE' => [],
    ],

    // Redis
    'REDIS' => [
        'DB_HOST' => '127.0.0.1',
        'DB_PORT' => '6379',
        'DB_AUTH' => '',
    ],



];