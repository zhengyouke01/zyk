<?php
/**
 * 生产队列
 * @author LYJ 2021。06.07
 */
namespace zyk\library\queue\driver;

use think\facade\Env;

class ZykKafKa {


    private $username;
    private $password;
    private $brokers = 'localhost:9092';

    public function __construct($conf = []) {
        // 验证扩展是否存在
        if (!extension_loaded('rdkafka')) {
            throw new \Exception('rdkafka extension not exist');
        }

        $this->username = $conf['username'] ?? '';
        $this->password = $conf['password'] ?? '';
        $broker = Env::get('kafka.brokers');
        $this->brokers = !empty($conf['brokers']) ? $conf['brokers'] : $broker;
    }


    /**
     * 生产单条消息
     * @author LYJ 2021.07.22
     * @param string $topics 话题
     * @param string|array $value 消息内容
     * @param string $key 消息ID
     * @param string $broker
     * @param
     */
    public function send($topics, $value) {
        //值处理
        if (is_array($value)) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        }
        //broker
        $broker = $this->brokers;
        //配置调用
        $conf = $this->getConf();
        $rk = new \RdKafka\Producer($conf);
        $rk->addBrokers($broker);
        $topic = $rk->newTopic($topics);
        //分区RD_KAFKA_PARTITION_UA代表未赋值； 0—RD_KAFKA_MSG_F_BLOCK 阻塞生产；消息
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $value);

        //消息刷出重试
        $timeout_ms = 5000; //
        for ($retry = 0; $retry < 5; $retry++) {
            $result = $rk->flush($timeout_ms);
            if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) {
                return true;
            }
        }
        //失败后输出
        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            throw new \Exception('kafka queue produce push fail');
        }
    }


    /**
     * @return Producer
     */
    public function getConf() {
        $conf = new \RdKafka\Conf();
        $conf->set('security.protocol', 'sasl_plaintext'); //sasl_plaintext SASL_SSL
        $conf->set('sasl.mechanisms', 'PLAIN');
        $conf->set('sasl.username', $this->username);
        $conf->set('sasl.password', $this->password);
        return $conf;
    }

    //生成key
    static function createUniqueKey() {
        return uniqid('', true).rand(111, 999);
    }

}
