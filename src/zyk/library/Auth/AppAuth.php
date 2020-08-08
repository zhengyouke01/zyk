<?php

namespace zyk\library\Auth;


use zyk\library\Request;

class AppAuth extends Auth {

    public function checkAuth(Request $request) {
        return true;
    }
}