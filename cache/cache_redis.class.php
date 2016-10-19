<?php
/**
 * redis的实现模式
 */
require 'cache_abstract.class.php';
class cache_redis extends cache_abstract {
	/**
	 * 定义memcache的实例化句柄
	 * @var resource
	 */
	private $conn;  
	
	/**
     * 构造生成cache实例
     * @param string $params ip:port端口数组
     */
	public function __construct($params) {
		$this->conn = new Redis();
		$this->conn->connect($params['host'], $params['port']);
		if (isset($params['dbuse'])) {
			$this->conn->select($params['dbuse']);
		}
	}
	
	/**
	 * 获取连接的实例信息
	 */
	public function instance() {
		return $this->conn;
	}
	
	/**
	 * 请求完成之后资源回收的处理
	 */
	public function __destruct() {
		unset($this->conn);
	}
	
	/**
	 * 写数据到cache当中
	 */
    public function set($key, $value, $ttl=0) {
    	if (!is_numeric($value)) {
    		$value = serialize($value);
    	}
    	if ($ttl > 0) {
    		return $this->conn->setex($key, $ttl, $value);
    	} else {
    		return $this->conn->set($key, $value);
    	}
    }
	
    /**
     * 从cache中获取数据
     */
    public function get($key) {
    	$value = $this->conn->get($key);
    	if (!is_numeric($value)) {
    		$value = unserialize($value);
    	}
        return $value;
    }
	
    /**
     * 删除cache中的记录
     */
    public function delete($key) {
    	return $this->conn->delete($key);
    }
    
    /**
     * 原子性操作数值减少1 返回最终数值
     */
    public function decrement($key, $value=1) {
    	return $this->conn->decrBy($key, $value); 
    }
    
    /**
     * 原子性操作数值累加1 返回最终数值
     */
    public function increment($key, $value=1) {
    	return $this->conn->incrBy($key, $value); 
    }
	
    /**
     * 情况cache中的部分或者全部信息
     */
    public function flush($path = '') {
    	return $this->conn->flushDB();
    }
    
	/**
	 * 获取分部署cache的服务状态
	 */
    public function status() {
    	$status = $this->conn->info();
        return $status;
    }
}