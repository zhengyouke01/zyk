<?php


namespace zyk\library\Log\driver;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use zyk\library\Log\ZykLog;
use zyk\library\Log\ZykLogInterface;

class Monolog implements ZykLogInterface {

    protected $fileName = 'default';
    protected $logPath = '';
    protected $level = 'info';
    protected $sys = '';

    public function logSys($sys) {
        $this->sys = $sys;
    }

    /**
     * 设置级别
     * @author wxw 2021/4/14
     *
     * @param $level
     */
    public function level($level) {
        $this->level = strtolower($level);
    }

    /**
     * 日志文件名称
     * @author wxw 2021/4/14
     *
     * @param $fileName
     */
    public function logFileName($fileName) {
        $this->fileName = $fileName;
    }

    /**
     * 日志路径
     * @author wxw 2021/4/14
     *
     * @param $logPath
     */
    public function logPath($logPath) {
        $this->logPath = $logPath;
    }

    /**
     * 存储日志
     * @author wxw 2021/4/14
     *
     * @param $msg
     */
    public function save($msg) {
        $logger = $this->setLog($this->fileName);
        $func = $this->level;
        if (is_callable([$logger, $func])) {
            $logger->$func($msg);
        } else {
            throw new \Exception("monolog func ${$func} not exist");
        }
    }

    /**
     * 获取日志计入的方式
     * @author 小贤
     * 2019-08-20
     * @param $name
     * @return bool|Logger
     */
    public function setLog($name, $module = '') {
        $logger = new Logger($this->sys.'_'.$name);
        $logPath = $this->logPath.$this->sys.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.date('Y-m');
        if (!is_dir($logPath)) {
            if (mkdir($logPath, 0777, true) === false) {
                return false;
            }
        }
        $logFileName = $logPath.DIRECTORY_SEPARATOR.$this->sys.'_'.$name.'_'.date('Y-m-d').'.log';
        $handler = new StreamHandler($logFileName, Logger::INFO);
        // 自定义格式
        $output = "[%datetime%]\t%level_name%\t%message%\t%context%\t%extra%\n";
        $formatter = new LineFormatter($output, 'Y-m-d H:i:s A', false, true);
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);
        return $logger;
    }
}