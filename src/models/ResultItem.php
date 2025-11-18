<?php

namespace App\VedCreator\Models;

class ResultItem
{
    public $dtItem;
    public $izItems = array();

    public function __construct($dtItem, $izItems)
    {
        $this->dtItem = $dtItem;
        $this->izItems = $izItems;
    }
}