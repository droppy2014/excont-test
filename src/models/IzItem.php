<?php

namespace App\VedCreator\Models;

class IzItem
{
    public $rowNumber;
    public $name;
    public $tnved;
    public $description;

    public function __construct($rowNumber, $name, $tnved, $description)
    {
        $this->rowNumber = $rowNumber;
        $this->name = $name;
        $this->tnved = $tnved;
        $this->description = $description;
    }
}