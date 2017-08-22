<?php

namespace Brendt\Pcntl;

/**
 * The manager is used to create and monitor child processes forked from a parent process. It uses sockets for
 * communication between the parent and children.
 *
 * Class Manager
 * @package Brendt\Pcntl
 */
class Manager
{
    /**
     * @return Manager
     */
    public static function create() : Manager {
        return new self();
    }

    /**
     * Check whether the pcntl extension is loaded and async processes can be supported.
     *
     * @return bool
     */
    public static function pcntlSupported() : bool {
        return extension_loaded('pcntl');
    }

    /**
     * Create an asynchronous process. The process itself will be returned for the caller process, and executed as a
     * child process of the caller. When the process is done executing, its output will be serialised and sent via a
     * socket to the caller process.
     *
     * @param Process $process
     *
     * @return Process
     */
    public function async(Process $process) : Process {
        socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $sockets);

        [$parentSocket, $childSocket] = $sockets;

        if (($pid = pcntl_fork()) == 0) {
            socket_close($childSocket);
            socket_write($parentSocket, serialize($process->execute()));
            socket_close($parentSocket);

            exit;
        }

        socket_close($parentSocket);

        return $process
            ->setStartTime(time())
            ->setPid($pid)
            ->setSocket($childSocket);
    }

    /**
     * Run a process in a synchronous way.
     *
     * @param Process $process
     *
     * @return Process
     */
    public function sync(Process $process) : Process {
        $output = $process->execute();
        $process->triggerSuccess();

        return $output;
    }

    /**
     * Instruct the caller process to wait for all its children to finish before finishing. This implementation will
     * wait for all children to finish before triggering any response.
     *
     * @param ProcessCollection $processCollection
     *
     * @return array
     */
    public function wait(ProcessCollection $processCollection) : array {
        $output = [];

        while (pcntl_waitpid(0, $status) != -1) {
            $status = pcntl_wexitstatus($status);
        }

        /** @var Process $process */
        foreach ($processCollection as $process) {
            $output[] = unserialize(socket_read($process->getSocket(), 4096));
            socket_close($process->getSocket());
        }

        return $output;
    }
}
