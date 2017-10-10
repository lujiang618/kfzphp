<?php
/**
 * Redis 公用类库
 * @author Wally
 * @since  2017-03-13
 */
class KfzRedis
{
    // private $redis = null;

    /**
     * 连接Redis
     * @author Wally
     * @since  2017-03-13
     * @param  string     $host [主机名]
     * @param  string     $post [端口号]
     */
    function __construct($conf = null)
    {
        // 判断参数
        if(empty($conf)) {
            exit('Redis config empty');
        }
        // 实例化
        $this->redis = new Redis();
        // 数据库连接
        $connect = $this->redis->connect($conf['DB_HOST'], $conf['DB_PORT']);
        if(!$connect) {
            exit('Redis connection fail');
        }
        // 密码验证
        if(!empty($conf['DB_AUTH'])) {
            $auths = $this->redis->auth($conf['DB_AUTH']);
            if(!$auths) {
                exit('Redis auth fail');
            }
        }
    }

    /**
     * 是否是JSON数据
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $val [description]
     * @return boolean         [description]
     */
    private function isJson($val)
    {
        json_decode($val);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * 写缓存
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key    [缓存键]
     * @param  [type]     $value  [缓存值]
     * @param  integer    $expire [过期时间，秒为单位，0:表示无过期时间]
     */
    public function set($key, $value, $expire = 0)
    {
        $val = is_array($value) ? json_encode($value) : $value;
        if(!empty($val)) {
            // 永不超时
            if($expire == 0){
                $ret = $this->redis->set($key, $val);
            }else{
                $ret = $this->redis->setex($key, $expire, $val);
            }
            return $ret;
        } else {
            return false;
        }
    }

    /**
     * 添加列表
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key   [缓存键]
     * @param  [type]     $value [缓存值]
     * @param  string     $type  [添加类型]
     * @return [type]            [0头部添加 1尾部添加]
     */
    public function push($key, $value, $type = 1)
    {
        if($type == 1) {
            return $this->redis->rpush($key, $value);
        } else {
            return $this->redis->lpush($key, $value);
        }
    }

    /**
     * 读缓存
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key [缓存KEY]
     * @return [type]          [成功true 失败false]
     */
    public function get($key)
    {
        if($this->exists($key)) {
            $ret = $this->redis->get($key);
            if($ret !== false) {
                if($this->isJson($ret)) {
                    return json_decode($ret, true);
                } else {
                    return $ret;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 删除缓存
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key [缓存KEY]
     * @return [type]          [成功true 失败false]
     */
    public function delete($key)
    {
        if($this->exists($key)) {
            return $this->redis->delete($key);
        } else {
            return true;
        }
    }

    /**
     * 设定一个key的活动时间（s）
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key    [缓存KEY]
     * @param  integer    $expire [过期时间，秒为单位]
     * @return [type]             [description]
     */
    public function expire($key, $expire = 3600)
    {
        if(!empty($key)) {
            return $this->redis->setTimeout($key, $expire);
        } else {
            return false;
        }
    }

    /**
     * key存活到一个unix时间戳时间
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key    [缓存KEY]
     * @param  integer    $expire [过期时间，秒为单位]
     * @return [type]             [description]
     */
    public function expireAt($key, $unixtime)
    {
        if(!empty($key) && !empty($unixtime)) {
            return $this->redis->expireAt($key, $unixtime);
        } else {
            return false;
        }
    }

    /**
     * 得到key的string的长度
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key [缓存KEY]
     * @return [type]          [长度值]
     */
    public function strlen($key)
    {
        return $this->redis->strlen($key);
    }

    /**
     * 判断key是否存在
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key [缓存KEY]
     * @return [type]          [存在 true 不存在false]
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * 实时返回 key 的剩余生存时间
     * 以秒为单位
     * 当 key 不存在时，返回 -2
     * 当 key 存在但没有设置剩余生存时间时，返回 -1
     * @author Wally
     * @since  2017-03-13
     * @param  [type]     $key [缓存KEY]
     * @return [type]          [返回剩余生存时间]
     */
    public function ttl($key)
    {
        return $this->redis->ttl($key);
    }

}
