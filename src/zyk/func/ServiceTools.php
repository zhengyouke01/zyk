<?php
declare(strict_types = 1);
/**
 * 校验字符表情
 * @author LYJ 2020.06.16
 * @param string $str
 * @return bool
 */
function zyk_check_emoji(string $str) {
    $mat = [];
    preg_match_all('/./u', $str,$mat);
    foreach ($mat[0] as $v){
        if(strlen($v) > 3){
            return false;
        }
    }
    return true;
}

/**
 * 验证存数字
 * @param $mobile
 * @return bool
 */
function zyk_check_number(string $mobile) {
    if(preg_match('/^[0-9]*$/',$mobile))
        return true;
    return false;
}

/**
 * 检查固定电话
 * @param $mobile
 * @return bool
 */
function zyk_check_telephone(string $mobile) {
    if(preg_match('/^([0-9]{3,4}-)?[0-9]{7,8}$/',$mobile))
        return true;
    return false;
}

/**
 * 检查邮箱地址格式
 * @param $email
 * @return bool
 */
function zyk_check_email(string $email) {
    if(filter_var($email,FILTER_VALIDATE_EMAIL))
        return true;
    return false;
}

/**
 * 验证是否是手机号
 * @author 小贤 2019/12/12
 * @param $mobile 需要判断的手机号
 * @return bool
 */
function zyk_check_mobile(string $mobile) {
    if(preg_match('/1[23456789]\d{9}$/',$mobile))
        return true;
    return false;
}


/**
 * 今日的开始结束时间
 *
 * @author wxw 2020/2/4
 *
 */
function zyk_todayTime() {
    $stime = mktime(0,0,0,intval(date('m')),intval(date('d')),intval(date('Y')));
    $etime = mktime(0,0,0,intval(date('m')),intval(date('d'))+1,intval(date('Y')))-1;
    return ['start_time' => $stime, 'end_time' => $etime];
}


/**
 * 获取制定时间的月开始和结束时间
 *
 * @author wxw 2020/1/11
 *
 * @param $time
 *
 * @return array
 */
function zyk_monthTime($time) {
    $stime = mktime(0, 0, 0, intval(date('m', $time)), 1,  intval(date('Y', $time)));
    $etime = mktime(23, 59, 59, intval(date('m', $time)),  intval(date('t', $time)),  intval(date('Y', $time)));
    return ['start_time' => $stime, 'end_time' => $etime];
}


/**
 * @author LYJ 2020.09.09
 * 获取短链接 -- 业务函数
 */
if(!function_exists('zyk_short_url')) {
    function zyk_short_url($data) {
        $redis = redis();
        $prefix = "s:applyTable:shortUrl:order_no:";
        $urlString = $redis->get($prefix.$data['order_no']);
        if (empty($urlString)) {
            $urlString = create_sign("order_no=".$data['order_no']);
            $redis->set($prefix.$data['order_no'], $urlString);
            $redis->set("s:applyTable:shortUrl:".$urlString, $data['order_no']);
        }
        return ['short_string' => $urlString];
    }
}

/**创建短网址标识
 * @modify 更改短标签生产方式 YYNOEL 2019-12-21
 * @param $sign
 * @return array
 */
if (!function_exists('create_sign')) {
    function create_sign($sign){
        $base32 = array (
                         'a', 'b', 'c', 'd', 'e', 'f',
                         'g', 'h', 'i', 'j', 'k', 'l',
                         'm', 'n', 'o', 'p', 'q', 'r',
                         's', 't', 'u', 'v', 'w', 'x',
                         'y', 'z', '0', '1', '2', '3',
                         '4', '5', '6', '7', '8', '9'
                         );
        $hex = md5($sign);
        $hexLen = strlen($hex);
        $subHexLen = $hexLen / 8;
        $output = array();
        for ($i = 0; $i < $subHexLen; $i++) {
            $subHex = substr ($hex, $i * 8, 8);
            $int = 0x3FFFFFFF & hexdec($subHex);
            $out = '';
            for ($j = 0; $j < 6; $j++) {
                $val = 0x0000001F & $int;
                $out .= $base32[$val];
                $int = $int >> 5;
            }
            $output[] = $out;
        }
        return $output[0];
    }
}
