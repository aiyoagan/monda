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


$exchangeName = 'md-e-fanout-1';
$queueName = 'md-q-fanout-1';
$routeKey = 'md-r-fanout-1';
$type = 'fanout';
if (isset($argv[1])) {
    switch ($argv[1]) {
        case 1:
            $type = 'direct';
            $exchangeName = 'md-e-direct-1';
            $queueName = 'md-q-direct-1';
            $routeKey = 'md-r-direct-1';
            break;
        case 2:
            $type = 'topic';
            $exchangeName = 'md-e-topic-1';
            $queueName = 'md-q-topic-1';
            $routeKey = '*.logs';
            break;
    }
}
//echo $exchangeName . "\r\n" . $queueName . "\r\n" . $routeKey . "\r\n" . $type;
$ra = new \monda\mq\MondaMQCommand($configs, $exchangeName, $queueName, $routeKey);
$ra->setType($type);
//$ra -> setDurable(false);//设置是否持久化

for ($i=0; $i<=10; $i++) {
    $ra->send('hello world--' . date('Y-m-d H:i:s',time()));
}