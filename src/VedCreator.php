<?php

namespace App\VedCreator;

use App\VedCreator\Builders\IzBuilder;
use App\VedCreator\Builders\DtBuilder;
use App\VedCreator\Builders\ResultBuilder;

/**
 * Основной фасад модуля VedCreator.
 * Отвечает за запуск полного цикла обработки:
 *  - парсинг ИЗ (Идентификационной справки)
 *  - парсинг ДТ (Декларации на товары)
 *  - формирование итоговых данных ResultData
 */
class VedCreator
{
    /**
     * Выполняет полный процесс обработки данных ВЭД:
     *  1. Загружает и парсит ИЗ.
     *  2. Загружает и парсит ДТ.
     *  3. Строит итоговую структуру ResultData.
     *
     * @param string $izPath Путь к XML-файлу ИЗ.
     * @param string $dtPath Путь к XML-файлу ДТ.
     * @return \App\VedCreator\Models\Result\ResultData|null Итоговые данные или null, если ДТ не прошёл фильтрацию.
     */
    public function run($izPath, $dtPath)
    {
        $iz = (new IzBuilder())->build($izPath);
        $dt = (new DtBuilder())->build($dtPath);

        $result = (new ResultBuilder())->build($iz, $dt);

        return $result;
    }
}
