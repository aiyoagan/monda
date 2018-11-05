<?php
/**
 * Created by PhpStorm.
 * User: wong
 * Date: 2018/11/5
 * Time: 下午3:16
 */

error_reporting(E_ALL);

require "../vendor/autoload.php";
require_once "config.php";

$exchange_name = 'class-e-2';
$queue_name = 'class-q-' . date('Y-m-d H:i:s') .rand(10,99);
$route_key = 'class-r-2' . date('Y-m-d H:i:s') .rand(10,99);
$ra = new \MondaMQ\MondaMQCommand($configs,$exchange_name,$queue_name,$route_key);
//$ra -> setDurable(false);

//业务处理
$callback = function($msg) {
    echo " [x] Received ", $msg->body, "\n";
    $path = dirname(__FILE__) . "/logs/log{$msg->delivery_info['delivery_tag']}.log";
    file_put_contents($path, $msg->body.'|' . $msg->delivery_info['delivery_tag'] . '' ."\r\n",FILE_APPEND);
    echo " [x] Done", "\n";
    //如果noAck为false，则需要手动应答
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

//noAck
$s = $ra->run($callback,false);