<?php

namespace App\VedCreator;

/**
 * Конфигурация правил обработки данных ВЭД.
 * Содержит справочные значения и коды, необходимые для фильтрации и сопоставления ДТ и ИЗ.
 */
class VedConfig
{
    /**
     * Разрешённые коды таможенных режимов.
     * 10 — экспорт  
     * 40 — выпуск для внутреннего потребления
     *
     * @return string[]
     */
    public static function getAllowedCustomsModes()
    {
        return ['10', '40'];
    }

    /**
     * Разрешённые коды решений таможенных органов.
     * Используются для фильтрации ДТ.
     *
     * @return string[]
     */
    public static function getAllowedDecisionCodes()
    {
        return ['10', '11', '12', '13', '14', '20'];
    }

    /**
     * Разрешённые префиксы PresentedDocumentModeCode.
     * Документы, код вида которых начинается с "01", считаются лицензиями / разрешительными документами.
     *
     * @return string[]
     */
    public static function getAllowedDocumentModePrefixes()
    {
        return ['01'];
    }

    /**
     * Код вида документа для ИЗ (Идентификационной справки).
     * Используется для привязки ИЗ к товарной позиции ДТ.
     *
     * @return string
     */
    public static function getIzDocumentModeCode()
    {
        return '01154';
    }
}
