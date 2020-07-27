<?php


namespace zyk\library;


use think\App;
use think\Container;

class Request {


    protected $request;

    public function __construct(App $app) {
        $this->app = $app ?: Container::get('app');
        $this->request = $this->app['request'];
    }


    /**
     * 获取header信息
     * @param string $name
     * @param null $default 默认值
     * @param string $exception
     * @return mixed
     */
    public function getHeaders($name = '', $default = null, $exception = '') {
        $headers = $this->request->header($name, $default);
        if ($name === '' && is_string($exception)) {
            $exception = array_filter(explode(',', strtolower($exception)));
            foreach ($headers as $hk => $hv) {
                if (in_array(strtolower($hk), $exception) ) {
                    unset($headers[$hk]);
                }
            }
        }
        return $headers;
    }



}
