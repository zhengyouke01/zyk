<?php
/**
 * 控制器
 */
declare(strict_types = 1);
namespace zyk\library;
use think\App;
use think\Container;
use think\exception\ValidateException;
use zyk\library\ZykSend;

class ZykController {

    use ZykSend;

    /**
     * @var array 前置操作方法列表
     */
    protected $beforeActionList = [];

    protected $request;

    public function __construct(App $app) {
        $this->app = $app;
        $this->request = $this->app['request'];
        // 前置操作方法
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                $this->beforeAction($method, $options);
            }
        }
    }


    /**
     * 前置操作
     * @access protected
     * @param  string $method  前置操作方法名
     * @param  array  $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction($method, $options = []) {
        if (isset($options['only'])) {
            if (!in_array($this->request->action(), $options['only'])) {
                return;
            }
        } elseif (isset($options['except'])) {
            if (in_array($this->request->action(), $options['except'])) {
                return;
            }
        }
        call_user_func_array([$this, $method], []);
    }


    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @param  mixed        $callback 回调方法（闭包）
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate($data, $validate, $message = [], $batch = false, $callback = null)
    {
        if (is_array($validate)) {
            $v = $this->app->validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $v = $this->app->validate($validate);
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }

        if (is_array($message)) {
            $v->message($message);
        }

        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }

        if (!$v->check($data)) {
            if ($this->failException) {
                throw new ValidateException($v->getError());
            }
            return $v->getError();
        }

        return true;
    }


    //controller 内置拦截器
    public function __debugInfo() {
        $data = get_object_vars($this);
        unset($data['app'], $data['request']);
        return $data;
    }

}
