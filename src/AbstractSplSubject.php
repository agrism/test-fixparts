<?php

namespace App;

use SplObjectStorage;
use SplObserver;
use SplSubject;

class AbstractSplSubject implements SplSubject
{
    protected SplObjectStorage $storage;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();
    }


    public function attach(SplObserver $observer): void
    {
        $this->storage->attach($observer);
    }

    public function detach(SplObserver $observer): void
    {
        $this->storage->detach($observer);
    }

    public function notify($newData = null): void
    {
        /** @var SplObserver $observer */
        foreach ($this->storage as $observer) {
            $observer->update($this, $newData);
        }
    }

}