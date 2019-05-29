<?php
namespace monda\utils;


require('function.php');
require('rsa/RSACrypt.php');
require('rsa/SignUtil.php');


class SmsApiClient
{

    private static $platId;

    private static $token;

    private static $url;


    public function __construct(int $platId, string $token, string $url) {
        self::$platId = $platId;
        self::$token = $token;
        self::$url = $url;
    }

    /**
     * 发送api调用请求
     * @param $uri
     * @param $params
     * @return string
     */
    public static function send($uri, $params) {
        $url = self::$url . $uri;
        $params['platId'] = self::$platId;
        $params['token'] = self::$token;
        $params['_timer'] = time();
        $sign = \SignUtil::sign($url, $params);
        $rsa = new \RSACrypt();
        $params['__sign'] = $rsa->encryptByPublicKey($sign);
        try {
            $result = post($url, $params);
        } catch (\Exception $e) {
           return  ajaxResult('002', $e);
        }
        if (!is_json($result)) {
            return  ajaxResult('002', 'APIReturnNotJsonException:'.$result);
        }
        $result = json_decode($result, true);
        return ajaxResult($result['code'], $result['message'], $result['data']);
    }
}
