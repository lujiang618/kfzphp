<?php
/**
 * 默认页
 */

class IndexController extends MobileController
{
    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {

        $this->send('title', '主页');
        $this->send('content', '欢迎回来!');
        $this->show();
    }

}