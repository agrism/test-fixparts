<?php

namespace App\Command;

use App\Entity\Entity;
use App\Entity\InventoryItem;
use App\Service\EntityManager;
use App\Service\LoggerService;
use App\Service\QuantityObserver;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:run',
)]
class AppCommand extends Command
{
    const DATA_STORE_PATH = 'data_store_file.data';

    private EntityManager $entityManager;

    public function __construct()
    {
        $storePath = dirname(__DIR__, 2) . '/var/' . self::DATA_STORE_PATH;

        $this->entityManager = new EntityManager($storePath);

        $logger = new LoggerService();
        $this->entityManager->attach($logger);

        $quantityObserver = new QuantityObserver();
        $this->entityManager->attach($quantityObserver);

        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            list($item1, $item2, $item3, $item4, $item5) = $this->createInventoryItems();

            $item1->itemsReceived(4);
            $item2->itemsReceived(2);
            $item3->itemsReceived(12);
            $item4->itemsReceived(20);
            $item5->itemsReceived(3);

            $item3->itemsHaveShipped(5);
            $item4->itemsHaveShipped(16);

            $item4->changeSalePrice(0.87);
            $item5->changeSalePrice(5.02);

            $this->entityManager->updateStore();

//            $data = $this->entityManager->findAll();
//            print_r($data);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

    }

    /**
     * create five new Inventory items
     * @return InventoryItem[]
     */
    private function createInventoryItems(): array
    {
        $items = [];

        $entityName = InventoryItem::class;

        Entity::setDefaultEntityManager($this->entityManager);

        $items[] = Entity::getEntity($entityName,
            array('sku' => 'abc-4589', 'qoh' => 0, 'cost' => '5.67', 'sale_price' => '7.27'));
        $items[] = Entity::getEntity($entityName,
            array('sku' => 'hjg-3821', 'qoh' => 0, 'cost' => '7.89', 'sale_price' => '12.00'));
        $items[] = Entity::getEntity($entityName,
            array('sku' => 'xrf-3827', 'qoh' => 0, 'cost' => '15.27', 'sale_price' => '19.99'));
        $items[] = Entity::getEntity($entityName,
            array('sku' => 'eer-4521', 'qoh' => 0, 'cost' => '8.45', 'sale_price' => '1.03'));
        $items[] = Entity::getEntity($entityName,
            array('sku' => 'qws-6783', 'qoh' => 0, 'cost' => '3.00', 'sale_price' => '4.97'));

        return $items;
    }


}