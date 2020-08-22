<?php


namespace zyk\library;


class Config {

    protected $sysTags = null;

    // 主要角色标记（ 管理员、咨询师、销售等）
    protected $roleTags = null;

    public function __construct() {
        $tags = include __DIR__.'/SysteamTag.php';
        if ($tags) {
            $this->sysTags = $tags['sys_tags'];
            $this->roleTags = $tags['admin_user_info_type'];
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
        ZYK_PROJECT_ASSISTANT => ZYK_SUPPLY_SYS
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
        ZYK_ORGANIZE => '审核机构',
        ZYK_MANAGE => '行政人员'
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