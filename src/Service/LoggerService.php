<?php

namespace App\Service;

use SplSubject;

class LoggerService implements \SplObserver
{
    const LOG_PATH = 'entity_update.log';

    /**
     * @throws \Exception
     */
    public function update(SplSubject $subject, mixed $newData = null): void
    {
        $date = (new \DateTime())->format('d.m.Y H:i:s');
        $strValue = json_encode($newData);
        $message = sprintf('%s: %s%s', $date, $strValue, "\n");
        $this->write($message);
    }

    /**
     * @throws \Exception
     */
    private function write(string $message): void
    {
        $filename = $this->getFilename();

        $fd = fopen($filename, 'a');
        if (false === $fd) {
            throw new \Exception('File not open.');
        }

        fwrite($fd, $message);
        fclose($fd);
    }

    private function getFilename(): string
    {
        return dirname(__DIR__, 2) . '/var/' . self::LOG_PATH;
    }
}