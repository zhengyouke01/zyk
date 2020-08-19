<?php


namespace zyk\library;


class Config {

    protected $sysTags = null;

    public function __construct() {
        $tags = include __DIR__.'/SysteamTag.php';
        if ($tags) {
            $this->sysTags = $tags['sys_tags'];
        }
    }


    protected $auth = '\\zyk\\library\\Auth\\AppAuth';

    protected $ruleSysLink = [
        ZYK_SALE => ZYK_SERVICE_SYS,
        ZYK_CONSULT => ZYK_PRODECT_SYS,
        ZYK_DECLARE => ZYK_PRODECT_SYS,
        ZYK_CUSTOMER => ZYK_SERVICE_SYS,
        ZYK_FINANCE => ZYK_FINANCE_SYS,
        ZYK_PRESALE => ZYK_SERVICE_SYS,
        ZYK_MANAGE => ZYK_PRODECT_SYS
    ];
    
    /**
     * 角色对应名称
     * @var string[]
     */
    protected $roleName = [
        ZYK_SALE => '销售',
        ZYK_CONSULT => '咨询师',
        ZYK_CUSTOMER => '客服',
        ZYK_DECLARE => '申报',
        ZYK_FINANCE => '财务',
        ZYK_PRESALE => '售前',
        ZYK_ADMINISTRATOR => '管理员',
        ZYK_ORGANIZE => '审核机构'
    ];

    /**
     * 获取配置内容
     * @author wxw 2020/8/18
     *
     * @param $name
     * @return |null
     */
    public function getConf($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

    /**
     * 获取系统角色的中文
     * @author wxw 2020/8/18
     *
     * @param $roleType
     * @return bool|string
     */
    public function getRoleName($roleType) {
        if (isset($this->roleName[$roleType])) {
            return $this->roleName[$roleType];
        }
        return false;
    }

    /**
     * 获取系统标示
     * @author wxw 2020/8/18
     *
     * @param $sys
     * @return bool|mixed
     */
    public function getSys($sys) {
        if (isset($this->sysTags[$sys])) {
            return $this->sysTags[$sys];
        }
        return false;
    }

}