<?php
namespace zyk\library\Log;

class Log extends \think\Facade {

    /**
     * 日志Facade
     * @author wxw 2021/4/2
     *
     * @return string
     */
    static public function getFacadeClass() {
        return "\\zyk\\library\\log\\PlatLog";
    }

}