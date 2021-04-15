<?php


namespace zyk\library\Log\factory;


use think\Exception;
use zyk\library\Log\driver\Monolog;
use zyk\library\Log\LogFactoryInterface;

class MonologFactory implements LogFactoryInterface {

    private $driver = null;

    public function __construct($config = []) {
        if (!$this->driver || !($this->driver instanceof Monolog)) {
            $this->driver = new Monolog();
        }
        if (isset($config['file_name'])) {
            $this->driver->logFileName($config['file_name']);
        } else {
            throw new Exception('log config file_name exist');
        }

        if (isset($config['log_path'])) {
            $this->driver->logPath($config['log_path']);
        } else {
            throw new Exception('log config log_path exist');
        }
    }

    /**
     * 获取写入器
     * @author wxw 2021/4/14
     *
     * @param $fileName
     * @param $logPath
     * @return Monolog
     */
    public function getDriver($level) {
        $this->driver->level($level);
        return $this->driver;
    }
}