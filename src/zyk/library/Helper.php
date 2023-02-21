<?php
use think\facade\Env;

if (!function_exists('logic')) {
    /**
     * 实例化logic
     * @param string    $name logic名称
     * @param string    $layer 业务层名称
     * @param bool      $appendSuffix 是否添加类名后缀
     * @return \think\Model
     */
    function logic($name = '', $layer = 'logic', $appendSuffix = false)
    {
        return app()->create($name, $layer, $appendSuffix);
    }
}


// 返回redis连接类
if (!function_exists('redis')) {
    function redis($config = []) {
        $config = array_merge(config('app.redis'), $config);
        $db_id = 0;
        if (!empty($config['db_id'])) {
            $db_id = $config['db_id'];
        }
        $isActive = $config['redis_active'] ?? 0;
        try {
            if ($isActive == 1) {
                return \zyk\tools\query\Redis::getRedisExample($config, $db_id);
            } else {
                return \zyk\tools\query\Redis::getInstance($config, $db_id);
            }
        } catch (\Exception $e) {
            throw new \Exception('redis连接异常');
        }
    }
}

// 返回gearman连接类
if (!function_exists('gearman')) {
    function gearman() {
        $host = empty(Env::get('gearman.host')) ? '127.0.0.1' : Env::get('gearman.host');
        $port = empty(Env::get('gearman.port')) ? 4730 : Env::get('gearman.port');
        $gmc = new GearmanClient();
        $gmc->addServer($host, $port);
        return $gmc;
    }
}


/**
 * 获取统一返回的状态码
 * @author wxw 2019/12/12
 *
 * @param $data
 *
 * @return mixed
 */
function res_client_code($data) {
    return $data['code'];
}


/**
 * 获取统一返回的msg信息
 * @author wxw 2019/12/12
 *
 * @param $data
 *
 * @return mixed
 */
function res_client_msg($data) {
    return $data['msg'];
}

/**
 * 获取统一返回的数据
 * @author wxw 2019/12/12
 *
 * @param $data
 *
 * @return mixed
 */
function res_client_data($data) {
    return $data['data'];
}

/**
 * 获取统一返回的状态码
 * @author wxw 2019/12/12
 *
 * @param $data
 *
 * @return mixed
 */
function res_get_code($data) {
    return $data['status'];
}


/**
 * 获取统一返回的msg信息
 * @author wxw 2019/12/12
 *
 * @param $data
 *
 * @return mixed
 */
function res_get_msg($data) {
    return $data['msg'];
}

/**
 * 获取统一返回的数据
 * @author wxw 2019/12/12
 *
 * @param $data
 *
 * @return mixed
 */
function res_get_data($data) {
    if (res_get_code($data) == RESULT_ERROR) {
        return false;
    }
    return $data['data'];
}

/**
 * 日志用户信息处理，组合需要的格式
 * @author wxw 2021/3/29
 *
 * @param $userInfo
 * @return array
 */
function logUserInfo($userInfo) {
    $logUserInfo = [
        'uid' => defined("UID") ? UID :0,
        'usertype' => defined("USER_TYPE") ? USER_TYPE: '',
        'username' =>  $userInfo['nickname'] ?? '',
        'account' => $userInfo['username'] ?? '',
        'org_id' => $userInfo['org_id'] ?? '',
        'role_id' => $userInfo['role_id'] ?? '',
        'info_id' => $userInfo['info_id'] ?? '',
    ];
    if (defined("USER_TYPE") &&  defined("ZYK_ADMIN_SUPER_ADMIN") && USER_TYPE == ZYK_ADMIN_SUPER_ADMIN) {
        $logUserInfo['uid'] = $userInfo['account_id'] ?? -1;
    }
    return $logUserInfo;
}

if (!function_exists('zykLog')) {
    /**
     * @author wxw 2021/4/10
     *
     * @param string $msg 日志记录的信息
     * @param string $level 日志等级，目前可以记录info 、error 、warning。默认info
     * @param string $subsystem 需要写入到的指定子系统的系统标识，用于子跨系统目录的方式
     * @param array $user 操作人信息。需要记录的操作人信息，最后按json格式存入日志内容中，默认为当前登陆用户的用户信息，如果未登录，存储空
     * @param string $uri 操作地址、参数。默认为请求方式、url和body。如果非请求，默认空
     * @param string $ip 操作者（客户端）ip
     */
    function zykLog($msg, $level = 'INFO', $subsystem = '', $user = [], $uri = '', $ip = '') {
        \zyk\library\Log\Log::record($msg, $level, $user, $uri, $ip, $subsystem);
    }
}

if (!function_exists('queue_producer')) {
    /**
     * 队列调用类
     * @author lyj 2021/7/26
     *
     * @param $queueName string 队列名称，监听的频道
     * @param $message string 发送的消息
     * @param $queueConf array 自定义队列完整配置
     * @return mixed
     */
    function queue_producer($queueName, $message, $queueConf = []) {
        return  \zyk\library\queue\Queue::send($queueName, $message, $queueConf);
    }
}

if (!function_exists('system_log')) {
    /**
     * @author wxw 2023/2/15
     *
     * @param $type string 类型
     * @param $typeId string 数据id
     * @param $info string 内容
     * @param $orgId int 联营id
     */
    function system_log($type, $typeId, $info, $orgId = '') {
        $userInfo = app(\zyk\library\Auth\AuthUser::class)->getUserInfo();
        $userName = ($userInfo['role_name']?? '').'+'.($userInfo['nickname'] ?? '');
        $data = [
            'user_name' => $userName,
            'org_id' => empty($orgId) ? ($userInfo['org_id'] ?? 2) : $orgId,
            'mobile' => $userInfo['username'] ?? '',
            'type' => $type,
            'type_id' => $typeId,
            'create_time' => time(),
            'info' => $info,
            'account_id' => $userInfo['account_id'] ?? 0
        ];
        return \think\Db::name('system_record')->insertGetId($data);
    }
}

