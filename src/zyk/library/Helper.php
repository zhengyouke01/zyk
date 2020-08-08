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