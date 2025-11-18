<?php

namespace App\VedCreator\VedCreator\Models;

class DtItem
{
    public $itemNumber;
    public $name;
    public $tnved;

    public function __construct($itemNumber, $name, $tnved)
    {
        $this->itemNumber = $itemNumber;
        $this->name = $name;
        $this->tnved = $tnved;
    }
}