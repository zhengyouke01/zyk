<?php
declare(strict_types = 1);
/**
 * 定义返回返回数据方法
 */

namespace zyk\library;


trait Send {

    protected $response;

    public function __construct(Response $response) {
        $this->response = $response;
    }

    /**
     * @param string $msg 信息
     * @param int $code 状态码
     * @param null $data 返回数据
     * @param array $header 返回头
     * @param array $custom 返回链接
     */
    public function successSend(string $msg = '', int $code = 200, $data = null, array $header = [], array $custom) {
        $data = $this->output($data);
        call_user_func_array([$this->response, 'outputJson'], [$code, $msg, $data, $header, $custom]);
    }

    /**
     * 失败返回
     */
    /**4
     * @param string $msg
     * @param int $code
     * @param null $data
     * @param array $header
     * @param $url
     * @param array $custom 自定义参数返回
     */
    public function errorSend($msg = '', $code = 400, $data = null, $header = [], $custom = []) {
        $data = $this->output($data);
        call_user_func_array([$this->response, 'outputJson'], [$code, $msg, $data, $header, $custom]);
    }


    /**
     * 格式化返回参数
     * @param array $data 数组
     */
    protected function output($data) {
        if (!is_array($data)) {
            if (is_string($data)) {
                return htmlspecialchars_decode($data);
            }
            return $data;
        } else {
            array_walk_recursive($data, function (&$value) {
                if (is_string($value)) {
                    $value = htmlspecialchars_decode($value);
                }
            });
            return $data;
        }
    }

}
