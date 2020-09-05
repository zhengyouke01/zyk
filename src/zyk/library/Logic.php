<?php


namespace zyk\library;


class Logic {

    protected $zykConf = null;

    public function __construct(Config $config) {
        $this->zykConf = $config;
    }

    public function success($msg, $data = [], $code = 1) {
        $res = [
            'status' => RESULT_SUCCESS,
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ];
        return $res;
    }

    protected function error($msg, $data = [], $code = 0) {
        $res = [
            'status' => RESULT_ERROR,
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ];
        return $res;
    }

    protected static function errorReturn($msg, $data = [], $code = 0) {
        $res = [
            'status' => RESULT_ERROR,
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ];
        return $res;
    }

    protected static function successReturn($msg, $data = [], $code = 0) {
        $res = [
            'status' => RESULT_SUCCESS,
            'msg' => $msg,
            'data' => $data,
            'code' => $code
        ];
        return $res;
    }
}