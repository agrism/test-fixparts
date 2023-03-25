<?php

namespace App\Entity;

class InventoryItem extends Entity
{
    //Update the number of items, because we have shipped some.
    public function itemsHaveShipped($numberShipped)
    {
        $this->qoh -= $numberShipped;
    }

    //We received new items, update the count.
    public function itemsReceived($numberReceived)
    {
        $this->qoh += $numberReceived;
    }

    public function changeSalePrice($salePrice)
    {
        $this->sale_price = $salePrice;
    }

    public function getMembers()
    {
        //These are the field in the underlying data array
        return [
            'sku' => 1,
            'qoh' => 1,
            'cost' => 1,
            'sale_price' => 1
        ];
    }

    public function getPrimary()
    {
        //Which field constitutes the primary key in the storage class?
        return 'sku';
    }
}