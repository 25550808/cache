<?php
/**
 * 缓存工厂类模型
 */
require '../cache/cache_abstract.class.php';
require 'config.class.php';
class cache {
	/**
	 * 工厂函数获取Cache
	 * @param string $engin 需要获取的Cache引擎
	 * @return cache_abstract  缓存实例
	 */
	public static function getinst($engin='') {
		static $cache   = array();
		static $uscache = '';
		if (empty($engin) && !empty($uscache)) {
			$engin      = $uscache;
		}
		if (!isset($cache[$engin])) {
			$share = config::load('share');
			if (!isset($share['cache'])) {
				$share = config::load('cache', false);
			}
			$uscache = isset($share['uscache'])? $share['uscache']:'file';
			if (empty($engin)) {
				$engin   = $uscache;
			}
			$class = 'cache_'.$engin;
			require '../cache/'.$class.'.class.php';
			$cache[$engin] = new $class($share['cache'][$engin]);
		}
		return $cache[$engin];
	}
}
