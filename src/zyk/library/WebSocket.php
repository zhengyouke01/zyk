<?php

namespace zyk\library;

use GatewayWorker\Lib\Gateway;
use think\facade\Env;
use zyk\library\traits\Jump;

class WebSocket {
use Jump;
    public static function onConnect($clientId = ""){
        echo "连接成功".$clientId;
    }

    /**
     * 当客户端发来消息时触发
     * @param $clientId
     * @param $message
     * @return void
     */
    public static function onMessage($clientId = "", $message = "") {
        call_user_func([new self(), 'businessSocket'], $clientId, $message);
    }

    /**
     * 处理基础业务逻辑
     * @author YYNOEL 2022/10/10
     * @param $clientId
     * @param $message
     */
    protected function businessSocket($clientId = "", $message = ""){
        $message = json_decode($message, true);
        $check = $this->checkAnomaly($clientId, $message);
        if ($check != true){
            return  false;
        }
        switch ($message['type']) {
            case 'login': //登录
                Gateway::setSession($clientId, ['connect_time' => time()]);
                $loginedUid = $this->registerUser($clientId, $message);
                if (is_string($loginedUid)){
                    $this->success($clientId, '登录成功', ['uid' => $loginedUid]);
                }
                break;
            case 'ping': //ping
                $this->success($clientId, '心跳成功');
                break;
            case 'pong': // 客户端主动关闭
                $thisSession = Gateway::getSession($clientId);
                if (empty($thisSession)){
                    $this->success($clientId, '连接断开成功');
                    Gateway::closeClient($clientId);
                }
                break;
            default:
                zykLog("异常连接".$clientId);
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
    public function checkAnomaly($clientId = "", $message = ""){
        if (!$message) {
            return $this->error($clientId, '数据格式错误');
        }
        if (!isset($message['type'])){
            return $this->error($clientId, '请求类型错误');
        }
        if (self::isTimeOut($clientId) && $message['type'] != 'login') {
            return $this->error($clientId, '连接超时', [],99);
        }
        return true;
    }

    /**
     * 当用户断开连接时触发
     * @param $clientId
     * @return void
     */
    public static function onClose($clientId = "") {
        echo '连接断开'.$clientId;
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
    private function registerUser($clientId = "", $data = []) {
        $token = $data['token'] ?? '';
        if (empty($token)) {
            return $this->error($clientId, '用户令牌不存在');
        }
        try {
            $info = ZykJWT::verification($token);
            if ($info['code'] == -1) {
                return $this->error($clientId, '无效令牌');
            }
        } catch (\Exception $e) {
            return $this->error($clientId, '无效令牌');
        }
        $key = 'user_auth_token:'.md5($token);
        $tokenInfo = redis(['clientId' => $clientId])->get($key);
        if (empty($tokenInfo)) {
            return $this->error($clientId, '请先登陆,并选择需要登陆的账号');
        }
        redis()->close();
        $userInfo = json_decode($tokenInfo, true);
        if (empty($userInfo)) {
            return $this->error($clientId, '请先登陆,并选择需要登陆的账号');
        }
        // 特殊非单客户端限制

        $uid = $userInfo['type'].'-'.$userInfo['user_id'].md5($token);

        // 查询已经的客户端
        $userClients = Gateway::getClientIdByUid($uid);

        echo "登录：{$clientId}----{$uid}\n";
        // 绑定最新的，把其他的都踢出
        Gateway::bindUid($clientId, $uid);
        if (!empty($userClients)) {
            foreach ($userClients as $existClientId) {
                $this->success($existClientId, '即将断开连接');
                Gateway::closeClient($existClientId);
            }
        }
        return $uid;
    }

    public function success($clientId = "", $message = "", $data = [], $code = 1, $type = "success") {
        $msg = $this->resultMsg($data, $code, $message, $type);
        zykLog("客户端：" . $clientId . "返回消息：" . $msg);
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
    public function error($clientId = "", $message = "", $data = [], $code = 0, $isClose = 1, $type = "error") {
        $msg = $this->resultMsg($data, $code, $message, $type);
        self::sendMsg($clientId, $msg);
        if ($isClose == 1) {
            Gateway::closeClient($clientId);
        }
        zykLog("客户端：" . $clientId . "返回消息：" . $msg);
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


