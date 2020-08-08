<?php


namespace zyk\library;


use think\App;

class Request {


    protected $request;

    protected $header;

    public function __construct(App $app) {
        $this->app = $app;
        $this->request = $this->app['request'];
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
