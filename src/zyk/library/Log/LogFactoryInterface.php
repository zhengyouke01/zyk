<?php


namespace zyk\library\Log;


interface LogFactoryInterface {

    public function getDriver($level);

}