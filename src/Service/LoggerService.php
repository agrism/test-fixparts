<?php

namespace App\Service;

use SplSubject;

class LoggerService implements \SplObserver
{
    private string $filename;

    /**
     * @param string $filename
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }


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
        $fd = fopen($this->filename, 'a');
        if (false === $fd) {
            throw new \Exception('File not open.');
        }

        fwrite($fd, $message);
        fclose($fd);
    }
}