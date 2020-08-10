<?php


namespace zyk\library;

use think\App;


class BaseValidate extends \think\validate{

    /**
     * 验收手机号码
     * @author YYNOEL 2020/6/29
     * @param $mobile
     * @return bool
     */
    public function checkMobile($mobile) {
        if(preg_match('/1[3456789]\d{9}$/',$mobile))
            return true;
        return false;
    }

    /**
     * 检测字符串长度
     * @author GJQ 2020-04-02
     *
     * @param $data
     *
     * @return bool
     */
    public function checkStrLength($data) {
        $str = preg_replace("/[\x{4e00}-\x{9fa5}]/u", '**', $data);
        if (strlen($str) > 12) {
            return false;
        }
        return true;
    }
    
}
