<?php
/**
 * 实现自定义抛出异常消息 重写Handle的render方法
 * @author lwh 2019-12-12
 */
namespace zyk\library\Exception;

//use app\common\service\Monolog;
use Exception;
use think\exception\ErrorException;
use think\exception\Handle;
use think\exception\HttpException;
use zyk\library\traits\Jump;
use zyk\tools\log\Monolog;

class Http extends Handle {

    use Jump;

    private $code = 500;
    private $msg = '很抱歉,请稍后重试';
    // 异常级别文字
    private $errorStr = [
        E_NOTICE => 'NOTICE',
        E_WARNING => 'WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_USER_WARNING => 'USER_WARNING'
    ];
    /**
     * 输出异常信息
     * @author wxw 2020-08-08
     * @param Exception $e
     * @return \think\Response|\think\response\Json
     */
    public function render(Exception $e) {
        $message = '报错文件:'.$e->getFile().',报错行数:第'.$e->getLine();
        //参数验证错误
        if($e instanceof ValidateException){
            $this->code = 201;
            $this->msg = '参数错误';
        }
        //请求异常
        if($e instanceof HttpException && request()->isAjax()){
            $this->code = 500;
            $this->msg = '服务器请求错误';
        }
        // 错误记录
        if ($e instanceof ErrorException) {
            // 检查错误级别
            if (in_array($e->getSeverity(),  [E_NOTICE, E_WARNING, E_USER_NOTICE, E_USER_WARNING])) {
                // notice和warning的处理
                $msg = '错误信息:'.$e->getMessage(). '， 错误位置：'.$e->getFile().'，行：'.$e->getLine().'，详细错误: '.$e->getTraceAsString();
                zykLog($msg, 'warning');
            } else {
                $msg = '错误信息:'.$e->getMessage(). '， 错误位置：'.$e->getFile().'，行：'.$e->getLine().'，详细错误: '.$e->getTraceAsString();
                zykLog($msg, 'error');
            }
        } else {
            $msg = '错误信息:'.$e->getMessage(). '， 错误位置：'.$e->getFile().'，行：'.$e->getLine().'，详细错误: '.$e->getTraceAsString();
            zykLog($msg, 'error');
        }
        // 错误返回
        if(config('app_debug')){
            //如果是开发模式
            return json(['code'=>$e->getCode(),'msg'=>$e->getMessage(),'data'=>['msg'=>$message]]);
        }else{
            //如果是生产模式,则返回与设定好的未知错误的json
            return json(['code'=>$e->getCode(),'msg'=>$this->msg,'data'=>['msg'=>$this->msg]]);
        }
    }


    /**
     * 关闭错误级别的情况下
     * @author YYNOEL 2020/6/10
     * @param Exception $e
     * @return \think\response\Json|void
     */
    public function report($e) {
        $message = '报错文件:'.$e->getFile().',报错行数:第'.$e->getLine();
        // 错误记录
        if ($e instanceof ErrorException) {
            // 检查错误级别
            if (in_array($e->getSeverity(),  [E_NOTICE, E_WARNING, E_USER_NOTICE, E_USER_WARNING])) {
                // notice和warning的单独记录
//                Monolog::error('wnError', '错误内容:' . $e->getMessage() . '， 错误位置：' . $e->getFile() . '，line：' . $e->getLine() . '，error_str: ' . $e->getTraceAsString(), '', '', $this->errorStr[$e->getSeverity()]);
            }
        }
        // 错误返回
        if(config('app_debug')){
            //如果是开发模式
            return json(['code'=>$e->getCode(),'msg'=>$e->getMessage(),'data'=>['msg'=>$message]]);
        }else{
            //如果是生产模式,则返回与设定好的未知错误的json
            return json(['code'=>$e->getCode(),'msg'=>$e->getMessage(),'data'=>['msg'=>$this->msg]]);
        }
    }
}
