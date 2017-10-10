<?php
/**
 * 子类
 */

class ChatController extends MobileController
{
    public function __construct()
    {
        parent::__construct();

        // p('ChatController');

    }

    public function wall()
    {
        pt($_GET);

        // $f = MODULE_PATH . '/' . MODULE_NAME . '/controller/IndexController.class.php';
        // pt($f);
        // $ob = new IndexController();
        // $ob->index();

        // abc();
        // p('wall');
        // pt($this->model);
        // $this->model->test();

        // $this->redis->delete('name');
        // $this->redis->set('name', 'zhangdi321', 100);
        // pt($this->redis->get('name'));

        // pt($this->tpl);

        // $this->send('title', '1111111');
        // $this->send('content', 'this is test content');
        // $this->show();


    }

    public function __destruct()
    {



    }

}