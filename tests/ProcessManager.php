<?php
/*
 +----------------------------------------------------------------------+
 | Swoole                                                               |
 +----------------------------------------------------------------------+
 | Copyright (c) 2012-2017 The Swoole Group                             |
 +----------------------------------------------------------------------+
 | This source file is subject to version 2.0 of the Apache license,    |
 | that is bundled with this package in the file LICENSE, and is        |
 | available through the world-wide-web at the following url:           |
 | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 | If you did not receive a copy of the Apache2.0 license and are unable|
 | to obtain it through the world-wide-web, please send a note to       |
 | license@swoole.com so we can mail you a copy immediately.            |
 +----------------------------------------------------------------------+
 | Author: Tianfeng Han  <mikan.tenny@gmail.com>                        |
 +----------------------------------------------------------------------+
 */

namespace Seasx\SeasLogger\Tests;

use Swoole;
use swoole_atomic;

class ProcessManager
{
    public $parentFunc;
    public $childFunc;
    /**
     * @var swoole_atomic
     */
    protected $atomic;
    /**
     * wait wakeup 1s default
     */
    protected $waitTimeout = 1.0;
    protected $childPid;
    protected $childStatus = 255;
    protected $parentFirst = false;
    /**
     * @var Swoole\Process
     */
    protected $childProcess;

    public function __construct()
    {
        $this->atomic = new Swoole\Atomic(0);
    }

    //等待信息

    public function wakeup()
    {
        return $this->atomic->wakeup();
    }

    //唤醒等待的进程

    public function run($redirectStdout = false)
    {
        $this->childProcess = new Swoole\Process(function () {
            if ($this->parentFirst) {
                $this->wait();
            }
            $this->runChildFunc();
            exit;
        }, $redirectStdout, $redirectStdout);
        if (!$this->childProcess || !$this->childProcess->start()) {
            exit("ERROR: CAN NOT CREATE PROCESS\n");
        }
        register_shutdown_function(function () {
            $this->kill();
        });
        if (!$this->parentFirst) {
            $this->wait();
        }
        $this->runParentFunc($this->childPid = $this->childProcess->pid);
        Swoole\Event::wait();
        $waitInfo = Swoole\Process::wait(true);
        $this->childStatus = $waitInfo['code'];
        return true;
    }

    public function wait()
    {
        return $this->atomic->wait($this->waitTimeout);
    }

    public function runChildFunc()
    {
        return call_user_func($this->childFunc);
    }

    /**
     *  Kill Child Process
     * @param bool $force
     */
    public function kill(bool $force = false)
    {
        if (!defined('PCNTL_ESRCH')) {
            define('PCNTL_ESRCH', 3);
        }
        if ($this->childPid) {
            if ($force || (!@Swoole\Process::kill($this->childPid) && swoole_errno() !== PCNTL_ESRCH)) {
                if (!@Swoole\Process::kill($this->childPid, SIGKILL) && swoole_errno() !== PCNTL_ESRCH) {
                    exit('KILL CHILD PROCESS ERROR');
                }
            }
        }
    }

    public function runParentFunc($pid = 0)
    {
        if (!$this->parentFunc) {
            return (function () {
                $this->kill();
            })();
        } else {
            return call_user_func($this->parentFunc, $pid);
        }
    }

    public function getChildOutput()
    {
        $this->childProcess->setBlocking(false);
        while (1) {
            $data = @$this->childProcess->read();
            if (!$data) {
                sleep(1);
            } else {
                return $data;
            }
        }
    }

    /**
     * @param $data
     */
    public function setChildOutput($data)
    {
        $this->childProcess->write($data);
    }
}