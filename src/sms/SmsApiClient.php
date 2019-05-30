<?php
namespace monda\sms;


require('function.php');
require('rsa/RSACrypt.php');
require('rsa/SignUtil.php');


class SmsApiClient
{

    private  $platId;

    private  $token;

    private  $url;


    public function __construct(int $platId, string $token, string $url) {
        $this->platId = $platId;
        $this->token = $token;
        $this->url = $url;
    }

    /**
     * 发送api调用请求
     * @param $uri
     * @param $params
     * @return string
     */
    public  function send($uri, $params) {
        $url = $this->url . $uri;
        $params['platId'] = $this->platId;
        $params['token'] = $this->token;
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
