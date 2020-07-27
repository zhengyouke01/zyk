<?php
/**
 * 格式化数据返回
 */

namespace zyk\library;


class Response {

    protected $responseType = 'json';

    public function __construct($obj = null) {

    }

    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @param array $header
     * @param array $custom 定义返回参数
     * @return false|string
     */
    public function outputJson(int $code = 200, string $message = '', array $data = [], array $header = [], $custom = []) {
        $return['code'] = $code;
        $return['message'] = $message;
        $return['data'] = is_array($data) ? $data : ['info' => $data];
        #处理自定义参数
        foreach ($custom as $k => $v) {
            if (!is_numeric($k) && !in_array(strtolower($k), ['code', 'message', 'data'])) {
                $return[$k] = $v;
            }
        }
        // 发送头部信息
        foreach ($header as $name => $val) {
            if (is_null($val)) {
                header($name);
            } else {
                header($name . ':' . $val);
            }
        }
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }

}
