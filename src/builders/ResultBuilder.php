<?php

namespace App\VedCreator\Builders;

use App\VedCreator\Models\ResultItem;
use App\VedCreator\Models\ResultData;

class ResultBuilder
{
    public function build($iz, $dt)
    {
        $items = array();

        foreach ($dt->items as $dtItem) {
            $matches = array();
            $items[] = new ResultItem($dtItem, $matches);
        }

        return 123;
    }
}