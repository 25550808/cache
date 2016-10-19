<?php
/**
 * Cache规则配置
 */
return array(
	'cache'		=> array(
		'file'  => 'db/',
		'memcached'  => array(
			array(
				'host'   => '127.0.0.1',
				'port'   => 11211,
				'weight' => 100
			)
		),
		'redis' => array(
			'host' => '127.0.0.1', 
			'port' => 6379,
			'dbuse'=> 4
		),
		'dev'	=> '/'
	),
	'uscache'   => 'memcached'
);
