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
        $this->username = $conf['username'] ?? '';
        $this->password = $conf['password'] ?? '';
        !empty($conf['brokers']) ? $this->brokers = $conf['brokers'] : '';
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
        //消息key
        if (empty($key)) {
            $key = self::createUniqueKey();
        }
        //broker
        if (empty($broker)) {
            $broker = Env::get('kafka.brokers') ?? 'localhost:9092';
        }
        try {
            //配置调用
            $conf = $this->getConf();
            $rk = new \RdKafka\Producer($conf);
            $rk->addBrokers($broker);
            $topic = $rk->newTopic($topics);
            //分区RD_KAFKA_PARTITION_UA代表未赋值； 0—RD_KAFKA_MSG_F_BLOCK 阻塞生产；消息
            $topic->produce(RD_KAFKA_PARTITION_UA, 0, $value);

            //消息刷出重试
            $timeout_ms = 5000; //
            for ($retry = 0; $retry < 5; $i++) {
                $result = $rk->flush($timeout_ms);
                if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) {
                    return true;
                    break;
                }
            }
            //失败后输出
            if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
                return false;
            }
        } catch (\Exception $exception) {
            return $exception->getMessage();
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
