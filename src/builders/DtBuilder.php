<?php

namespace App\VedCreator\Builders;

use App\VedCreator\Models\DtCollection;

class DtBuilder
{
    public function build($path)
    {
        $xml = new \DOMDocument();
        $xml->load($path);
        $xp = new \DOMXPath($xml);

        $items = array();

        return new DtCollection($items);
    }
}