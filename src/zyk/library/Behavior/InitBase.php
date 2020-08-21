<?php

namespace zyk\library\Behavior;
use think\App;
use think\Container;
use think\Loader;
use think\Db;
use think\Request;

/**
 * 初始化基础信息行为
 */
class InitBase{

    protected $app ;

    protected $tags = null;

    public function __construct(App $app) {
        $this->app = $app ?: Container::get('app');

        if (is_null($this->tags)) {
            $tags = include __DIR__.'/../SysteamTag.php';
            $this->tags = $tags;
        }
    }

    /**
     * 初始化行为入口
     */
    public function run(){

        // 初始化常量
        $this->initConst();

        // 初始化配置
        $this->initConfig();

    }

    /**
     * 初始化常量
     */
    private function initConst(){

        // 初始化目录常量
        $this->initDirConst();

        // 初始化结果常量
        $this->initResultConst();

        // 初始化数据状态常量
        $this->initDataStatusConst();

        // 初始化时间常量
        $this->initTimeConst();

        // 初始化系统常量
        $this->initSystemConst();

        // 初始化路径常量
        $this->initPathConst();

        // 初始化会员角色常量
        $this ->initUserRole();

        // 初始化项目系统常量
        $this->initProductConst();

        // 初始化管理员身份级别
        $this->initAdminItemLevel();
    }

    /**
     * 初始化用户身份级别
     * @author wxw 2020/8/19
     *
     */
    private function initAdminItemLevel() {
        if ($this->tags) {
            array_walk($this->tags['identity_level'], function ($tag, $key) {
                defined($tag) or  define($tag, $key) ;
            });
            // 后台用户常量的载入
            array_walk($this->tags['admin_user_info_type'], function ($tag, $key) {
                defined($tag) or  define($tag, $key) ;
            });
        }
    }

    /**
     * 初始化系统标识
     * @author wxw 2020/8/18
     *
     */
    private function initProductConst() {
        if ($this->tags) {
            array_walk($this->tags['sys_tags'], function ($tag, $key) {
                defined($tag) or  define($tag, $key) ;
            });
        }

    }

    /**
     * 初始化角色常量
     */
    private function initUserRole(){
        if ($this->tags) {
            // 用户角色常量载入
            array_walk($this->tags['user_roles'], function ($tag, $key) {
                defined($tag) or  define($tag, $key) ;
            });
        }
    }


    /**
     * 初始化目录常量
     */
    private function initDirConst(){
        define('SYS_CONTROLLER_NAME', 'controller');
        define('SYS_LOGIC_NAME'     , 'logic');
        define('SYS_MODEL_NAME'     , 'model');
        define('SYS_SERVICE_NAME'   , 'service');
        define('PUBLIC_PATH', ROOT_PATH .'public'. DS);  // 增加public目录

    }

    /**
     * 初始化结果常量
     */
    private function initResultConst(){

        define('RESULT_SUCCESS' , 'success');
        define('RESULT_ERROR'   , 'error');
        define('RESULT_REDIRECT', 'redirect');
        define('RESULT_MESSAGE' , 'message');
        define('RESULT_URL'     , 'url');
        define('RESULT_DATA'    , 'data');
        define('RESULT_CODE',     'result');
    }

    /**
     * 初始化数据状态常量
     */
    private function initDataStatusConst(){

        define('DATA_COMMON_STATUS' ,  'status');
        define('DATA_NORMAL'        ,  1);
        define('DATA_DISABLE'       ,  0);
        define('DATA_DELETE'        , -1);
        define('DATA_SUCCESS'       , 1);
        define('DATA_ERROR'         , 0);
    }

    /**
     * 初始化时间常量
     */
    private function initTimeConst(){

        define('TIME_CT_NAME' ,  'create_time');
        define('TIME_UT_NAME' ,  'update_time');
        define('TIME_NOW'     ,   time());
    }

    /**
     * 初始化系统常量
     */
    private function initSystemConst(){

        define('SYS_APP_NAMESPACE'              , config('app_namespace'));
        define('SYS_COMMON_DIR_NAME'            , 'common');
        define('SYS_STATIC_DIR_NAME'            , 'static');
        define('SYS_VERSION'                    , '1.0.0');
        define('SYS_ADMINISTRATOR_ID'           , 1);
        define('SYS_DSS'                        , '/');
    }

    /**
     * 初始化路径常量
     */
    private function initPathConst(){

        // 定义URL
        if (!defined('WEB_URL')) {
            $url = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
            define('WEB_URL', (('/' == $url || '\\' == $url) ? '' : $url));
        }

        //定义域名信息
        if(!defined('SITE_URL')) {
            $http = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
            #define('SITE_URL', $http.'://'.$_SERVER['HTTP_HOST']);
            if (!empty($_SERVER['HTTP_HOST'])) {
                define('SITE_URL', $http.'://'.$_SERVER['HTTP_HOST']);
                define('HTTP_HOST', $_SERVER['HTTP_HOST']);
            } else {
                define('SITE_URL', '');
                define('HTTP_HOST','');
            }

        }

    }

    /**
     * 初始化配置信息
     */
    private function initConfig(){

        //将数据库中的config配置全部读出来  todo
        //动态配置
    }

}
