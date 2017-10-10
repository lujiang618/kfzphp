<?php
/**
 * 控制器基类
 */

class BaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // 加载模型类
        $this->model = self::loadModel();

        // p('BaseController');
    }

    /**
     * 加载模块模型
     * @author Wally
     * @since  2017-09-23
     * @return [type]     [模型对象只有加载成功才会正常返回，否则返回false]
     */
    private function loadModel()
    {
        // 模型名称
        $model_name = ucfirst(CONTROLLER_NAME) . 'Model';
        // 创建控制器对应的模型对象
        if(class_exists($model_name)) {
            return new $model_name();
        }
        return false;
    }



}