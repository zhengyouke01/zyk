<?php


namespace zyk\library;


class Config {

    protected $sysTags = null;

    // 主要角色标记（ 管理员、咨询师、销售等）
    protected $userTypeTags = null;

    public function __construct() {
        $tags = include __DIR__.'/SysteamTag.php';
        if ($tags) {
            $this->sysTags = $tags['sys_tags'];
            $this->userTypeTags = $tags['admin_user_info_type'];
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
        ZYK_MANAGE => ZYK_PRODECT_SYS,
        ZYK_PRESALE => ZYK_SERVICE_SYS,
        ZYK_PROJECT_ASSISTANT => ZYK_SUPPLY_SYS,
        ZYK_RISK => ZYK_SUPPLY_SYS,
        ZYK_OPERATION => ZYK_OPERATION_SYS,
        ZYK_RENEW => ZYK_SERVICE_SYS
    ];

    /**
     * 角色对应名称
     * @var string[]
     */
    protected $roleName = [
        ZYK_SALE => '销售',
        ZYK_CONSULT => '咨询',
        ZYK_CUSTOMER => '商务',
        ZYK_DECLARE => '申报',
        ZYK_FINANCE => '财务',
        ZYK_PRESALE => '售前',
        ZYK_ADMINISTRATOR => '管理员',
        ZYK_ORGANIZE => '审核机构',
        ZYK_MANAGE => '行政',
        ZYK_PROJECT_ASSISTANT => '助理',
        ZYK_RISK => '风控',
        ZYK_OPERATION => '运维',
        ZYK_RENEW => '续签',
        ZYK_ASK => '问询',
        ZYK_EDIT => '修改员',
        ZYK_CONSULT_ASSISTANT => '咨询助理',
        ZYK_INTERPRETER => '口译',
        ZYK_PROJECT_DEVELOP => '项目开发',
        ZYK_ATM => 'ATM',
        ZYK_QA => 'QA',
        ZYK_DOC_ASSISTANT => '文档助理'
    ];

    /**
     * 身份等级对应名称
     * @var string[]
     */
    protected $identityLevelName = [
        ZYK_LEVEL_MANAGER => '区域经理',
        ZYK_LEVEL_SUPERVISOR => '员工主管',
        ZYK_LEVEL_PERSONNEL => '一般员工',
    ];

    /**
     * 系统标识对应的名称
     * @var string[]
     */
    protected $sysName = [
        ZYK_SUPPLY_SYS => '供应链管理系统',
        ZYK_PRODECT_SYS => '交付管理系统',
        ZYK_SERVICE_SYS => '商务管理系统',
        ZYK_FINANCE_SYS => '财务结算系统',
        ZYK_OPERATION_SYS => '运维监控管理系统',
        ZYK_TASK_SYS => '工单系统',
    ];


    /**
     * 角色对应用户表
     * @var array
     */
    protected $roleTabelName = [
        ZYK_SALE => ZYK_ADMIN_USER_SALE,
        ZYK_CONSULT => ZYK_ADMIN_USER_CONSULT,
        ZYK_CUSTOMER => ZYK_ADMIN_USER_ADMIN,
        ZYK_DECLARE => ZYK_ADMIN_USER_ADMIN,
        ZYK_FINANCE => ZYK_ADMIN_USER_ADMIN,
        ZYK_PRESALE => ZYK_ADMIN_USER_ADMIN,
        ZYK_ADMINISTRATOR => ZYK_ADMIN_USER_ADMIN,
        ZYK_ORGANIZE => ZYK_ADMIN_USER_ADMIN,
        ZYK_MANAGE => ZYK_ADMIN_USER_ADMIN,
        ZYK_RISK => ZYK_ADMIN_USER_ADMIN,
        ZYK_OPERATION => ZYK_ADMIN_USER_ADMIN,
        ZYK_RENEW => ZYK_ADMIN_USER_ADMIN
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
        return $this->getVal('roleName', $roleType); // 兼容之前的写法
    }

    /**
     * 获取系统标示
     * @author wxw 2020/8/18
     *
     * @param $sys
     * @return bool|mixed
     */
    public function getSys($sys) {
        return $this->getVal('sysTags', $sys); // 兼容之前的写法
    }

    /**
     * 获取配置的数
     * @author wxw 2020/8/19
     *
     * @param $var
     * @param $field
     * @return bool
     */
    public function getVal($var, $field) {
        if (!isset($this->$var)) {
             return false;
        }
        $data = $this->$var;
        if (isset($data[$field])) {
            return $data[$field];
        }
        return false;
    }

    /**
     * 获取系统对应个的角色
     * @author wxw 2020/8/22
     *
     * @param $sys
     * @return array
     */
    public function getSysRole($sys) {
        $role = [];
        array_walk($this->ruleSysLink, function ($ruleSys, $key) use (&$role, $sys) {
            if ($ruleSys == $sys) {
                $role[] = $key;
            }
        });
        return $role;
    }
}
