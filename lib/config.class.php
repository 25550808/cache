<?php
/**
 * 配置加载类
 */
class config {
	/**
	 * 动态加载其他的配置
	 * @param  string  $sfilefix
	 * @param  boolean $global
	 * @return mix 
	 */
	public static function load($sfilefix, $global=true, $setting='dev') {
		static $loader = array();
		if (!isset($loader[$sfilefix])) {
			if ($global) {
				$sfile  = sprintf('../inc/%s.inc.php', $sfilefix);
			} else {
				$sfile  = sprintf('../inc/%s/%s.inc.php', $setting, $sfilefix);
			}
			$loader[$sfilefix] = require $sfile;
		}
		return $loader[$sfilefix];
	}
}