<?php
abstract class cache_abstract {
    abstract public function set($key, $var, $expire = 0);
    
    abstract public function get($key);
    
    abstract public function flush($path = '');
    
    abstract public function delete($key);
    
    abstract public function decrement($key, $value=1);
    
    abstract public function increment($key, $value=1);
}