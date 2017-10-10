<?php
/**
 * 应用程序根类
 */

class App
{
    public function __construct()
    {
        // 配置文件信息
        $this->config = C();
        // 加载Redis
        $this->redis = self::loadRedis();

        // p('App');
    }

    /**
     * 加载Redis类
     * @author Wally
     * @since  2017-09-23
     * @return [type]     [description]
     */
    private function loadRedis()
    {
        if($this->config['REDIS_STATUS']) {
            // 引入模型基类
            $class_name = 'KfzRedis';
            $redis_file = MASTER_PATH . '/' . $class_name . CLASS_EXT;
            if(file_exists($redis_file)) {
                require_once $redis_file;
                // 判断类是否存在
                if(class_exists($class_name)) {
                    return new $class_name($this->config['REDIS']);
                }
            }
        }
        return false;
    }

}