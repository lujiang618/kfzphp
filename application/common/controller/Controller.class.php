<?php
/**
 * 控制器根类
 */

class Controller extends App
{
    public function __construct()
    {
        parent::__construct();
        // 加载流程
        self::loadHandle();

        // p('Controller');
    }

    /**
     * 判断加载流程
     * @author Wally
     * @since  2017-09-23
     * @return [type]     [description]
     */
    private function loadHandle()
    {
        if(IS_AJAX) {

        } else {
            // pt(111);
            $this->tpl = self::loadSmarty();
        }
    }

    /**
     * 加载Smarty
     * @author Wally
     * @since  2017-09-23
     * @return [type]     [description]
     */
    private function loadSmarty()
    {
        if(!$this->config['TPL_STATUS']) {
            return false;
        }
        // 创建Smarty对象
        include VENDOR_PATH . '/Smarty/Smarty.class.php';
        $tpl = new Smarty();
        $tpl->template_dir = TEMPLATE_PATH . '/' . MODULE_NAME . '/';    // 模板目录
        $tpl->compile_dir = CACHE_PATH . '/';   // 编译目录
        $tpl->config_dir = CONFIG_PATH . '/';    // 配置项目录
        $tpl->cache_dir  = CACHE_PATH . '/';   // 设置缓存的存放路径
        $tpl->force_compile = false;    // 强迫编译
        // $tpl->compile_check = false;    // 网站上线后使用
        $tpl->debugging = $this->config['TPL_DEBUG_ON'];     // 调试
        $tpl->caching = $this->config['TPL_CACHE_ON'];   // 开启缓存
        $tpl->cache_lifetime = $this->config['TPL_CACHE_TIME'];  // 缓存存活时间（秒）
        $tpl->left_delimiter = $this->config['TPL_L_DELIM'];
        $tpl->right_delimiter = $this->config['TPL_R_DELIM'];
        return $tpl;
    }

    /**
     * 模块赋值
     * @author Wally
     * @since  2017-09-23
     * @param  [type]     $key [description]
     * @param  [type]     $val [description]
     * @return [type]          [description]
     */
    protected function send($key, $val)
    {
        $this->tpl->assign($key, $val);
    }

    /**
     * 显示模板
     * @author Wally
     * @since  2017-09-23
     * @param  string     $html [description]
     * @return [type]           [description]
     */
    protected function show($html = '')
    {
        // 网页字符编码
        if($this->config['HTTP_CONTENT_TYPE']) header('Content-Type:' . $this->config['HTTP_CONTENT_TYPE'] . '; charset=' . $this->config['HTTP_CHARSET']);
        if($this->config['HTTP_CACHE_CONTROL']) header('Cache-control: ' . $this->config['HTTP_CACHE_CONTROL']);  // 页面缓存控制
        if($this->config['HTTP_X_POWERED_BY']) header('X-Powered-By:' . $this->config['HTTP_X_POWERED_BY']);   //
        // 模块文件定义
        $tpl_prefix = ucfirst(CONTROLLER_NAME) . '_';   // 模板前缀
        $tpl_suffix = $this->config['TPL_SUFFIX'] ? $this->config['TPL_SUFFIX'] : '.html';   // 模板后缀
        // 定义模板
        if(empty($html)) {
            $html = $tpl_prefix . ACTION_NAME . $tpl_suffix;
        }
        // pt($html);
        $this->tpl->display($html);
    }
}