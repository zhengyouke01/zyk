<?php


namespace zyk\library;

use think\App;
use think\Container;
use think\Controller as TpController;
use zyk\library\traits\Jump;

class Controller {
    
    use Jump;

    // tp APP 容器
    protected $app;

    // tp 内置controller
    protected $controller;
    // request 对象
    protected $request;

    protected $config;

    // 验证黑白名单
    protected $authWhite = [];
    protected $authBlack = [];

    // 统一配置
    protected $zykConfig;

    // 请求参数
    protected $param;

    /**
     * 前置操作方法列表（即将废弃）沿用Tp的方法
     * @var array $beforeActionList
     */
    protected $beforeActionList = [];


    public function __construct(App $app, TpController $controller, Config $config) {
        $this->app     = $app ?: Container::get('app');
        $this->controller = $controller;
        // 请求
        $this->request = $app['request'];
        $this->config = $app['config'];
        $this->initRequestInfo();
        $this->initRequest($app);
        // 沿用tp初始化机制
        $this->initialize();
        // 预制配置类载入
        $this->zykConfig = $config;
        // 注册登陆验证的方法 todo
        $this->auth();

        // 前置操作方法 即将废弃 沿用Tp的
        foreach ((array) $this->beforeActionList as $method => $options) {
            is_numeric($method) ?
                $this->beforeAction($options) :
                $this->beforeAction($method, $options);
        }

        // 后置行为放到路由后置处理
    }


    protected function initialize(){
    }

    /**
     * 注册自己的request类
     *
     * @author wxw 2020/8/7
     *
     * @param App $app
     */
    protected function initRequest(App $app) {
        $this->request = app('zyk\library\Request', [$app]);
    }

    /**
     * 鉴权方法处理
     *
     * @author wxw 2020/8/7
     *
     */
    private function auth() {
        $white = array_map('strtolower',$this->authWhite);
        $black = array_map('strtolower',$this->authBlack);
        $action = $this->request->action();

        // 优先判断黑名单，如果黑名单批量，直接进入判断
        $need = false;
        if (in_array($action, $white)) {
            $need = true;
        }
        if (!empty($black) && !in_array($action, $black)) {
            $need = true;
        }

        if ($need) {
            // 调用验证方法
            $class = $this->config->get('auth_class');
            $class = empty($class) ? $this->zykConfig->getConf('auth'): $class;
            // 调用
            if (class_exists($class)) {
                $res = call_user_func([app($class), 'checkAuth'], $this->request);
            }
        }
    }

    /**
     * 访问原先控制器的方法
     *
     * @author wxw 2020/8/7
     *
     * @param $name 方法名
     * @param $arguments 参数
     * @return mixed
     */
    public function __call($name, $arguments) {
        $conRef = new \ReflectionClass($this->controller);
        $mothed = $conRef->getMethod($name);
        if ($mothed->isProtected()) {
            // 只有保护方法开放读取，私有的不允许读取
            $mothed->setAccessible(true);
        } elseif ($mothed->isPrivate()) {
            throw new \Exception('method not exists: ' . $mothed->getName() . '()');
        }
        return Container::getInstance()->invokeReflectMethod($this->controller, $mothed, $arguments);
    }

    /**
     * 初始化请求信息
     */
    final private function initRequestInfo(){
        defined('IS_POST')          or define('IS_POST',         $this->request->isPost());
        defined('IS_GET')           or define('IS_GET',          $this->request->isGet());
        defined('MODULE_NAME')      or define('MODULE_NAME',     $this->request->module());
        defined('CONTROLLER_NAME')  or define('CONTROLLER_NAME', $this->request->controller());
        defined('ACTION_NAME')      or define('ACTION_NAME',     $this->request->action());
        defined('URL')              or define('URL',             strtolower($this->request->path()));
        defined('URL_MODULE')       or define('URL_MODULE',      strtolower($this->request->module()) . SYS_DSS . URL);
        defined('CLIENT_IP')        or define('CLIENT_IP',       $this->request->ip());
        $this->param = $this->request->param();
    }
}