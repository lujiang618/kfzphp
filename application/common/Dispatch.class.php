<?php
/**
 * URL解析、路由和调度
 */

class Dispatch
{
    /**
     * 初始化
     * @author Wally
     * @since  2017-09-26
     */
    public function __construct()
    {
        // 配置文件
        $this->config = $GLOBALS['_CFG'];

    }

    /**
     * url 路由解释
     * @author Wally
     * @since  2017-09-26
     * @return [type]     [description]
     */
    public function route()
    {
        $parse = parse_url(SITE_URL);
        // 判断服务器访问地址是否正确
        if(!empty($this->config['SERVER_HOST']) && ($this->config['SERVER_HOST'] != $parse['host']) && !APP_DEBUG) {
            message($this->config['WORD_DOMAIN_ERROR'], 'WARN');
        }
        // 获取控制器参数
        $path = $parse['path'];
        // url 分析
        if($path != '/') {
            // 去两加空格、左边斜杠
            $path = ltrim(trim($path), '/');
            // 伪静态扩展名不存在，则返回错误信息
            if(strpos($path, $this->config['URL_REWRITE_SUFFIX']) === false) {
                message($this->config['WORD_URL_ERROR'], 'ERROR');
            }
            // 返回路由和伪扩展
            $data = explode('.', $path);
            // 如果伪静态扩展名不存在或错误，则返回错误信息
            if(!isset($data[1]) || ($data[1] != ltrim($this->config['URL_REWRITE_SUFFIX'], '.'))) {
                message($this->config['WORD_URL_ERROR'], 'ERROR');
            }
            // 区分模块、控制器、方法
            $route = explode('/', $data[0]);
            // 没有模块的情况下
            if(count($route) == 3) {
                $module = $route[0];
                $controller = $route[1];
                $action = $route[2];
            } else if(count($route) == 2) {
                $module = $this->config['DEFAULT_MODULE'];
                $controller = $route[0];
                $action = $route[1];
            } else if(count($route) == 1) {
                $module = $this->config['DEFAULT_MODULE'];
                $controller = $this->config['DEFAULT_CONTROLLER'];
                $action = $route[0];
            } else {
                message($this->config['WORD_URL_ERROR'], 'ERROR');
            }
        } else {
            // 如果地址不存在跳入默认页面
            $module = $this->config['DEFAULT_MODULE'];
            $controller = $this->config['DEFAULT_CONTROLLER'];
            $action = $this->config['DEFAULT_ACTION'];
        }
        // 定义控制名
        define('MODULE_NAME', strtolower($module));         // 模块名称
        define('CONTROLLER_NAME', strtolower($controller));     // 控制器名称
        define('ACTION_NAME', strtolower($action));      // 方法名称
        // p(MODULE_NAME);p(CONTROLLER_NAME);pt(ACTION_NAME);

        // 模块是否存在
        $module_name = MODULE_PATH . '/' . MODULE_NAME;
        // pt($module_name);
        if(!is_dir($module_name)) {
            message($this->config['WORD_MODUEL_UNFOUND'], 'ERROR');
        }

        // 加载模块方法
        $module_function = FUNCTION_PATH . '/' . MODULE_NAME . '.php';
        if(file_exists($module_function)) {
            require $module_function;
        }
    }

    /**
     * 注册 autoload 方法
     * @author Wally
     * @since  2017-09-25
     * @return [type]     [description]
     */
    public function start()
    {
        spl_autoload_register('self::autoload');
        class_parents(ucfirst(CONTROLLER_NAME) . 'Controller', true);
    }

    /**
     * 自动加载所需控制器
     * @author Wally
     * @since  2017-09-25
     * @return [type]     [description]
     */
    private function autoload($class_name)
    {
        // 类名中带有 Controller 和 Model 的文件参与自动加载
        if((strpos($class_name, 'Controller') !== false) || (strpos($class_name, 'Model') !== false)) {
            // p('autoload -> ' . $class_name);
            $class_path = self::search($class_name);
            // 只有类文件存在的情况下
            if($class_path !== false) {
                // p('require -> ' . $class_path);
                // 加载类文件
                require $class_path;
                // 如果类名不存在
                if(!class_exists($class_name)) {
                    // 对控制器名不存在
                    if(strpos($class_name, 'Controller') !== false) {
                        message($this->config['WORD_CONTROLLER_CLASS_ERROR'] . '[' . $class_name . ']', 'ERROR');
                    }
                    // 对模型名不存在, 访问的方法对应的模型可以不存在
                    if((strpos($class_name, 'Model') !== false) && ($class_name != ucfirst(CONTROLLER_NAME) . 'Model')) {
                        message($this->config['WORD_MODEL_CLASS_ERROR'] . '[' . $class_name . ']', 'ERROR');
                    }
                }
            } else {
                // 对控制器不存在的判断
                if(strpos($class_name, 'Controller') !== false) {
                    message($this->config['WORD_CONTROLLER_FILE_ERROR'] . '[' . $class_name . ']', 'ERROR');
                }
                // 对模型不存在的判断
                if((strpos($class_name, 'Model') !== false) && ($class_name != ucfirst(CONTROLLER_NAME) . 'Model')) {
                    message($this->config['WORD_MODEL_FILE_ERROR'] . '[' . $class_name . ']', 'ERROR');
                }
            }
        }
    }

    /**
     * 获取控制器路径
     * @author Wally
     * @since  2017-09-28
     * @return [type]     [description]
     */
    private function search($class_name)
    {
        // 控制器和目录
        $class_dir = [
            'moduel_controller' => MODULE_PATH . '/' . MODULE_NAME . '/controller/',
            'common_controller' => COMMON_PATH . '/controller/',
            'moduel_model' => MODULE_PATH . '/' . MODULE_NAME . '/model/',
            'common_model' => COMMON_PATH . '/model/',
        ];
        // 控制器路径
        $class_file = $class_name . CLASS_EXT;
        // 循环判断文件位置
        foreach ($class_dir as $key => $dir) {
            $controller_path = $dir . $class_file;
            if(file_exists($controller_path)) {
                return $controller_path;
            }
        }
        return false;
    }

    /**
     * 运行应用
     * @author Wally
     * @since  2017-09-26
     * @return [type]     [description]
     */
    public function run()
    {
        $action = strtolower(ACTION_NAME);
        $controller_name = ucfirst(CONTROLLER_NAME) . 'Controller';
        if(!class_exists($controller_name)) {
            message($this->config['WORD_CONTROLLER_CLASS_ERROR'], 'ERROR');
        }
        $obj = new $controller_name;
        if(!method_exists($obj, $action)) {
            message($this->config['WORD_ACTION_UNFOUND'], 'ERROR');
        }
        $obj->$action();
    }
}
