<?php

namespace zyk\library\security;

use think\Facade;

class Security extends Facade {

    static function getFacadeClass() {
        return "\\zyk\\library\\security\\SecurityUtil";
    }

}
