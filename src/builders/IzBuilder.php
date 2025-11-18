<?php

namespace App\VedCreator\Builders;

use App\VedCreator\Models\IzCollection;

class IzBuilder
{
    public function build($path)
    {
        $xml = new \DOMDocument();
        $xml->load($path);
        $xp = new \DOMXPath($xml);

        $items = array();

        return new IzCollection($items);
    }
}