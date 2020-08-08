<?php


namespace zyk\library\Auth;


use zyk\library\Request;

abstract class Auth {

    abstract public function checkAuth(Request $request);
    
}