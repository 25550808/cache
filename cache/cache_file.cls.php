<?php
/**
 * 文件cache的实现模式
 */
class cache_file extends cache_abstract {
	
	/**
	 * 定义文件cache存储路径
	 * @var string
	 */
    private $path = './';
    
    /**
     * 构造生成cache实例
     * @param string $path 存放路径
     */
    public function __construct($path='') {
    	$path  = ($path == '')? $path:'db/';
    	$share = config::load('share');
    	$this->path = $share['dir']['cache'].$path; 
    }
    
    /**
     * 设置cache缓存数据
     * @param string $key
     * @param mix    $var
     * @param int	 $expire
     * 
     * @return boolean
     */
    public function set($key, $var, $expire = 0) {
        $fp = fopen($this->make_hash($key, true), 'w');
        if ($expire != 0) {
            $expire = time() + $expire;
        } 
        $value = array('timeout' => $expire);
        $type  = gettype($var);
        if ($type == 'object' || $type == 'resource' || $type == 'unknown type') {
            $value['serialize'] = serialize($var);
        } else {
            $value['var'] 		= $var;
        }
        $result = fwrite($fp, '<?php return ' . var_export($value, true) . ';?>');
        fclose($fp);
        return $result;
    }
    
    /**
     * 获取指定Key对应的Cache数据
     * @param string $key
     * @return mix
     */
    public function get($key) {
        $result = false;
        $file   = $this->make_hash($key);
        if (is_file($file)) {
            $value = include($file);
            if ($value['timeout'] == 0 || time() <= $value['timeout']) {
            	if (isset($value['var'])) {
            		$result = $value['var'];
            	} else if (isset($value['serialize'])) {
            		$result = unserialize($value['serialize']);
            	} else {
            		$result = null;
            	}
            } else {
				unlink($file);
			}
        }
        return $result;
    }
    
    /**
     * 删除指定Key对应的Cache数据
     * @param string $key
     * @return boolean
     */
	public function delete($key) {
        $file = $this->make_hash($key);
        return is_file($file)? unlink($file):false;
    }
    
    /**
     * 原子性操作数值减少1 返回最终数值
     */
    public function decrement($key, $value=1) {
    	$result = false;
        $file   = $this->make_hash($key);
        if (is_file($file)) {
            $svalue = include($file);
            $svalue['var'] += $value;
            $result         = $svalue['var'];
            file_put_contents($file, '<?php return ' . var_export($svalue, true) . ';?>');
        }
        return $result;
    }
    
    /**
     * 原子性操作数值累加1 返回最终数值
     */
    public function increment($key, $value=1) {
    	$result = false;
        $file   = $this->make_hash($key);
        if (is_file($file)) {
            $svalue = include($file);
            $svalue['var'] += 1;
            $result         = $svalue['var'];
            file_put_contents($file, '<?php return ' . var_export($svalue, true) . ';?>');
        }
        return $result;
    }
    
    /**
     * 清理目录下的Cache文件
     * @param string $path
     */
    public function flush($path = '') {
        if (!is_dir($path)) {
        	$path = $this->path.$path;
        	if (!is_dir($path)) {
        		return;
        	}
        }
        if (strncmp($path, $this->path, strlen($this->path)) !== 0) {
        	return;
        }
        $hdp = dir($path);
        while ($file = $hdp->read()) {
        	if($file == '.svn' || $file == '.' || $file == '..') {
        		continue;
        	}
			$file = $path . '/' . $file;
			if (is_file($file)) {
				unlink($file);
			} else {
				$this->flush($file);
				rmdir($file);
			}
        }
        $hdp->close();
    }
    
    /**
     * 根据Key值生成文件访问路径
     * @param string $key
     * 
     * @return string $file
     */
    private function make_hash($key, $makedir=false) {
    	$key    = str_replace('-', '/', $key);
    	$md5str = md5($key);
    	if (($npos = strpos($key, '/')) !== false) {
    		$key  = sprintf('%s/%s/%s/%s', substr($key, 0, $npos), 
    			substr($md5str, 0, 3), substr($md5str, 3, 3), $md5str);
    	} else {
    		$key  = sprintf('%s/%s/%s/%s', substr($md5str, 0, 3),
    		 	substr($md5str, 3, 3), substr($md5str, 6, 3), $md5str);
    	}
        $pos  = strrpos($key, '/');
        $path = $this->path;
        if ($pos !== false) {
            $path .= substr($key, 0, $pos);
            $key   = substr($key, $pos + 1);
        }
        if ($makedir && !is_dir($path)) {
            mkdir($path, 0755, true);
        }
        return $path . '/' . urlencode($key) . '.cache.php';
    }
}