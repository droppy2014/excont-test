<?php

namespace App\VedCreator\Models\Result;

/**
 * Итоговая модель товарной позиции.
 * Формируется после сопоставления ДТ и ИЗ и содержит расширенные данные
 * по каждой товарной позиции, включая разрешения, получателей и детали операции.
 */
class ResultGoodsItem
{
    /** @var string Наименование товара. */
    public $GOODS_NAME;

    /** @var string Код ТН ВЭД товара. */
    public $TNVED;

    /** @var string Номер ИЗ (или пустая строка, если нет ИЗ). */
    public $PERMIT_NUMBER;

    /** @var string Дата ИЗ (или пустая строка). */
    public $PERMIT_DATE;

    /** @var string Контракт (из ИЗ). */
    public $CONTRACT;

    /** @var string Получатель (из ИЗ). */
    public $RECIPIENT;

    /** @var string Результат разрешения (из ИЗ). */
    public $PERMIT_RESULT;

    /** @var string Данные об операции (из ИЗ). */
    public $OPERATION_INFO;

    /** @var string Дополнительные функциональные сведения о товаре (по ТЗ пусто). */
    public $GOODS_FUNC_DETAIL;

    /**
     * @param string $goodsName Наименование товара.
     * @param string $tnved Код ТН ВЭД.
     * @param string $permitNumber Номер разрешения / ИЗ.
     * @param string $permitDate Дата разрешения / ИЗ.
     * @param string $contract Контракт.
     * @param string $recipient Получатель.
     * @param string $permitResult Результат разрешения.
     * @param string $operationInfo Информация об операции.
     * @param string $goodsFuncDetail Дополнительные сведения (пусто по ТЗ).
     */
    public function __construct(
        $goodsName,
        $tnved,
        $permitNumber,
        $permitDate,
        $contract,
        $recipient,
        $permitResult,
        $operationInfo,
        $goodsFuncDetail
    ) {
        $this->GOODS_NAME = $goodsName;
        $this->TNVED = $tnved;
        $this->PERMIT_NUMBER = $permitNumber;
        $this->PERMIT_DATE = $permitDate;
        $this->CONTRACT = $contract;
        $this->RECIPIENT = $recipient;
        $this->PERMIT_RESULT = $permitResult;
        $this->OPERATION_INFO = $operationInfo;
        $this->GOODS_FUNC_DETAIL = $goodsFuncDetail;
    }
}
