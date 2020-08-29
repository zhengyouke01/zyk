<?php


namespace zyk\library\Auth;


class AuthUser {

    /**
     * 权限存储
     * @var null
     */
    protected $rules = null;

    /**
     * 用户信息
     * @var null
     */
    protected $userInfo = [];

    /**
     * 设置权限
     * @author wxw 2020/8/29
     *
     * @param $rules
     */
    public function setRules($rules) {
        $this->rules = $rules;
    }

    /**
     * 获取权限
     * @author wxw 2020/8/29
     *
     * @return null
     */
    public function getRules() {
        return $this->rules;
    }

    /**
     * 设置用户信息
     * @author wxw 2020/8/29
     *
     * @param $userInfo
     */
    public function setUserInfo($userInfo) {
        $this->userInfo = array_merge($this->userInfo, $userInfo);
    }

    /**
     * 获取所有的用户信息
     * @author wxw 2020/8/29
     *
     */
    public function getUserInfo() {
        return $this->userInfo;
    }

    /**
     * 魔术变量，如果变量不存在，则去uesrInfo内寻找
     * @author wxw 2020/8/29
     *
     * @param $name
     * @return mixed|null
     */
    public function __get($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        // 获取用户信息的内容
        if (isset($this->userInfo[$name])) {
            return $this->userInfo[$name];
        }
        return null;
    }
}