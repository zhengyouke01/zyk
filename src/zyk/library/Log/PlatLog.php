<?php


namespace zyk\library\Log;


use think\App;
use zyk\library\Auth\AppAuth;
use zyk\library\Auth\AuthUser;
use zyk\library\Config;
use zyk\library\Log\factory\MonologFactory;

class PlatLog {

    /**
     * 实际写日志的类库
     * @var string
     */
    private $driver = null;

    /**
     * 写入日志的类型，先放着
     * @var string
     */
    private $type = 'file';

    /**
     * 系统标记
     * @var mixed|string
     */
    private $sys = '';

    public function __construct(Config $sysConfig) {
        $zykLogConfig = config('app.zykLog');
        if (isset($zykLogConfig['file_name'])) {
            $sys = $zykLogConfig['file_name'];
            $this->sys = $sysConfig->getVal('sysStrName', $sys) ?  $sysConfig->getVal('sysStrName', $sys) : '无系统标记';
        }
    }

    /**
     * 记录用户
     * @author wxw 2021/4/6
     *
     * @param $msg
     * @param $level
     * @param array $user
     * @param string $uri
     * @param string $ip
     * @return mixed
     */
    public function record($msg, $level, $user = [], $uri = '', $ip = '', $subsystem = '') {
        $zykLogConfig = config('app.zykLog');
        if (!empty($subsystem)) {
            // 在特殊需要传递记录文件名称的时候，
            $zykLogConfig['file_name'] = $subsystem;
        }
        $logFactory = $this->loaderFactory($zykLogConfig['log_driver'], $zykLogConfig);
        $driver = $logFactory->getDriver($level); // 获取记录类
        $msgStr = $this->processMsg($msg, $user, $ip, $uri);
        $driver->save($msgStr);
    }

    /**
     * 加载对应的工厂
     * @author wxw 2021/4/14
     *
     * @param $type
     * @param $config
     * @return mixed
     */
    protected function loaderFactory($type, $config) {
        $factoryPath = "\\zyk\\library\\Log\\factory\\";
        $func = ucfirst($type);
        $class = $factoryPath.$func.'Factory';
        if (class_exists($class)) {
            return new $class($config);
        } else {
            throw new \Exception("factory class {$func} not exist");
        }
    }

    /**
     * 处理格式
     * @author 小贤
     * 2019-08-28
     * @param $msg
     * @param $ip
     * @param string $params
     * @param string $module
     * @return string
     */
    private function processMsg($msg, $user = [], $ip = '', $uri = '', $sys = '') {
        // 系统标记，填充默认
        $sys = empty($sys)?$this->sys:$sys;
        // ip处理，填充默认
        $ip = empty($ip)? (empty(request()->ip())? '':request()->ip()) :$ip;
        // uri资源内容处理
        if (PHP_SAPI == 'cli') {
            $uriInfo = "脚本:".request()->pathinfo();
        } else {
            $uriInfo = request()->method().' ';
            $uriInfo .= empty($uri)? (empty(request()->url()) ? '': request()->url()) :$uri;
            if (request()->isPost()) {
                // 记录post参数
                $uriInfo.=' body:'.json_encode(request()->param(), JSON_UNESCAPED_UNICODE);
            }
        }

        // 处理用户信息
        $userInfo = '';
        if (!empty($user)) {
            $userInfo = json_encode($user, JSON_UNESCAPED_UNICODE);
        } else {
            if (PHP_SAPI == 'cli') {
                $userInfo = '{}';
            } else {
                $user = logUserInfo(app(AuthUser::class)->getUserInfo());
                if (!empty($user)) {
                    $userInfo = json_encode($user, JSON_UNESCAPED_UNICODE);
                }
            }
        }
        return "$ip"."\t".$uriInfo."\t".$sys."\t".$userInfo."\t".$msg;
    }

}