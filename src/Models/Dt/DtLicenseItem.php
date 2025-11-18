<?php

namespace App\VedCreator\Models\Dt;

/**
 * Модель документа, представленного по товарной позиции ДТ (PresentedDocument).
 * Обычно включает лицензии, сертификаты и иные разрешительные документы.
 */
class DtLicenseItem
{
    /** @var string|null Номер документа. */
    public $documentNumber;

    /** @var string|null Дата документа. */
    public $documentDate;

    /** @var string|null Наименование документа. */
    public $documentName;

    /** @var string Код вида документа (PresentedDocumentModeCode). */
    public $documentModeCode;

    /**
     * @param string|null $documentNumber Номер документа.
     * @param string|null $documentDate Дата документа.
     * @param string|null $documentName Наименование.
     * @param string $documentModeCode Код вида документа (например 01154).
     */
    public function __construct($documentNumber, $documentDate, $documentName, $documentModeCode)
    {
        $this->documentNumber = $documentNumber;
        $this->documentDate = $documentDate;
        $this->documentName = $documentName;
        $this->documentModeCode = $documentModeCode;
    }
}
