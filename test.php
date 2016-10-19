<?php
require 'lib/cache.class.php';

//µ÷ÓÃredis»º´æ
cache::getinst('redis')->set('name','tom');
$get = cache::getinst('redis')->get('name');
var_dump($get);






