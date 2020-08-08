<?php


namespace zyk\library\Auth;


use zyk\library\ZykRequest;

abstract class Auth {

    abstract public function checkAuth(ZykRequest $request);


    
}