# zyk
证优客composer

##### 框架使用的相关配置（TP5.1）

项目目录下tags.php 配置示例
```php
<?php

define('BEHAVIOR_PATH', '\\zyk\\library\\Behavior\\');
return [
    // 应用初始化
    'app_init'     => [BEHAVIOR_PATH.'InitBase'],
    // 应用开始
    'app_begin'    => [],
    // 模块初始化
    'module_init'  => [],
    // 操作开始执行
    'action_begin' => [],
    // 视图内容过滤
    'view_filter'  => [],
    // 日志写入
    'log_write'    => [],
    // 应用结束
    'app_end'      => [],
];

```

项目配置文件配置：

```
'exception_handle'  => '\\zyk\\library\\Exception\\Http'
```

