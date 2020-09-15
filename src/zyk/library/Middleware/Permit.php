<?php


namespace zyk\library\Middleware;


use zyk\library\Auth\AuthUser;
use zyk\library\traits\Jump;

abstract class Permit
{
    use Jump;

    /**
     * 特殊的权限判断
     * @author wxw 2020/9/15
     *
     * @param $rule
     * @param $rules
     * @return mixed
     */
    abstract function specialRulesCheck($rule, $rules);

    public function handle($request, \Closure $next) {
        if (ROLE_TYPE == ZYK_ADMINISTRATOR) {
            // 超级管理员不受限制
            return $next($request);
        }
        $rules = app(AuthUser::class)->getRules();
        // 接口验证权限
        $route = $request->routeInfo();
        if (empty($route['rule'])) {
            $this->jump(RESULT_ERROR, '接口访问失败');
        }
        $checkRules = $this->checkRoute($route['rule'], $rules);
        if (!$checkRules) {
            // 特殊情况处理（扩展）
            $res = $this->specialRulesCheck($route['rule'], $rules);
            if (!$res) {
                $this->jump(RESULT_ERROR, '无权操作');
            }
        }
        return $next($request);
    }

    /**
     * 验证是否有权访问
     * @author wxw 2020/9/15
     *
     * @param $action
     * @param $rules
     * @return bool
     */
    protected function checkRoute($action, $rules) {
        $routes = array_column($rules, 'route');
        if (in_array($action, $routes)) {
            return true;
        }
        return false;
    }
}