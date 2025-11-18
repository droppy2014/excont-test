<?php

namespace App\VedCreator\Models;

class ResultData
{
    public $items = array();

    public function __construct($items)
    {
        $this->items = $items;
    }
}