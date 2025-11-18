<?php

namespace App\Models;

class IzCollection
{
    public $items = array();

    public function __construct($items)
    {
        $this->items = $items;
    }
}