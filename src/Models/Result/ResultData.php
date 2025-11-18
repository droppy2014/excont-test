<?php

namespace App\VedCreator\Models\Result;

/**
 * Итоговая структура данных для выгрузки результата.
 * Объединяет сведения о декларации, товарных позициях и документах
 * после сопоставления ИЗ с ДТ.
 */
class ResultData
{
    /** @var string|null Дата сделки / регистрации декларации. */
    public $DATE_DEAL;

    /** @var string|null Номер декларации в итоговом формате. */
    public $DECLARATION_NUMBER;

    /** @var ResultGoodsItem[] Список товарных позиций в итоговом представлении. */
    public $GOODS = [];

    /** @var ResultLicenseItem[] Список разрешительных документов. */
    public $LICENSE = [];

    /**
     * @param string|null $dateDeal Дата сделки.
     * @param string|null $declarationNumber Номер декларации.
     * @param ResultGoodsItem[] $goods Итоговые данные по товарам.
     * @param ResultLicenseItem[] $license Итоговые документы.
     */
    public function __construct($dateDeal, $declarationNumber, $goods, $license)
    {
        $this->DATE_DEAL = $dateDeal;
        $this->DECLARATION_NUMBER = $declarationNumber;
        $this->GOODS = $goods;
        $this->LICENSE = $license;
    }
}
