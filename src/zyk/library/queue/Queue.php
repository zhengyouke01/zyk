<?php


namespace zyk\library\queue;


use think\Facade;

class Queue extends Facade {

    static function getFacadeClass() {
        return "\\zyk\\library\\queue\\ZykQueue";
    }

}
