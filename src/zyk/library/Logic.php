<?php


namespace zyk\library;


class Logic {

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
}