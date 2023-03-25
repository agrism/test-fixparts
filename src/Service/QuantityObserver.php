<?php

namespace App\Service;

use SplSubject;

class QuantityObserver implements \SplObserver
{
    const KEY_QOH = 'qoh';
    const KEY_SKU = 'sku';
    const MIN_QOH = 5;
    const MAIL_TO = 'test@mail.com';


    public function update(SplSubject $subject, mixed $newData = null): void
    {
        $qoh = $newData[self::KEY_QOH] ?? null;
        $sku = $newData[self::KEY_SKU] ?? null;

        if (null === $qoh || null === $sku) {
            return;
        }

        if ($qoh < self::MIN_QOH) {
            $this->sendMail($sku, $qoh);
        }
    }

    private function sendMail(string $sku, int $qoh): void
    {
        echo sprintf('Mail with: "%s: %d"', $sku, $qoh), PHP_EOL;

//        try {
//            mail(self::MAIL_TO, 'Subject', sprintf('%s: %d', $sku, $qoh));
//        } catch (\Throwable $e) {
//            echo $e->getMessage(), PHP_EOL;
//        }
    }
}