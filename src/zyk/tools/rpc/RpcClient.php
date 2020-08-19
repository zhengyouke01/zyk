<?php


namespace zyk\tools\rpc;

use Swoole\Client;
use zyk\tools\BaseInterface;

class RpcClient implements BaseInterface {

    protected $port = 9099;
    protected $host = '127.0.0.1';
    protected $options = [
        'open_length_check'     => 1,
        'package_length_type'   => 'N',
        'package_length_offset' => 0, //第N个字节是包长度的值
        'package_body_offset'   => 4, //第几个字节开始计算长度
        'package_max_length'    => 8192000, //协议最大长度
    ];

    public function serviceInfo()
    {
        return ['version' => '1.0', 'info' => 'RPC 客户端'];
    }

    public function __construct($host = null, $port = null) {
        if (!empty($port)) {
            $this->port = $port;
        }
        if (!empty($host)) {
            $this->host = $host;
        }
    }

    /**
     * 发起远程调用
     *
     * @author wxw 2020/6/28
     *
     * @param $data
     * @return mixed
     */
    public function send($data) {
        $options = $this->options;
        $a = null;
        $res = null;
        $client = new Client(SWOOLE_SOCK_TCP);
        $client->connect($this->host, $this->port, 0.5);
        $client->set($options);
        $client->send(json_encode($data));
        $res = $client->recv();
        $client->close();
        return $res;
    }

    /**
     * 进行远程调用
     *
     * @author wxw 2020/6/28
     *
     * @param $type
     * @param $func string 调试的类和方法， 例如：ArrayTools::max
     * @param array $args
     */
    public function call($type, $func, $args = []) {
        $data = [
            'type' => $type,
            'func' => $func,
            'args' => $args
        ];
        return $this->processRes($this->send($data));
    }

    /**
     * 远程调用mysql语句
     *
     * @author wxw 2020/7/9
     *
     * @param $type
     * @param $sql
     * @param array $args
     * @return array|mixed
     */
    public function callSql($type, $sql, $args = []) {
        $data = [
            'type' => $type,
            'sql' => $sql,
            'args' => $args
        ];
        return $this->processRes($this->send($data));
    }


    /**
     * 返回结果的处理
     * @author wxw 2020/6/28
     *
     * @param $data
     * @return array|mixed
     */
    protected function processRes($data) {
        $data = json_decode($data, true);
        if (!$data) {
            throw new \Exception('rpc exception:'. '返回信息错误');
        }
        if ($data['code'] != 0) {
            throw new \Exception('rpc exception:'. $data['msg']);
        }
        if (!isset($data['data'])) {
            throw new \Exception('rpc exception:'. '返回信息错误');
        }
        return $data['data'];
    }

}