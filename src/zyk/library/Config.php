<?php


namespace zyk\library;


class Config {

    protected $auth = '\\zyk\\library\\Auth\\AppAuth';

    protected $ruleSysLink = [
        ZYK_CONSULT => ZYK_SUPPLY_SYS,
        ZYK_DECLARE => ZYK_PRODECT_SYS,
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

    public function getConf($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

    public function getRoleName($roleType) {
        if (isset($this->roleName[$roleType])) {
            return $this->roleName[$roleType];
        }
        return false;
    }
}