<?php
/**
 * MYSQL PDO类
 */
class KfzMysql
{
    // private $dbh = '';   // PDO对象
    // private $sth = '';   // PDOStatement对象
    // private $debug = '';   // 是否是dubug模式

    /**
     * 构造函数
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $conf [数据库配置参数]
     */
    public function __construct($conf)
    {
        if (!class_exists('PDO')){
            throw new Exception('not found PDO');
            return false;
        }
        $this->debug = $conf['DB_DEBUG'];
        $this->prefix = !empty($conf['DB_PREFIX']) ? $conf['DB_PREFIX'] : ''; // 表前缀
        try{
            $this->dbh = new PDO(
                $conf['DB_TYPE'] . ':dbname=' . $conf['DB_NAME'] . ';host=' . $conf['DB_HOST'] . ';port='.$conf['DB_PORT'],
                $conf['DB_USER'],
                $conf['DB_PASS'],
                array(
                    // 并发访问不大的网站上使用数据库永久连接
                    PDO::ATTR_PERSISTENT => $conf['DB_PCONNECT'],
                    PDO::MYSQL_ATTR_INIT_COMMAND => "set names '".$conf['DB_CHARSET']."';"
                )
            );
            if($this->debug){
                error_reporting(E_ALL ^ E_NOTICE);
                // 开启pdo的抛出异常模式
                $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }else{
                error_reporting(NULL);
                ini_set('display_errors','Off');
            }
        } catch (PDOException $e){
            echo 'Connection failed: ' . $e->getMessage();
            exit;
        }
    }

    /**
     * 真实表名
     * @author Wally
     * @since  2017-09-23
     * @param  string     $tablename [description]
     * @return [type]                [description]
     */
    public function table($tablename = '')
    {
        return !empty($tablename) ? $this->prefix . $tablename : false;
    }

    /**
     * 获取所有记录
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  boolean    $show [是否显示sql语句]
     * @return [type]           [所有记录数组]
     */
    public function getAll($sql, $show = false)
    {
        // 预处理
        $this->prepare($sql, $show);
        // 返回结果集
        $this->sth->setFetchMode(PDO::FETCH_ASSOC);
        return $this->sth->fetchAll();
    }

    /**
     * 获取单行记录
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  boolean    $show [是否显示sql语句]
     * @return [type]           [一条记录]
     */
    public function getRow($sql, $show = false)
    {
        // 预处理
        $this->prepare($sql, $show);
        // 返回结果集
        $this->sth->setFetchMode(PDO::FETCH_ASSOC);
        return $this->sth->fetch();
    }

    /**
     * 获取某栏位的所有值
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  boolean    $show [是否显示sql语句]
     * @return [type]           [某列的一维数组]
     */
    public function getCol($sql, $show = false)
    {
        // 预处理
        $this->prepare($sql, $show);
        // 返回结果集
        $this->sth->setFetchMode(PDO::FETCH_NUM);
        $res = $this->sth->fetchAll();
        if ($res !== false) {
            $arr = array();
            foreach ($res as $k => $v) {
                $arr[] = $v[0];
            }
            return $arr;
        } else {
            return false;
        }
    }

    /**
     * 获取单个值
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  boolean    $show [是否显示sql语句]
     * @return [type]           [某一个值]
     */
    public function getOne($sql, $show = false)
    {
        // 预处理
        $this->prepare($sql, $show);
        // 返回结果集
        return $this->sth->fetchColumn();
    }

