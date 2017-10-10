<?php
/**
 * 子类模型
 */

class ChatModel extends MobileModel
{
    public function __construct()
    {
        parent::__construct();

        // p('ChatModel');
    }

    public function test()
    {
        $sql = 'select age from ' . $this->mysql->table('user') . ' where 1';
        $res = $this->mysql->getCol($sql);
        pt($res);

        // p(111);
        $name = $this->redis->get('name');
        pt($name);

        // pt($this->config);
    }

}