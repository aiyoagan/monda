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
    private $interface;
    private $version;
    private $method;
    private $params;

    /**
     * @return mixed
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host) {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port) {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getInterface() {
        return $this->interface;
    }

    /**
     * @param mixed $interface
     */
    public function setInterface($interface) {
        $this->interface = $interface;
    }

    /**
     * @return mixed
     */
    public function getVersion() {
        return $this->version;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * @return mixed
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams(array $params) {
        $this->params = $params;
    }


    /**
     * @return bool
     * @throws \Exception
     * 远程调用
     */
    public function call() {
        if (!empty($this->interface) || empty($this->version) || empty($this->version) || empty($this->host) || empty($this->port)) {
            throw new \Exception("rpc配置出错");
        }
        $fp = stream_socket_client("tcp://{$this->host}:{$this->port}", $errno, $errstr);
        if (!$fp) {
            throw new \Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
        }
        $data = [
            'interface' => $this->interface,
            'version' => $this->version,
            'method' => $this->method,
            'params' => $this->params,
            'logid' => uniqid(),
            'spanid' => 0,
        ];
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);
        fwrite($fp, $data);
        $result = fread($fp, 1024);
        fclose($fp);
        $result = json_decode($result, 1);
        if (isset($result['status']) && isset($result['data']) && $result['status'] == 200) {
            return $result['data'];
        }
        //记录日志
        return false;
    }
}