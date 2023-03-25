<?php

namespace App;

use App\Command\AppCommand;
use App\Service\EntityManager;
use App\Service\LoggerService;
use App\Service\QuantityObserver;

class Application
{
    const DATA_DIR = '/var/';
    const STORE_FILE = 'data_store_file.data';
    const LOG_FILE = 'entity_update.log';

    private \Symfony\Component\Console\Application $application;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * @throws \Exception
     */
    public function run(): void
    {
        $this->application->run();
    }

    /**
     * @throws \Exception
     */
    private function init(): void
    {
        $storePath = $this->getFilePath(self::STORE_FILE);
        $entityManager = new EntityManager($storePath);

        $logPath = $this->getFilePath(self::LOG_FILE);
        $logger = new LoggerService($logPath);
        $entityManager->attach($logger);

        $quantityObserver = new QuantityObserver();
        $entityManager->attach($quantityObserver);

        $command = new AppCommand($entityManager);

        $this->application = new \Symfony\Component\Console\Application();
        $this->application->add($command);
        $this->application->setDefaultCommand($command->getName(), true);
    }

    private function getFilePath(string $filename): string
    {
        return dirname(__DIR__) . self::DATA_DIR . $filename;
    }
}