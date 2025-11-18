<?php

namespace App\VedCreator\Models;

class DtCollection
{
    public $items = array();

    public function __construct($items)
    {
        $this->items = $items;
    }
}