<?php

namespace App\VedCreator\Models\Dt;

/**
 * Модель товарной позиции из ДТ.
 * Содержит сведения о наименовании, коде ТН ВЭД и связанных документах.
 */
class DtGoodsItem
{
    /** @var int Порядковый номер товарной позиции в ДТ. */
    public $positionNumber;

    /** @var string Наименование товара. */
    public $goodsName;

    /** @var string Код ТН ВЭД (10 знаков). */
    public $tnved;

    /** @var DtLicenseItem[] Документы, относящиеся к этой товарной позиции (PresentedDocument). */
    public $documents = [];

    /**
     * @param int $positionNumber Порядковый номер товарной позиции.
     * @param string $goodsName Наименование товара.
     * @param string $tnved Код ТН ВЭД.
     * @param DtLicenseItem[] $documents Документы по товарной позиции.
     */
    public function __construct($positionNumber, $goodsName, $tnved, $documents = [])
    {
        $this->positionNumber = $positionNumber;
        $this->goodsName = $goodsName;
        $this->tnved = $tnved;
        $this->documents = $documents;
    }
}
