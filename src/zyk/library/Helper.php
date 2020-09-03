<?php

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
        $config = array_merge($config, config('app.redis'));
        $db_id = 0;
        if (!empty($config['db_id'])) {
            $db_id = $config['db_id'];
        }
        try {
            return \zyk\tools\query\Redis::getInstance($config, $db_id);
        } catch (\Exception $e) {
            throw new \Exception('redis连接异常');
        }
    }
}

// 返回gearman连接类
if (!function_exists('gearman')) {
    function gearman() {
        $gmc = new GearmanClient();
        $gmc->addServer();
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

