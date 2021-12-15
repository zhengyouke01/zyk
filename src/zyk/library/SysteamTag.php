<?php

return [
    // 所有系统标识
    'sys_tags' => [
        1 => 'ZYK_SUPPLY_SYS', // 供应链系统
        2 => 'ZYK_PRODECT_SYS',  // 项目系统
        3 => 'ZYK_SERVICE_SYS',  // 服务系统
        4 => 'ZYK_FINANCE_SYS',  // 财务系统
        5 => 'ZYK_OPERATION_SYS',  // 运维系统
        6 => 'ZYK_TASK_SYS', // 工单系统
        7 => 'ZYK_BUTLER_SYS', // 优证管家小程序系统
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
        12 => 'ZYK_PROJECT_ASSISTANT', // 项目助理
        13 => 'ZYK_RISK', // 风控角色
        14 => 'ZYK_OPERATION', // 运维人员
        15 => 'ZYK_RENEW', // 续签
        16 => 'ZYK_ASK', // 订单问询
        17 => 'ZYK_EDIT', // 订单修改员
        18 => 'ZYK_CONSULT_ASSISTANT', // 咨询助理
        19 => 'ZYK_INTERPRETER', // 口译
        20 => 'ZYK_PROJECT_DEVELOP', // 项目开发
        21 => 'ZYK_ATM', // ATM
    ],
    // 身份等级标识
    'identity_level' => [
        1 => 'ZYK_LEVEL_MANAGER', // 经理
        2 => 'ZYK_LEVEL_SUPERVISOR', // 主管
        3 => 'ZYK_LEVEL_PERSONNEL' // 普通员工
    ],
    // 后台用户类型表识
    'admin_user_info_type' => [
        'super_admin' => 'ZYK_ADMIN_SUPER_ADMIN', // 超级管理员
        'admin' => 'ZYK_ADMIN_USER_ADMIN', // pc用户
        'sale' => 'ZYK_ADMIN_USER_SALE', // 销售
        'consult' => 'ZYK_ADMIN_USER_CONSULT' // 咨询师
    ],
    'flow_unit' => [
        1 => 'FLOW_WAITING_ORDER', // 待接单
        2 => 'FLOW_IN_SERVICE', // 服务中
        3 => 'FLOW_FINISH_CERT', // 已出证
        4 => 'FLOW_FIRST_VISIT', // 首访
        5 => 'FLOW_CONFIRM_INFO', // 确认资料
        6 => 'FLOW_RECEIVE_ORDER', // 接单
        7 => 'FLOW_REJECT_ORDER', // 拒单
        8 => 'FLOW_UPLOAD_DECLARE_FILE', // 上传申报文件
        9 => 'FLOW_COMMIT_AGENCY', // 提交机构
        10 => 'FLOW_PLAN_VERIFY', // 安排审核
        11 => 'FLOW_ISSUE_CERT', // 出证
        12 => 'FLOW_IMPROVE_INFO', // 完善资料
        13 => 'FLOW_CHANGE_APPLICATION', // 修改申请表
        14 => 'FLOW_PLACE_FILE', // 文件整改归档
        15 => 'FLOW_COMFIRM_VERIFY', // 确认审核安排
        16 => 'FLOW_VERIFY_COMMUNICATE', // 确认审核沟通
        17 => 'FLOW_COMFIRM_FILE', // 确认档案袋
        18 => 'FLOW_UPLOAD_CERT', // 上传电子正式
        19 => 'FLOW_MAIL_CERT', // 邮寄证书
        20 => 'FLOW_RECEIVE_CERT', //领取证书原件
        21 => 'FLOW_BEOVER', // 完结
        22 => 'FLOW_REVIEWABLE',//可排审reviewable
        23 => 'FLOW_REGISTER',//注册register
        24 => 'FLOW_SAMPLING',//抽样sampling
        25 => 'FLOW_ESTIMATE_PLAN',//安排评估计划estimate_plan
        26 => 'FLOW_ANNOUNCE',//公示announce
    ]
];