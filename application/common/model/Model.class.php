<?php
/**
 * 模型根类
 */

class Model extends App
{
    public function __construct()
    {
        parent::__construct();
        // 加载Mysql
        $this->mysql = self::loadMysql();

        // p('Model');
    }

    /**
     * 加载MySQL类
     * @author Wally
     * @since  2017-09-23
     * @return [type]     [description]
     */
    private function loadMysql()
    {
        if($this->config['MYSQL_STATUS']) {
            // 引入模型基类
            $class_name = 'KfzMysql';
            $mysql_file = MASTER_PATH . '/' . $class_name . '.class.php';
            if(file_exists($mysql_file)) {
                require_once $mysql_file;
                // 判断类是否存在
                if(class_exists($class_name)) {
                    return new $class_name($this->config['MYSQL']['MASTER']);
                }
            }
        }
        return false;
    }


}