<?php

namespace App\VedCreator\Models\Result;

/**
 * Итоговый объект разрешительного документа (лицензии / presented document),
 * собранный из данных ДТ. Используется в финальной структуре ResultData.
 */
class ResultLicenseItem
{
    /** @var string|null Номер документа. */
    public $DOCUMENT_NUMBER;

    /** @var string|null Дата документа. */
    public $DOCUMENT_DATE;

    /** @var string|null Наименование документа. */
    public $DOCUMENT_NAME;

    /** @var string Код вида документа (PresentedDocumentModeCode). */
    public $DOCUMENT_MODE_CODE;

    /**
     * @param string|null $number Номер документа.
     * @param string|null $date Дата документа.
     * @param string|null $name Наименование документа.
     * @param string $modeCode Код вида документа.
     */
    public function __construct($number, $date, $name, $modeCode)
    {
        $this->DOCUMENT_NUMBER = $number;
        $this->DOCUMENT_DATE = $date;
        $this->DOCUMENT_NAME = $name;
        $this->DOCUMENT_MODE_CODE = $modeCode;
    }
}
