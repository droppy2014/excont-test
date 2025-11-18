<?php

namespace App\VedCreator\Builders;

use App\VedCreator\VedConfig;
use App\VedCreator\Models\Dt\DtGoodsItem;
use App\VedCreator\Models\Dt\DtLicenseItem;
use App\VedCreator\Models\Iz\IzDocument;
use App\VedCreator\Models\Result\ResultData;
use App\VedCreator\Models\Result\ResultGoodsItem;
use App\VedCreator\Models\Result\ResultLicenseItem;

/**
 * Строитель итоговых данных (ResultData),
 * объединяющий сведения из ИЗ и ДТ.
 */
class ResultBuilder {

    /**
     * Формирует итоговый объект ResultData, объединяя данные ИЗ и ДТ.
     *
     * @param IzDocument|null $iz Объект ИЗ (может быть null).
     * @param object|null $dt Объект ДТ (DtDocument). Если null — возврат null.
     * @return ResultData|null
     */
    public function build($iz, $dt) {
        if (!$dt) {
            return null;
        }

        $goods = $this->buildGoods($iz, $dt);
        $licenses = $this->buildLicenses($dt);

        return new ResultData(
            $dt->dateDeal,
            $dt->declarationNumber,
            $goods,
            $licenses
        );
    }

    /**
     * Формирует список товарных позиций для ResultData.
     * Если для позиции в ДТ найден документ ИЗ — подставляются данные из ИЗ.
     *
     * @param IzDocument|null $iz
     * @param object $dt DtDocument
     * @return ResultGoodsItem[]
     */
    protected function buildGoods($iz, $dt) {
        $result = [];
        $izCode = VedConfig::getIzDocumentModeCode(); // код ИЗ, например "01154"

        foreach ($dt->goods as $dtItem) {

            // Проверяем, прикреплён ли документ ИЗ к товарной позиции
            $hasIz = false;
            foreach ($dtItem->documents as $doc) {
                if ($doc->documentModeCode === $izCode) {
                    $hasIz = true;
                    break;
                }
            }

            // Если есть ИЗ — берём данные из него
            if ($hasIz && $iz) {
                $permitNumber   = $iz->number;
                $permitDate     = $iz->permitDate;
                $contract       = $iz->contract;
                $recipient      = $iz->recipient;
                $permitResult   = $iz->permitResult;
                $operationInfo  = $iz->operationInfo;
                $goodsFuncDetail = ''; // По ТЗ всегда пусто
            } else {
                // Нет ИЗ — поля пустые
                $permitNumber = '';
                $permitDate = '';
                $contract = '';
                $recipient = '';
                $permitResult = '';
                $operationInfo = '';
                $goodsFuncDetail = '';
            }

            $result[] = new ResultGoodsItem(
                $dtItem->goodsName,
                $dtItem->tnved,
                $permitNumber,
                $permitDate,
                $contract,
                $recipient,
                $permitResult,
                $operationInfo,
                $goodsFuncDetail
            );
        }

        return $result;
    }

    /**
     * Формирует список разрешительных документов (licenses)
     * для итогового объекта ResultData.
     *
     * @param object $dt DtDocument
     * @return ResultLicenseItem[]
     */
    protected function buildLicenses($dt) {
        $result = [];

        foreach ($dt->licenses as $license) {
            /** @var DtLicenseItem $license */
            $result[] = new ResultLicenseItem(
                $license->documentNumber,
                $license->documentDate,
                $license->documentName,
                $license->documentModeCode
            );
        }

        return $result;
    }
}
