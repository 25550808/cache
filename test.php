<?php
require 'lib/cache.class.php';

//����redis����
cache::getinst('redis')->set('name','tom');
$get = cache::getinst('redis')->get('name');
var_dump($get);






