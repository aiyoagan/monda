<?php
/**
 *
 * Created by PhpStorm.
 * User: mofuhao <mofh@pvc123.com>
 * Date: 19-5-29
 * Time: 上午10:19
 */

require('../src/sms/SmsApiClient.php');

//對應的平臺名稱
$platId = 1;
//从后台自动生成token
$token = '';
$url = 'http://utils.dasu123.my/';
$sms = new \monda\utils\SmsApiClient($platId, $token, $url);
$uri = 'api/sms/send';
$prames = [
    'platId' => $platId,
    'mobile' => '',//手机号码
    'content' => '验证码{$code},用于测试,有效期为十分钟'
];
$res = $sms->send($uri, $prames);
var_dump($res);
