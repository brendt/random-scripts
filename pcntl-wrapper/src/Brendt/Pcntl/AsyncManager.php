<?php

namespace Brendt\Pcntl;

/**
 * The AsyncManager provides an extension to the Manager by adding the possibility to hook into each process individually.
 * This way, feedback to the parent process can be sent on a child-process basis, and not after all children are finished.
 *
 * Class AsyncManager
 * @package Brendt\Pcntl
 */
class AsyncManager extends Manager
{
    /**
     * This implementation of `wait` will be able get the status of each child process individually, and call the
     * `success` callback (almost) immediately when a child process is finished, instead of waiting for all  children to
     * be finished.
     *
     * @param ProcessCollection $processCollection
     *
     * @return array
     * @throws \Exception
     *
     * @see \Brendt\Pcntl\Process::onSuccess()
     * @see \Brendt\Pcntl\Process::triggerSuccess()
     */
    public function wait(ProcessCollection $processCollection) : array {
        $output = [];
        $passes = 1;
        $processes = $processCollection->toArray();

        while (count($processes)) {
            /** @var Process $process */
            foreach ($processes as $key => $process) {
                $processStatus = pcntl_waitpid($process->getPid(), $status, WNOHANG | WUNTRACED);

                if ($processStatus == $process->getPid()) {
                    $output[] = unserialize(socket_read($process->getSocket(), 4096));
                    socket_close($process->getSocket());
                    $process->triggerSuccess();

                    unset($processes[$key]);
                } else if ($processStatus == 0) {
                    if ($process->getStartTime() + $process->getMaxRunTime() < time() || pcntl_wifstopped($status)) {
                        if (!posix_kill($process->getPid(), SIGKILL)) {
                            throw new \Exception("Failed to kill {$process->getPid()}: " . posix_strerror(posix_get_last_error()));
                        }

                        unset($processes[$key]);
                    }
                } else {
                    throw new \Exception("Could not reliably manage process {$process->getPid()}");
                }
            }

            if (!count($processes)) {
                break;
            }

            ++$passes;
            usleep(100000);
        }

        return $output;
    }

}
