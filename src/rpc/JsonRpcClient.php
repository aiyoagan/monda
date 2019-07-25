<?php
/**
 * rpc客户端
 * Created by PhpStorm.
 * User: chenzf
 * Date: 2019/3/11
 * Time: 上午10:05
 */

namespace monda\rpc;

/**
 * Class JsonRpcClient
 * @package monda\rpc
 * @since 2.0 返回jsonrpc协议
 */
class JsonRpcClient {
    protected $rpcEol = "\r\n\r\n";
    private $host;
    private $port;
    private $interface;
    private $version;
    private $method;
    private $params;
    private $extra = [];

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
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @param mixed $extra
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function call() {
        if (empty($this->interface) || empty($this->version) || empty($this->host) || empty($this->port)) {
            throw new \Exception("rpc配置出错");
        }
        $host = $this->host.":".$this->port;
        $version = $this->version;
        $class = $this->interface;
        $method = $this->method;
        $param = $this->params;
        $ext = $this->extra;

        $fp = stream_socket_client($host, $errno, $errstr);
        if (!$fp) {
            throw new \Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
        }
        $req = [
            "jsonrpc" => '2.0',
            "method" => sprintf("%s::%s::%s", $version, $class, $method),
            'params' => $param,
            'id' => uniqid(),
            'ext' => $ext,
        ];
        $data = json_encode($req) . $this->rpcEol;
        fwrite($fp, $data);
        $result = '';
        while (!feof($fp)) {
            $tmp = stream_socket_recvfrom($fp, 1024);
            if ($pos = strpos($tmp, $this->rpcEol)) {
                $result .= substr($tmp, 0, $pos);
                break;
            } else {
                $result .= $tmp;
            }
        }
        fclose($fp);
        $resultArr = json_decode($result, true);
        if (isset($resultArr['result'])) {
            return $resultArr['result'];
        }
        return false;
    }
}