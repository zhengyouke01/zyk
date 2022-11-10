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
     * @param $conf array 自定义kafka配置
     */
    public function send($queueName, $message, $conf = []) {
        if (!empty($conf)) {
            $this->queueConf = $conf;
        }
        $object = Loader::factory($this->queueDriver, '\\zyk\\library\\queue\\driver\\', $this->queueConf);
        return $object->send($queueName, $message);
    }


}
