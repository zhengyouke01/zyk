<?php

return [
    // 所有系统标识
    'sys_tags' => [
        1 => 'ZYK_SUPPLY_SYS', // 供应链系统
        2 => 'ZYK_PRODECT_SYS',  // 项目系统
        3 => 'ZYK_SERVICE_SYS',  // 服务系统
        4 => 'ZYK_FINANCE_SYS',  // 财务系统
        5 => 'ZYK_OPERATION_SYS',  // 运维系统
    ],
    // 所有用户身份标识
    'user_roles' => [
        1 => 'ZYK_ADMINISTRATOR', // 管理员/超级管理员
        2 => 'ZYK_SALE', // 销售
        3 => 'ZYK_CONSULT', // 咨询老师
        4 => 'ZYK_ORGANIZE', // 审核机构
        5 => 'ZYK_MANAGER', // 客户经理
        6 => 'ZYK_AREA_MANAGER', // 区域经理
        7 => 'ZYK_DECLARE', // 申报人员
        8 => 'ZYK_CUSTOMER', // 客服
        9 => 'ZYK_FINANCE', // 财务
        10 => 'ZYK_PRESALE', // 售前
        11 => 'ZYK_MANAGE', // 行政人员
        12 => 'ZYK_PROJECT_ASSISTANT' // 项目助理
    ],
    // 身份等级标识
    'identity_level' => [
        1 => 'ZYK_LEVEL_MANAGER', // 经理
        2 => 'ZYK_LEVEL_SUPERVISOR', // 主管
        3 => 'ZYK_LEVEL_PERSONNEL' // 普通员工
    ],
    // 后台用户类型表识
    'admin_user_info_type' => [
        'admin' => 'ZYK_ADMIN_USER_ADMIN', // 内部后台用户
        'sale' => 'ZYK_ADMIN_USER_SALE', // 销售
        'consult' => 'ZYK_ADMIN_USER_CONSULT' // 咨询师
    ],
];