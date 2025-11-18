<?php

namespace App\VedCreator\Models\Dt;

/**
 * Модель декларации (ДТ).
 * Содержит сведения о регистрации, режиме, решении и списки товаров и документов.
 */
class DtDocument
{
    /** @var string|null Дата регистрации сделки (RegistrationDate). */
    public $dateDeal;

    /** @var string|null Полный номер декларации (CustomsCode/Date/Number). */
    public $declarationNumber;

    /** @var string|null Код таможенного режима. */
    public $customsModeCode;

    /** @var string|null Код решения по декларации. */
    public $decisionCode;

    /** @var DtGoodsItem[] Список товарных позиций. */
    public $goods = [];

    /** @var DtLicenseItem[] Список разрешительных документов (лицензий). */
    public $licenses = [];

    /**
     * @param string|null $dateDeal Дата сделки/регистрации.
     * @param string|null $declarationNumber Номер декларации.
     * @param string|null $customsModeCode Код режима.
     * @param string|null $decisionCode Код решения таможни.
     * @param DtGoodsItem[] $goods Список товаров.
     * @param DtLicenseItem[] $licenses Список документов.
     */
    public function __construct($dateDeal, $declarationNumber, $customsModeCode, $decisionCode, $goods, $licenses)
    {
        $this->dateDeal = $dateDeal;
        $this->declarationNumber = $declarationNumber;
        $this->customsModeCode = $customsModeCode;
        $this->decisionCode = $decisionCode;
        $this->goods = $goods;
        $this->licenses = $licenses;
    }
}
