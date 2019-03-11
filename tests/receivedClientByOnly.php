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

$exchangeName = 'md-e-direct-1';
$queueName = 'md-q-direct-1';
$routeKey = 'md-r-direct-1';
$type = 'direct';
//echo $exchangeName . "\r\n" . $queueName . "\r\n" . $routeKey . "\r\n" . $type;

$ra = new \monda\mq\MondaMQCommand($configs, $exchangeName, $queueName, $routeKey);
$ra->setType($type);
//$ra -> setDurable(false);//设置是否持久化

//业务处理
$callback = function($msg) {
    echo " [x] Received only-", $msg->body, "\n";
    $path = dirname(__FILE__) . "/logs/only_log{$msg->delivery_info['delivery_tag']}.log";
    file_put_contents($path, $msg->body.'|' . $msg->delivery_info['delivery_tag'] . '' ."\r\n",FILE_APPEND);
    echo " [x] Done", "\n";
    //如果noAck为false，则需要执行下面的手动应答
    //$msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

//执行消费者监听，noAck默认true【即自动应答，看项目需求传参】
$s = $ra->run($callback,true);