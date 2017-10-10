<?php
/**
 * 子类
 */

class TestController extends MobileController
{
    public function __construct()
    {
        parent::__construct();

        // p('TestController');

    }

    public function lang()
    {
        p("<h2>我的后台语言？</h2>");
        // pt($_GET);

    }

    public function __destruct()
    {
        // p('__destruct');

    }

}