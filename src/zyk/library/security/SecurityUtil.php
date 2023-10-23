<?php

namespace zyk\library\security;

use think\Loader;

class SecurityUtil
{
    protected $securityConf;//算法配置
    protected $securityDriver;//算法驱动
    private $object;

    public function __construct($conf = [])
    {
        $this->securityConf = config('app.security');
        $this->securityDriver = $this->securityConf['driver_class'];
        if (!empty($conf)) {
            $this->securityConf = $conf;
            $this->securityDriver = $this->securityConf['driver_class'];
        }
        $this->object = Loader::factory($this->securityDriver, '\\zyk\\library\\security\\driver\\', $this->securityConf);
    }

    /**
     * 加密
     * @param $data
     * @param $key //无须秘钥则不传
     */
    public function encrypt($data, $key = '')
    {
        return $this->object->encrypt($data, $key);
    }

    /**
     * 解密
     * @param $data
     * @param $key //无须秘钥则不传
     * @return false|string
     */
    public function decrypt($data, $key = '')
    {
        return $this->object->decrypt($data, $key);
    }

    /**
     * 获取密钥
     * @param $kid //唯一标识
     * @return false|string
     */
    public function getKey($kid = 0)
    {
        return $this->object->getKey($kid);
    }
}

