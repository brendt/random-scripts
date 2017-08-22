# pcntl-wrapper

This library adds a few wrapper classes around `ext-pcntl` to help manage forked processes and fallbacks when 
`ext-pcntl` is not supported.

### Simple example

In this example, a bunch of processes are created based on a set of `$things`.

First a `CallbackProcess` is made, which is a simple implementation of `Brendt\Pcntl\Process`. This process takes a 
callback which is executed as a child process (if supported).

The `$processCollection` holds a collection of all processes the manager will have to wait for after all childres have
been started.

If `ext-pcntl` is not supported, a fallback is provided to run each process synchronous.

```php
<?php

use Brendt\Pcntl\CallbackProcess;
use Brendt\Pcntl\Manager;
use Brendt\Pcntl\Process;
use Brendt\Pcntl\ProcessCollection;

$manager = new Manager();
$pcntlSupported = Manager::pcntlSupported();
$processCollection = new ProcessCollection();

foreach ($things as $thing) {
    $process = new CallbackProcess(function (Process $process) {
        sleep(5);
        return $process->getPid();
    });
        
    if ($pcntlSupported) {
        $processCollection[] = $manager->async($process);
    } else {
        $manager->sync($process);
    }
}

if ($pcntlSupported) {
    $manager->wait($processCollection);
}
```

### Full example

This example shows more advanced process configuration, and also an event trigger when the process is done. Furthermore 
it uses the `AsyncManager` instead of the normal `Manager`, which will trigger process successes individually, instead of
waiting for all processes to be done.

```php
<?php

use Brendt\Pcntl\AsyncManager;
use Brendt\Pcntl\CallbackProcess;
use Brendt\Pcntl\Process;
use Brendt\Pcntl\ProcessCollection;

$manager = new AsyncManager();
$pcntlSupported = AsyncManager::pcntlSupported();
$processCollection = new ProcessCollection();

foreach ($things as $thing) {
    $process = (new CallbackProcess(function (Process $process) use ($thing) {
            sleep(5);
            return $thing->getName();
        }))
        ->setName($thing->getId())
        ->setMaxRunTime(100)
        ->onSuccess(function (Process $process) use ($thing) {
            $this->eventDispatcher->dispatch('event.name', ['thing' => $thing]);
        });
        
    if ($pcntlSupported) {
        $processCollection[] = $manager->async($process);
    } else {
        $manager->sync($process);
    }
}

if ($pcntlSupported) {
    $manager->wait($processCollection);
}
```

### Custom process

Instead of using the `CallbackProcess`, you can implement your own processes.

```php
<?php

use Brendt\Pcntl\Process;

class MyProcess extends Process
{
    public function execute() {
        sleep(10);
        
        return $this->getPid();
    }
}

// ...

$myProcess = new MyProcess(/* ... */);
$processCollection[] = $manager->async($myProcess);
```
