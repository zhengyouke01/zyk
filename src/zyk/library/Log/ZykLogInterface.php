<?php


namespace zyk\library\Log;


interface ZykLogInterface {

    public function level($level);

    public function logFileName($fileName);

    public function logPath($logPath);

    public function save($msg);
}