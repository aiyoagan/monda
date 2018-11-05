<?php
/**
 * Created by PhpStorm.
 * User: wong
 * Date: 2018/11/5
 * Time: 下午2:41
 */

namespace MondaMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;

class MondaMQCommand {

    public $configs = [];

    //交换机名称
    public $exchangeName = '';

    //队列名称
    public $queueName = '';

    //路由名称
    public $routeKey = '';

    //是否持久化
    public $durable = true;

    //排他队列
    public $exclusive = false;

    //$passive参数为false，队列不存在则会创建；参数为true，队列不存在则不会创建
    public $passive = false;

    /**
     * direct
     * topic
     * headers
     * fanout 广播消息给已知队列
     * @var string
     */
    public $type = 'fanout';

    /**
     * 自动删除
     * 当所有队列都已完成使用时，将删除Exchange。
     * 当最后一个用户退订时，队列被删除。
     * @var bool
     */
    public $autoDelete = false;

    /**
     * 镜像
     * 镜像队列，打开后消息会在节点之间复制，有master和slave的概念
     * @var bool
     */
    public $mirror = false;

    private $_conn = null;

    private $_channel = null;

    public function __construct($configs = [], $exchangeName = '', $queueName = '', $routeKey = '') {
        $this->setConfig($configs);
        $this->exchangeName = $exchangeName;
        $this->queueName = $queueName;
        $this->routeKey = $routeKey;
    }

    private function setConfig($configs) {
        if (!is_array($configs)) {
            throw new \Exception('config is not array');
        }
        if (!($configs['host'] && $configs['port'] && $configs['username'] && $configs['password'])) {
            throw new \Exception('configs is empty');
        }
        if (empty($configs['vhost'])) {
            $configs['vhost'] = '/';
        }
        $configs['login'] = $configs['username'];
        unset($configs['username']);
        $this->configs = $configs;
    }

    /**
     * 设置是否持久化，默认为true
     * @param $durable
     * @author wong
     */
    public function setDurable($durable) {
        $this->durable = $durable;
    }

    /**
     * exchange类型 默认 fanout
     * @param $type
     * @author wong
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * 设置是否自动删除
     * @param $autoDelete
     * @author wong
     */
    public function setAutoDelete($autoDelete) {
        $this->autoDelete = $autoDelete;
    }

    /**
     * 设置是否镜像
     * @param $mirror
     * @author wong
     */
    public function setMirror($mirror) {
        $this->mirror = $mirror;
    }

    /**
     * 打开amqp连接
     * @throws \Exception
     * @author wong
     */
    private function open() {
        if (!$this->_conn) {
            try {
                $this->_conn = new AMQPStreamConnection($this->configs['host'], $this->configs['port'],
                    $this->configs['login'], $this->configs['password'], $this->configs['vhost']);
                $this->initConnection();
            } catch (AMQPConnectionClosedException $e) {
                throw new $e('cannot connection rabbitmq',500);
            }
        }
    }

    /**
     * rabbitmq连接不变
     * 重置交换机，队列，路由等配置
     * @param $exchangeName
     * @param $queueName
     * @param $routeKey
     * @throws \Exception
     * @author wong
     */
    public function reset($exchangeName,$queueName,$routeKey) {
        $this->exchangeName = $exchangeName;
        $this->queueName = $queueName;
        $this->routeKey = $routeKey;
        $this->initConnection();
    }

    /**
     * 初始化rabbit连接的相关配置
     * @throws \Exception
     * @author wong
     */
    private function initConnection() {
        if (empty($this->exchangeName) || empty($this->queueName) || empty($this->routeKey)) {
            throw new \Exception('mondamq exhangeName or queueName or routeKey is empty',500);
        }
        //连接channel
        $this->_channel = $this->_conn->channel();
        //创建交换机
        $this->_channel->exchange_declare($this->exchangeName, $this->type, $this->passive, $this->durable, $this->autoDelete);

        //创建队列
        $this->_channel->queue_declare($this->queueName, $this->passive, $this->durable, $this->exclusive, $this->autoDelete);

        //将队列通过制定路由绑定到指定交换机上
        $this->_channel->queue_bind($this->queueName, $this->exchangeName);
    }

    public function close() {
        if ($this->_conn) {
            $this->_conn->close();
        }
        if ($this->_channel) {
            $this->_channel->close();
        }
    }

    public function __sleep() {
        $this->close();
        return array_keys(get_object_vars($this));
    }

    public function __destruct() {
        $this->close();
    }

    /**
     * 生产者发送消息
     * @param $msg
     * @return mixed
     * @throws \Exception
     * @author wong
     */
    public function send($msg) {
        $this->open();
        if (is_array($msg)) {
            $msg = json_encode($msg);
        } else {
            $msg = trim(strval($msg));
        }
        $message = new AMQPMessage($msg);
        return $this->_channel->basic_publish($message, $this->exchangeName, $this->routeKey);
    }

    /**
     * 消费者
     * @param $callback
     * @param bool $noAck
     * @return bool
     * @throws \Exception
     * @author wong
     */
    public function run($callback, $noAck = true) {
        $this->open();
        if (!$callback) {
            return false;
        }
        $this->_channel->basic_consume($this->queueName, '', false, $noAck, $this->exclusive, false, $callback);
        while(count($this->_channel->callbacks)){
            $this->_channel->wait();
        }
    }
}
