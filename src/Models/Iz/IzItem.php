<?php

namespace App\VedCreator\Models\Iz;

/**
 * Элемент таблицы товаров ИЗ (Идентификационной справки).
 * Содержит строку таблицы: номер, наименование, код ТН ВЭД и техническое описание.
 */
class IzItem
{
    /** @var string|int Номер строки таблицы (обычно 1., 2., 3. …). */
    public $rowNumber;

    /** @var string Наименование товара. */
    public $goodsName;

    /** @var string Код ТН ВЭД товара. */
    public $tnved;

    /** @var string Техническое описание товара (многострочный текст). */
    public $technicalDescription;

    /**
     * @param string|int $rowNumber Номер строки таблицы.
     * @param string $goodsName Наименование товара.
     * @param string $tnved Код ТН ВЭД.
     * @param string $technicalDescription Техническое описание.
     */
    public function __construct($rowNumber, $goodsName, $tnved, $technicalDescription)
    {
        $this->rowNumber = $rowNumber;
        $this->goodsName = $goodsName;
        $this->tnved = $tnved;
        $this->technicalDescription = $technicalDescription;
    }
}
