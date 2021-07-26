<?php
/**
 * 队列处理工厂类
 * @author LYJ 2021.07.23
 */
namespace zyk\library\queue;

use think\Loader;

class ZykQueue {

    protected $queueConf;
    protected $queueDriver;
    public function __construct() {
        $this->queueConf = config('app.queue');
        $this->queueDriver = $this->queueConf['driver_class'];
    }

    /**
     * 发送队列信息
     * @author LYJ 2021.07.23
     * @param $queueName
     * @param $message
     */
    public function send($queueName, $message) {
        $object = Loader::factory($this->queueDriver, '\\zyk\\library\\queue\\driver\\', $this->queueConf);
        return $object->send($queueName, $message);
    }


}
