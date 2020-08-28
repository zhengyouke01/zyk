<?php
namespace zyk\tools;

use think\facade\Session;
use think\Request;
use think\Url;
use zyk\extend\geetest\GeetestLib;

class Geetest implements BaseInterface {


    public function serviceInfo() {
        return ['service_name' => '极验验证类', 'service_class' => 'Geetest', 'service_describe' => '极验验证类', 'author' => 'wxw', 'version' => '1.0'];
    }

    private static $_instance = NULL;

    private $geetest_lib ;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $this->geetest_lib = new GeetestLib(config('app.geetest')['captcha_id'], config('app.geetest')['private_key']);
    }

    public function markCode($type, $userId = 0)
    {
        if (empty($userId)) {
            $userId = Session::get('login_id');
            if (empty($userId)) {
                $userId = md5(TIME_NOW.'login_id');
            }
        }
        $data = array(
            "user_id" => $userId, # 网站用户id
            "client_type" => $type,
            "ip_address" => request()->ip(),
        );
        $status = $this->geetest_lib->pre_process($data);
        Session::set('gtserver', $status);
        Session::set('login_id', $data['user_id']);
        return $this->geetest_lib->get_response_str();
    }

    public function validate_geetest($type, $post_fields)
    {
        $data = array(
            "user_id" => Session::get('login_id'), # 网站用户id
            "client_type" => $type,
            "ip_address" =>  request()->ip(),
        );

        if (Session::get('gtserver') == 1) {   //服务器正常
            return $this->geetest_lib->success_validate($post_fields['geetest_challenge'], $post_fields['geetest_validate'], $post_fields['geetest_seccode'], $data);
        }else{  //服务器宕机,走failback模式
            return $this->geetest_lib->fail_validate($post_fields['geetest_challenge'],$post_fields['geetest_validate'],$post_fields['geetest_seccode']);
        }
    }


}
