<?php

namespace App\VedCreator\Models\Iz;

/**
 * Модель ИЗ (Идентификационная справка).
 * Содержит основные реквизиты документа и таблицу товаров.
 */
class IzDocument
{
    /** @var string|null Номер ИЗ (DocumentNumber). */
    public $number;

    /** @var string|null ИНН, извлечённый из номера ИЗ. */
    public $inn;

    /** @var string|null Дата ИЗ (DocumentDate). */
    public $permitDate;

    /** @var string|null Данные о контракте (TextSection[2]). */
    public $contract;

    /** @var string|null Сведения об операции (TextSection[3]). */
    public $operationInfo;

    /** @var string|null Получатель (TextSection[5]). */
    public $recipient;

    /** @var string|null Результат разрешения (TextSection[8]). */
    public $permitResult;

    /** @var IzItem[] Таблица товаров из ИЗ. */
    public $goodsTable = [];

    /**
     * @param string|null $number Номер ИЗ.
     * @param string|null $inn ИНН.
     * @param string|null $permitDate Дата ИЗ.
     * @param string|null $contract Контракт.
     * @param string|null $operationInfo Сведения об операции.
     * @param string|null $recipient Получатель.
     * @param string|null $permitResult Результат разрешения.
     * @param IzItem[] $goodsTable Таблица товаров из ИЗ.
     */
    public function __construct($number, $inn, $permitDate, $contract, $operationInfo, $recipient, $permitResult, $goodsTable)
    {
        $this->number = $number;
        $this->inn = $inn;
        $this->permitDate = $permitDate;
        $this->contract = $contract;
        $this->operationInfo = $operationInfo;
        $this->recipient = $recipient;
        $this->permitResult = $permitResult;
        $this->goodsTable = $goodsTable;
    }
}
