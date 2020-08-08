<?php


namespace zyk\library;


class Config {

    protected $auth = '\\zyk\\library\\Auth\\AppAuth';

    public function getConf($name) {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

}