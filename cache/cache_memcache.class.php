<?php
/**
 * 分布式memcache的实现模式
 */
require 'cache_abstract.class.php';
class cache_memcache extends cache_abstract {
	/**
	 * 定义memcache的实例化句柄
	 * @var resource
	 */
	private $conn;  
	
	/**
     * 构造生成cache实例
     */
	public function __construct($params) {
		$this->conn = new Memcache();
		foreach ($params as &$item) {
			$this->conn->addServer($item['host'], $item['port']);
		}
	}
	
	/**
	 * 请求完成之后资源回收的处理
	 */
	public function __destruct() {
		$this->conn->close();
	}
	
	/**
	 * 写数据到cache当中
	 */
    public function set($key, $value, $ttl=0) {
    	return $this->conn->set($key, $value, MEMCACHE_COMPRESSED, $ttl);
    }
	
    /**
     * 从cache中获取数据
     */
    public function get($key) {
    	$data  = $this->conn->get($key);
        return $data;
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
    	return $this->conn->decrement($key, $value); 
    }
    
    /**
     * 原子性操作数值累加1 返回最终数值
     */
    public function increment($key, $value=1) {
    	return $this->conn->increment($key, $value); 
    }
	
    /**
     * 清空cache中的部分或者全部信息
     */
    public function flush($path = '') {
    	if (empty($path)) {
        	return $this->conn->flush();
    	} else {
	    	$data = $this->conn->getExtendedStats('items');
	    	foreach ($data as &$items) {
	    		foreach ($items['items'] as $number=>&$item) {
	    			$list = $this->conn->getExtendedStats('cachedump', $number, 0);
	    			foreach ($list as &$kitems) {
	    				foreach ($kitems as $key=>&$kitem) {
	    					$nlen = strlen($path);
	    					$klen = strlen($key);
	    					if ($klen > $nlen && strncmp($path, $key, $nlen) == 0) {
	    						$this->conn->delete($key);
	    					}
	    				}
	    			}
	    		}
	    	}
    	}
    }
    
	/**
	 * 获取分部署cache的服务状态
	 */
    public function status() {
    	$info= $this->conn->getStats();
        return $info;
    }
}