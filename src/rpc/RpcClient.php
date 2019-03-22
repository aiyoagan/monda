<?php
/**
 * rpc客户端
 * Created by PhpStorm.
 * User: chenzf
 * Date: 2019/3/11
 * Time: 上午10:05
 */

namespace MondaMQ\rpc;


class RpcClient {
    private $host;
    private $port;


    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param string $interface
     * @param string $version
     * @param string $method
     * @param array $params
     * @return bool|string
     * @throws \Exception
     * 远程调用
     */
    public function call(string $interface, string $version, string $method, array $params = []) {
        $fp = stream_socket_client("tcp://{$this->host}:{$this->port}", $errno, $errstr);
        if (!$fp) {
            throw new \Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
        }
        $data = [
            'interface' => $interface,
            'version' => $version,
            'method' => $method,
            'params' => $params,
            'logid' => uniqid(),
            'spanid' => 0,
        ];
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        fwrite($fp, $data);
        $result = fread($fp, 1024);
        fclose($fp);
         $result = json_decode($result,1);
        if(isset($result['status']) && isset($result['data']) && $result['status'] == 200){
            return $result['data'];
        }
        //记录日志
        return false;
    }
}