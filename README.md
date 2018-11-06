# MondaMQ
MondaMQ基于RabbitMQ的php-amqplib二次封装而来。主要是对生产者以及消费者进出进行了规则统一方便多个系统可以共用

一.src目录下MondaMQCommand进行了php-amqp进行了二次封装，对生产者&消费者的封装

二.tests目录下是MondaMQ各种单元测试脚本

    1.product.php 生产者
    2.receivedClientByAll.php & receivedClientByAll1.php Fanout类型监听【广播】 $type='fanout' 默认执行 php product.php 即可
    3.receivedClientByOnle.php Direct类型监听【RoutingKey与BindingKey要一致对应】 $type='direct' 执行 php product.php 1
    3.receivedClientByLike.php Topic类型监听【正则匹配RoutingKey与BindingKey】 $type='direct' 执行 php product.php 1
三.参数解释
``````
    Exchange:交换机【路由】接受生产者发送的消息，并根据Binding规则将消息路由给服务器中的队列。ExchangeType决定了Exchange路由消息的行为，例如，在RabbitMQ中，ExchangeType有direct、Fanout和Topic三种，不同类型的Exchange路由的行为是不一样的。
    $exchangeName: 交换机名称
    $type:ExchangeType名称
            1.direct 会将消息中的RoutingKey与该Exchange关联的所有Binding中的BindingKey进行比较，如果相等，则发送到该Binding对应的Queue中。
            2.topic 会按照正则表达式，对RoutingKey与BindingKey进行匹配，如果匹配成功，则发送到对应的Queue中。
            3.fanout 会将消息发送给所有与该 Exchange 定义过 Binding 的所有 Queues 中去，其实是一种广播行为。
    

    $queueName:队列名称

    $routeKey:路由名称

    $durable:持久化

    $exclusive:排他队列排他队列，如果一个队列被声明为排他队列，该队列仅对首次声明它的连接可见，并在连接断开时自动删除。这里需要注意三点：其一，排他队列是基于连接可见的，同一连接的不同信道是可以同时访问同一个连接创建的排他队列的。其二，“首次”，如果一个连接已经声明了一个排他队列，其他连接是不允许建立同名的排他队列的，这个与普通队列不同。其三，即使该队列是持久化的，一旦连接关闭或者客户端退出，该排他队列都会被自动删除的。这种队列适用于只限于一个客户端发送读取消息的应用场景。

    $passive:参数为false，队列不存在则会创建；参数为true，队列不存在则不会创建

    $autoDelete:自动删除,当所有队列都已完成使用时，将删除Exchange。当最后一个用户退订时，队列被删除。
    
    basic_publish(
            $msg,//消息对象
            $exchange = '',//路由
            $routing_key = '',//消息路由名
            $mandatory = false,
            $immediate = false,
            $ticket = null
    )
   
    basic_consume(
        $queue = '',//消息要取得消息的队列名
        $consumer_tag = '',//消费者标签
        $no_local = false,//这个功能属于AMQP的标准,但是rabbitMQ并没有做实现.
        $no_ack = false,//收到消息后,是否不需要回复确认即被认为被消费
        $exclusive = false,//排他消费者,即这个队列只能由一个消费者消费.适用于任务不允许进行并发处理的情况下.比如系统对接
        $nowait = false,//不返回执行结果,但是如果排他开启的话,则必须需要等待结果的,如果两个一起开就会报错
        $callback = null,//回调函数
        $ticket = null,
        $arguments = array()
    )
``````