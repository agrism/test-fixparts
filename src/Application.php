<?php

namespace App;

use App\Command\AppCommand;
use App\Service\DataStore;
use App\Service\EntityManager;
use App\Service\LoggerService;
use App\Service\QuantityObserver;

class Application
{
    const DATA_DIR = '/var/';
    const STORE_FILE = 'data_store_file.data';
    const LOG_FILE = 'entity_update.log';

    private \Symfony\Component\Console\Application $application;
    private array $container;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $container = $this->mockInitContainer();

        /** @var AppCommand $command */
        $command = $container[AppCommand::class];

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

    /**
     * @throws \Exception
     */
    private function mockInitContainer(): array
    {
        $storePath = $this->getFilePath(self::STORE_FILE);
        $dataStore = new DataStore($storePath);

        $entityManager = new EntityManager($dataStore);

        $logPath = $this->getFilePath(self::LOG_FILE);
        $logger = new LoggerService($logPath);
        $entityManager->attach($logger);

        $quantityObserver = new QuantityObserver();
        $entityManager->attach($quantityObserver);

        $command = new AppCommand($entityManager);

        return [
            EntityManager::class => $entityManager,
            LoggerService::class => $logger,
            QuantityObserver::class => $quantityObserver,
            AppCommand::class => $command,
        ];
    }

    private function getFilePath(string $filename): string
    {
        return dirname(__DIR__) . self::DATA_DIR . $filename;
    }
}