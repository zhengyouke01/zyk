<?php

namespace zyk\library\Auth;


use zyk\library\ZykRequest;

class AppAuth extends Auth {

    public function checkAuth(ZykRequest $request) {
        return true;
    }
}