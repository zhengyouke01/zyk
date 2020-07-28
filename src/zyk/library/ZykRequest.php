<?php


namespace zyk\library;


class ZykRequest {


    protected $request;

    protected $header;

    protected $server;

    public function __construct(App $app) {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->server = $_SERVER;
    }


    /**
     * 获取header信息
     * @param string $name
     * @param null $default 默认值
     * @param string $exception
     * @return mixed
     */
    public function getExceptionHeaders($name = '', $default = null, $exception = '') {

        if (empty($this->header)) {

            $header = [];
            if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
                $header = $result;
            } else {
                $server = $this->server;
                foreach ($server as $key => $val) {
                    if (0 === strpos($key, 'HTTP_')) {
                        $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                        $header[$key] = $val;
                    }
                }
                if (isset($server['CONTENT_TYPE'])) {
                    $header['content-type'] = $server['CONTENT_TYPE'];
                }
                if (isset($server['CONTENT_LENGTH'])) {
                    $header['content-length'] = $server['CONTENT_LENGTH'];
                }
            }
            $this->header = array_change_key_case($header);
        }

        if ($name === '') {
            $exception = array_filter(explode(',', strtolower($exception)));
            foreach ($this->header as $hk => $hv) {
                if (in_array(strtolower($hk), $exception) ) {
                    unset($this->header[$hk]);
                }
            }
            return $this->header;
        } else {
            $name = str_replace('_', '-', strtolower($name));
            return isset($this->header[$name]) ? $this->header[$name] : $default;
        }
    }

    /**
     * 调用request的方法
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments) {
        return call_user_func_array([$this->request, $name], $arguments);
    }

}
