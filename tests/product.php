<?php
/**
 * Created by PhpStorm.
 * User: wong
 * Date: 2018/11/5
 * Time: 下午3:16
 */

set_time_limit(0);

require "../vendor/autoload.php";
require_once "config.php";

$exchange_name = 'class-e-2';
$queue_name = 'class-q-2' . date('Y-m-d H:i:s') .rand(10,99);
$route_key = 'class-r-2' . date('Y-m-d H:i:s') .rand(10,99);
$ra = new \MondaMQ\MondaMQCommand($configs,$exchange_name,$queue_name,$route_key);
//$ra -> setDurable(false);

for($i=0;$i<=10;$i++){
    $ra->send('hello world--' . date('Y-m-d H:i:s',time()));
}