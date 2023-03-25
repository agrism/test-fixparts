<?php

namespace App;

use App\Command\AppCommand;

class Application
{
    private \Symfony\Component\Console\Application $application;

    public function __construct()
    {
        $command = new AppCommand();

        $this->application = new \Symfony\Component\Console\Application();
        $this->application->add($command);
        $this->application->setDefaultCommand($command->getName(), true);
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->application->run();
    }
}