<?php

namespace App\Service;

class DataStore
{
    protected ?string $storePath = null;

    protected ?array $dataStore = [];

    /**
     * @throws \Exception
     */
    public function __construct($storePath)
    {
        $this->storePath = $storePath;
        if (!file_exists($storePath)) {
            if (!touch($storePath)) {
                throw new \Exception("Could not create data store file $storePath. Details:" .
                    $this->getLastError());
            }
            if (!chmod($storePath, 0777)) {
                throw new \Exception("Could not set read/write on data store file $storePath. " .
                    "Details:" . $this->getLastError());
            }
        }
        if (!is_readable($storePath) || !is_writable($storePath)) {
            throw new \Exception("Data store file $storePath must be readable/writable. Details:" .
                $this->getlastError());
        }
        $rawData = file_get_contents($storePath);

        if ($rawData === false) {
            throw new \Exception("Read of data store file $storePath failed.  Details:" .
                $this->getLastError());
        }

        if (strlen($rawData > 0)) {
            $this->dataStore = unserialize($rawData);
        } else {
            $this->dataStore = null;
        }
    }

    //update the store with information
    public function set($item, $primary, $data): void
    {
        $this->dataStore[$item][$primary] = $data;
    }

    //get information
    public function get($item, $primary)
    {
        return $this->dataStore[$item][$primary] ?? null;
    }

    //delete an item.
    public function delete($item, $primary): void
    {
        if (isset($this->dataStore[$item][$primary])) {
            unset($this->dataStore[$item][$primary]);
        }
    }


    /**
     * save everything
     * @throws \Exception
     */
    public function save(): void
    {
        $result = file_put_contents($this->storePath, serialize($this->dataStore));
        if (false === $result) {
            throw new \Exception("Write of data store file $this->storePath failed.  Details:" . $this->getLastError());
        }
    }

    //Which types of items do we have stored
    public function getItemTypes(): array
    {
        if (is_null($this->dataStore)) {
            return array();
        }
        return array_keys($this->dataStore);
    }

    //get keys for an item-type, so we can loop over.
    public function getItemKeys($itemType): array
    {
        return array_keys($this->dataStore[$itemType]);
    }

    public function getAll(): array
    {
        return $this->dataStore;
    }

    private function getLastError(): string
    {
        $errorInfo = error_get_last();
        if (null === $errorInfo) {
            return '';
        }

        return sprintf(
            ' Error type %s, %s on line %s of %s. ',
            $errorInfo['type'],
            $errorInfo['message'],
            $errorInfo['line'],
            $errorInfo['file']
        );

    }
}