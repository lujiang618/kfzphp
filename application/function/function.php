<?php
/**
 * 公共函数库
 */

/**
 * 打印调试函数
 * @author Wally
 * @since  2016-11-01
 * @param  [type]     $val [值]
 */
function p($val)
{
    if(is_string($val) || is_int($val)) {
        echo "<pre>";
        echo $val;
    } else if(is_object($val) || is_bool($val)) {
        echo "<pre>";
        var_dump($val);
    } else {
        echo "<pre>";
        print_r($val);
    }
}
function pt($val)
{
    p($val);
    exit;
}
function pc()
{
    pt(get_defined_constants());
}
function pp($val)
{
    $openid = $GLOBALS['_SESSION']['openid'];
    $opentel = $GLOBALS['_SESSION']['opentel'];
    if(!empty($openid) && in_array($openid, array_filter(my_array_column(C('TEST_USERS'), 'openid')))) {
        pt($val);
    } else if(!empty($opentel) && in_array($opentel, array_filter(my_array_column(C('TEST_USERS'), 'opentel')))) {
        pt($val);
    }
}
function phtml($html)
{
    echo "htmlentities: <br /> ";
    echo htmlentities($html,ENT_QUOTES,"UTF-8");
    exit;
}
/**
 * 页面打印消息提示
 * @param  string $stat [0警告 1错误 2提示]
 * @param  string $msg  [信息内容]
 * @param  string $sub  [副内容]
 * @param  string $url  [跳转URL]
 * @return [type]       [description]
 */
function message($msg = '', $stat = 'PROMPT', $sub = '', $url = 'javascript:;')
{
    $img = '/public/common/images/' . $GLOBALS['_CFG']['FILE_MESSAGE_IMG'][$stat];
    // if(empty($url)) {
    //     // 判断是否是微信
    //     $url = IS_WECHAT ? default_url('wap') : default_url('adm');
    // }
    echo <<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
        <title>友情提示</title>
        <link rel="stylesheet" type="text/css" href="/public/common/css/adm_style.css"/>
    </head>
    <body>
        <div id="reason">
            <div class="weui_msg">
                <div class="weui_icon_area"> <img src="{$img}"/> </div>
                <div class="weui_text_area">
                    <h2 class="weui_msg_title">{$msg}</h2>
                    <p class="weui_msg_desc">{$sub}</p>
                </div>
                <div class="weui_opr_area">
                    <div class="weui_btn_area"></div>
                </div>
                <a class="smallBtn" href="{$url}">Home</a>
            </div>
        </div>
    </body>
</html>
EOF;
    exit;
}

/**
 * 读取配置文件
 * @author Wally
 * @since  2016-11-12
 * @param  string     $val [description]
 */
function C($val = '')
{
    $conf1 = $GLOBALS['_CFG'];
    $conf2 = include CONFIG_PATH . '/' . MODULE_NAME . '.php';
    $conf2 = !empty($conf2) ? $conf2 : array();
    $config = array_merge($conf1, $conf2);
    if(empty($val)) {
        return $config;
    } else if($val && isset($config[$val])) {
        return $config[$val];
    } else {
        return false;
    }
}









