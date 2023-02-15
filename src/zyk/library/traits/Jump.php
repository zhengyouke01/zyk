<?php
namespace zyk\library\traits;

use think\exception\HttpResponseException;
use think\Response;

trait Jump {

    use \traits\controller\Jump;

    /**
     * 系统通用跳转,做接口使用
     */
    final protected function jump($jump_type = null, $message = null, $data = null, $code = 0){
        if (is_array($jump_type)){
            $jump_type = array_values($jump_type);
            switch (count($jump_type)) {
                case 2  : list($jump_type, $message) = $jump_type; break;
                case 3  : list($jump_type, $message, $data) = $jump_type; break;
                case 4  : list($jump_type, $message, $data, $code) = $jump_type; break;
                default : die(RESULT_ERROR);
            }
        }

        $success  = RESULT_SUCCESS;
        $error    = RESULT_ERROR;
        $redirect = RESULT_REDIRECT;
        $result   = RESULT_CODE;

        switch ($jump_type) {
            case $success  : $this->success($message, $data); break;
            case $error    : $this->error($message, $data);   break;
            case $redirect : $this->$redirect($message);      break;
            case $result   : $this->result($data,$code,$message); break;
            default        : die(RESULT_ERROR);
        }
    }
    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  mixed     $data 返回的数据
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function success($msg = '', $data = '',  array $header = [])
    {
        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
        ];

        $type = $this->getResponseType();
        // 把跳转模板的渲染下沉，这样在 response_send 行为里通过getData()获得的数据是一致性的格式
        if ('html' == strtolower($type)) {
            $type = 'jump';
        }

        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  mixed     $data 返回的数据
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function error($msg = '', $data = '',  array $header = [])
    {
        $type = $this->getResponseType();
        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
        ];

        if ('html' == strtolower($type)) {
            $type = 'jump';
        }

        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的API数据到客户端
     * @access protected
     * @param  mixed     $data 要返回的数据
     * @param  integer   $code 返回的code
     * @param  mixed     $msg 提示信息
     * @param  string    $type 返回数据格式
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function result($data, $code = 0, $msg = '', $type = '', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * ws封装后统一返回
     * @author YYNOEL 2022/10/11
     * @param $data
     * @param $code
     * @param $msg
     * @param $status
     * @return false|string
     */
    public static function resultMsg($data = [], $code = 0, $msg = "",  $status = '') {
        $msg = ['code' => $code, 'msg' => $msg, 'data' => $data, 'status' => $status,];
        return json_encode($msg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * URL重定向
     * @access protected
     * @param  string         $url 跳转的URL表达式
     * @param  array|integer  $params 其它URL参数
     * @param  integer        $code http code
     * @param  array          $with 隐式传参
     * @return void
     */
    protected function redirect($url, $params = [], $code = 302, $with = [])
    {
        $response = new Redirect($url);

        if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }

        $response->code($code)->params($params)->with($with);

        throw new HttpResponseException($response);
    }
}
