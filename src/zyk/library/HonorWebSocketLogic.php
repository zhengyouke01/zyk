<?php

namespace zyk\library;

use GatewayWorker\Lib\Gateway;
use think\facade\Env;
use zyk\library\traits\Jump;
use zyk\tools\jwt\ZykJWT;
use zyk\tools\rpc\RpcClient;

class HonorWebSocketLogic {
    use Jump;
    /**
     * 当客户端发来消息时触发
     * @param $clientId
     * @param $message
     * @return void
     */
    public static function onMessage($clientId = "", $message = "") {

        self::businessSocket($clientId, $message);
    }

    /**
     * 处理基础业务逻辑
     * @author YYNOEL 2022/10/10
     * @param $clientId
     * @param $message
     */
    protected static function businessSocket($clientId = "", $message = ""){
        $message = json_decode($message, true);
        $check = self::checkAnomaly($clientId, $message);
        if ($check == false) return false;

        switch ($message['type']) {
            case 'login': //登录
                $loginedUid = self::registerUser($clientId, $message);
                if (isset($loginedUid['uid'])){
                    self::success($clientId, '登录成功', ['uid' => $loginedUid['uid']]);
                }
                break;
            case 'ping': //ping
                self::success($clientId, '心跳成功', [],1, 'success');
                break;
            default:
                Gateway::closeClient($clientId);
                break;
        }
        return true;
    }

    /**
     * 检查是否异常
     * @author YYNOEL 2022/10/10
     * @param $clientId
     * @param $message
     */
    public static function checkAnomaly($clientId = "", $message = ""){
        if (!$message) {
            // 数据格式错误
            return false;
        }
        if (!isset($message['type'])){
            // 请求类型错误
            return false;
        }
        if (self::isTimeOut($clientId) && $message['type'] != 'login') {
            // 连接超时
            return false;
        }
        return true;
    }


    /**
     * @param $clientId
     * @return bool
     */
    private static function isTimeOut($clientId = "") {
        $thisSession = Gateway::getSession($clientId);
        if (isset($thisSession['connect_time'])) {
            // 未超过一个小时允许连接
            if ($thisSession['connect_time'] + 3600 < time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * 注册，绑定，客户端用户
     * @author wxw 2022/5/12
     *
     * @param $clientId
     * @param $data
     * @return mixed
     */
    private static function registerUser($clientId = "", $data = []) {
        $token = $data['token'] ?? '';
        if (empty($token)) {
            return self::error($clientId, '用户令牌不存在');
        }
        try {
            $info = ZykJWT::verification($token);
            if ($info['code'] == -1) {
                return self::error($clientId, '无效令牌');
            }
        } catch (\Exception $e) {
            return self::error($clientId, '无效令牌');
        }
        $key = 'user_auth_token:'.md5($token);
        $tokenInfo = redis(['clientId' => $clientId])->get($key);
        if (empty($tokenInfo)) {
            return self::error($clientId, '请先登陆,并选择需要登陆的账号');
        }

        $userInfo = json_decode($tokenInfo, true);
        if (empty($userInfo)) {
            return self::error($clientId, '请先登陆,并选择需要登陆的账号');
        }
        redis()->close();
        // test查询redis

        // 记录客户端
        Gateway::setSession($clientId, ['connect_time' => time(), 'uid'=> $userInfo['account_id']]);
        return ['uid' => $userInfo['account_id']];
    }

    public static function success($clientId = "", $message = "", $data = [], $code = 1, $type = "success") {
        $msg = self::resultMsg($data, $code, $message, $type);
        return self::sendMsg($clientId, $msg);
    }

    /**
     * 发送错误的返回
     * @param $clientId
     * @param $message
     * @param $code
     * @param $isClose  是否关闭客户端
     * @param $type
     * @return bool
     * @author YYNOEL 2022/10/10
     */
    public static function error($clientId = "", $message = "", $data = [], $code = 0, $isClose = 1, $type = "error") {
        $msg = self::resultMsg($data, $code, $message, $type);
        self::sendMsg($clientId, $msg);
        if ($isClose == 1) {
            Gateway::closeClient($clientId);
        }
        return true;
    }

    /**
     * 发送给网关
     * @param $clientId
     * @param $data
     * @return bool
     * @author wxw 2022/5/12
     *
     */
    private static function sendMsg($clientId, $data) {
        return Gateway::sendToClient($clientId, $data);
    }

}