    /**
     * 新增数据
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $table [表名]
     * @param  array      $data  [新增的数据数组]
     * @param  boolean    $show  [是否显示sql语句]
     * @return [type]            [新增的列ID]
     */
    public function insert($table, $data = array(), $show = false)
    {
        if(empty($table)) {
            exit('table name empty');
        }
        if(empty($data)) {
            exit('data empty');
        }
        // 判断插入一条还是多条
        if (count($data) == count($data, 1)) {
            // 一维数组
            $fields = $values = '';
            foreach ($data as $k => $v) {
                $fields[] = $k;
                $values[] = $this->process($v);
            }
            $sql = 'insert into ' . $table . ' (' . implode(',', $fields) . ') values ("' . implode('","', $values) . '")';
        } else {
            // 多维数组
            $array = array();
            foreach ($data as $key => $val) {
                $values = array();
                foreach ($val as $k => $v) {
                    // 获取字段名
                    if($key == 0) {
                        $fields[] = $k;
                    }
                    // 对值的处理
                    $values[] = $this->process($v);
                }
                // $this->pt($values);
                $array[] = '("' . implode('","', $values) . '")';
            }
            $sql = 'insert into ' . $table . ' (' . implode(',', array_unique($fields)) . ') values ' . implode(',', $array);
            // $this->pt($sql);
        }
        // 预处理
        $res = $this->prepare($sql, $show);
        if($res) {
            return $this->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * 更新数据
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $table [表名]
     * @param  [type]     $save  [更新的数据数组]
     * @param  [type]     $where [更新条件]
     * @param  boolean    $show  [是否显示sql语句]
     * @return [type]            [true成功 false失败]
     */
    public function update($table, $save, $where, $show = false)
    {
        if(empty($table)) {
            exit('table empty');
        }
        if(empty($save)) {
            exit('save empty');
        }
        if(empty($where)) {
            exit('where empty');
        }
        // 拼接sql语句
        $fields = array();
        foreach ($save as $k => $v) {
            $fields[] = $k . ' = "' . $this->process($v) . '"';
        }
        // 整合sql语句
        $sql = 'update ' . $table . ' set ' . implode(',', $fields) . ' where ' . $where;
        // 预处理
        $res = $this->prepare($sql, $show);
        if($res) {
            return $this->sth->rowCount();
        } else {
            return false;
        }
    }

    /**
     * 更新自增数
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $table  [表名]
     * @param  [type]     $field  [字段名]
     * @param  [type]     $number [自增数]
     * @param  [type]     $where  [更新条件]
     * @param  boolean    $show   [是否显示sql]
     * @return [type]             [true成功 false失败]
     */
    public function increase($table, $field, $number, $where, $show = false)
    {
        if(empty($where)) {
            exit('where empty');
        }
        $sql = 'update ' . $table . ' set ' . $field . ' = ' . $field . ' + ' . $number . ' where ' . $where;
        // 预处理
        $res = $this->prepare($sql, $show);
        if($res) {
            return $this->sth->rowCount();
        } else {
            return false;
        }
    }

    /**
     * 更新自减数
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $table  [表名]
     * @param  [type]     $field  [字段名]
     * @param  [type]     $number [自增数]
     * @param  [type]     $where  [更新条件]
     * @param  boolean    $show   [是否显示sql]
     * @return [type]             [true成功 false失败]
     */
    public function decrease($table, $field, $number, $where, $show = false)
    {
        if(empty($where)) {
            exit('where empty');
        }
        $sql = 'update ' . $table . ' set ' . $field . ' = ' . $field . ' - ' . $number . ' where ' . $where;
        // 预处理
        $res = $this->prepare($sql, $show);
        if($res) {
            return $this->sth->rowCount();
        } else {
            return false;
        }
    }

    /**
     * 删除数据
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  boolean    $show [是否显示sql语句]
     * @return [type]           [true成功 false失败]
     */
    public function delete($sql, $show = false)
    {
        // 预处理
        $res = $this->prepare($sql, $show);
        if($res) {
            return $this->sth->rowCount();
        } else {
            return false;
        }
    }

    /**
     * 执行sql语句
     * 只针对于select/insert/update/delete
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  boolean    $show [是否显示sql语句]
     * @return [type]           [按不同sql返回相应结果]
     */
    public function query($sql, $show = false)
    {
        // 预处理
        $res = $this->prepare($sql, $show);
        if(strpos(trim($sql), "select") !== false) {
            $this->sth->setFetchMode(PDO::FETCH_ASSOC);
            if(strpos(trim($sql), "limit 1") !== false) {
                // 查询一条
                return $this->sth->fetch();
            } else {
                // 查询所有
                return $this->sth->fetchAll();
            }
        } else {
            if($res) {
                if(strpos(trim($sql), "insert") !== false) {
                    // 新增
                    return $this->lastInsertId();
                } else {
                    // 修改或删除
                    return $this->sth->rowCount();
                }
            } else {
                return false;
            }
        }
    }

    /**
     * 获取最后插入行的ID
     * @author Wally
     * @since  2017-08-29
     * @return [type]     [description]
     */
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * 开始一个事务
     * @author Wally
     * @since  2017-08-28
     * @param  [type]     $callback [description]
     * @return [type]               [description]
     */
    public function transaction($callback)
    {
        try {
            // 开始一个事务，关闭自动提交
            $this->dbh->beginTransaction();
            $callback($this);
            $this->dbh->commit();
        } catch (PDOException $e) {
            echo $e->getMessage();
            // 识别出错误并回滚更改
            $this->dbh->rollBack();
            if($this->debug) {
                throw new PDOException($e->getMessage());
            }
        }
    }

    /**
     *  执行一条 SQL 语句，并返回受影响的行数
     *  如果没有受影响的行，则 PDO::exec() 返回 0
     * @author Wally
     * @since  2017-08-28
     * @param  [type]     $sql [SQL 语句]
     * @return [type]          [description]
     */
    public function exec($sql)
    {
        return $this->dbh->exec($sql);
    }

    /**
     * 抛出异常
     * @author Wally
     * @since  2017-08-29
     * @param  [type]     $msg [description]
     * @return [type]          [description]
     */
    public function rollback($msg)
    {
        throw new PDOException($msg);
    }

    /**
     * 预处理
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  [type]     $show [是否显示sql语句]
     * @return [type]           [true成功 false失败]
     */
    private function prepare($sql, $show)
    {
        if(empty($sql)) {
            exit('sql empty');
        }
        // 是否显示sql语句
        $this->showSql($sql, $show);
        // 准备要执行的SQL语句
        $this->sth = $this->dbh->prepare($sql);
        if (!$this->sth) {
            echo "\nPDO::errorInfo():\n";
            print_r($this->dbh->errorInfo());
            exit;
        }
        // 执行一条预处理语句
        return $this->sth->execute();
    }

    /**
     * 销毁变量
     * @author Wally
     * @since  2017-08-25
     */
    private function destroy()
    {
        unset($this->dbh);
        unset($this->sth);
    }

    /**
     * 判断是否显示sql语句
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $sql  [sql语句]
     * @param  [type]     $show [是否显示]
     * @return [type]           [打印结果]
     */
    private function showSql($sql, $show)
    {
        if($show) {
            $this->pt($sql);
        }
    }

    /**
     * 过滤字符
     * @author Wally
     * @since  2017-08-25
     * @param  [type]     $item [过滤的字符串]
     * @return [type]           [处理后字符串]
     */
    private function process($item)
    {
        $item = trim($item);
        if(!get_magic_quotes_gpc()) $item = addslashes($item);
        return $item;
    }

    /**
     * 打印调试(不中断)
     * @author Wally
     * @since  2017-08-24
     */
    private function p($v)
    {
        echo "<pre>";
        if(is_string($v) || is_int($v)) {
            echo $v;
        } else if(is_object($v) || is_bool($v)) {
            var_dump($v);
        } else {
            print_r($v);
        }
    }

    /**
     * 打印调试(中断)
     * @author Wally
     * @since  2017-08-24
     */
    private function pt($v)
    {
        $this->p($v);
        exit;
    }
}
