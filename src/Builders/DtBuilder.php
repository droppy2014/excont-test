<?php

namespace App\VedCreator\Builders;

use App\VedCreator\VedConfig;
use App\VedCreator\Models\Dt\DtDocument;
use App\VedCreator\Models\Dt\DtGoodsItem;
use App\VedCreator\Models\Dt\DtLicenseItem;

/**
 * Построитель документов ДТ (декларации) из XML.
 * Парсит XML, извлекает данные и собирает объекты DtDocument.
 */
class DtBuilder {

    /**
     * Строит объект DtDocument из XML-файла.
     *
     * @param string $path Путь к XML-файлу ДТ.
     * @return DtDocument|null null — если ДТ не подходит по кодам процедуры или решению.
     * @throws \DOMException
     */
    public function build($path) {

        $dom = new \DOMDocument();
        $dom->load($path);

        $xp = new \DOMXPath($dom);
        $this->registerNamespaces($xp);

        $dateDeal          = $this->extractDateDeal($xp);
        $declNumber        = $this->extractDeclarationNumber($xp);
        $customsModeCode   = $this->extractCustomsModeCode($xp);
        $decisionCode      = $this->extractDecisionCode($xp);

        // фильтрация по виду процедуры
        if (!in_array($customsModeCode, VedConfig::getAllowedCustomsModes(), true)) {
            return null;
        }

        // фильтрация по коду решения
        if (!in_array($decisionCode, VedConfig::getAllowedDecisionCodes(), true)) {
            return null;
        }

        $goods = [];
        $licenses = [];

        $goodsNodes = $xp->query('//esad:ESADout_CUGoods');
        if ($goodsNodes) {
            foreach ($goodsNodes as $index => $goodsNode) {

                $positionNumber = $index + 1;

                $goodsName = $this->extractGoodsName($xp, $goodsNode);
                $tnved     = trim($xp->evaluate('string(catESAD_cu:GoodsTNVEDCode)', $goodsNode));

                // документы по товарной позиции
                $documents = $this->extractDocumentsForGoods($xp, $goodsNode);

                // сохраняем все license-документы (код вида 01*)
                foreach ($documents as $doc) {
                    $licenses[] = $doc;
                }

                $goods[] = new DtGoodsItem(
                    $positionNumber,
                    $goodsName,
                    $tnved,
                    $documents
                );
            }
        }

        return new DtDocument(
            $dateDeal,
            $declNumber,
            $customsModeCode,
            $decisionCode,
            $goods,
            $licenses
        );
    }

    /**
     * Регистрирует необходимые пространства имён для XPath.
     *
     * @param \DOMXPath $xp
     * @return void
     */
    protected function registerNamespaces($xp) {
        $xp->registerNamespace('ed', 'urn:customs.ru:Information:ExchangeDocuments:ED_Container:5.24.0');
        $xp->registerNamespace('esad', 'urn:customs.ru:Information:CustomsDocuments:ESADout_CU:5.24.0');
        $xp->registerNamespace('gtd', 'urn:customs.ru:Information:CustomsDocuments:GTDoutCustomsMark:5.24.0');
        $xp->registerNamespace('cat_ru', 'urn:customs.ru:CommonAggregateTypes:5.24.0');
        $xp->registerNamespace('catESAD_cu', 'urn:customs.ru:CUESADCommonAggregateTypesCust:5.24.0');
    }

    /**
     * Извлекает дату регистрации сделки.
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractDateDeal($xp) {
        $date = $xp->evaluate('string(//gtd:GTDoutCustomsMark/gtd:GTDID/cat_ru:RegistrationDate)');
        return $date !== '' ? $date : null;
    }

    /**
     * Формирует номер декларации в формате "кодТаможни/дата/GTDNumber".
     *
     * @param \DOMXPath $xp
     * @return string
     */
    protected function extractDeclarationNumber($xp) {
        $customsCode = $xp->evaluate('string(//gtd:GTDoutCustomsMark/gtd:GTDID/cat_ru:CustomsCode)');
        $regDate     = $xp->evaluate('string(//gtd:GTDoutCustomsMark/gtd:GTDID/cat_ru:RegistrationDate)');
        $gtdNumber   = $xp->evaluate('string(//gtd:GTDoutCustomsMark/gtd:GTDID/cat_ru:GTDNumber)');

        if ($regDate !== '') {
            try {
                $dt = new \DateTime($regDate);
                $regDateDmy = $dt->format('dmy');
            } catch (\Exception $e) {
                $regDateDmy = $regDate;
            }
        } else {
            $regDateDmy = '';
        }

        return $customsCode . '/' . $regDateDmy . '/' . $gtdNumber;
    }

    /**
     * Извлекает код таможенного режима (CustomsModeCode).
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractCustomsModeCode($xp) {
        $code = $xp->evaluate('string(//esad:ESADout_CU/*[local-name()="CustomsModeCode"])');

        if ($code === '') {
            $code = $xp->evaluate('string(//esad:CustomsModeCode)');
        }
        return $code !== '' ? $code : null;
    }

    /**
     * Извлекает код решения (DecisionCode).
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractDecisionCode($xp) {
        $code = $xp->evaluate('string(//gtd:GTDoutCustomsMark/*[local-name()="GTDOutResolution"]/*[local-name()="DecisionCode"])');
        return $code !== '' ? $code : null;
    }

    /**
     * Извлекает наименование товара из товара ESAD.
     *
     * @param \DOMXPath $xp
     * @param \DOMNode $goodsNode
     * @return string
     */
    protected function extractGoodsName($xp, $goodsNode) {
        $parts = [];

        $groupNodes = $xp->query('catESAD_cu:GoodsGroupDescription/catESAD_cu:GoodsDescription', $goodsNode);

        if ($groupNodes && $groupNodes->length > 0) {
            foreach ($groupNodes as $n) {
                $text = trim($n->textContent);
                if ($text !== '') {
                    $parts[] = $text;
                }
            }
        }

        if (!empty($parts)) {
            return implode(' ', $parts);
        }

        return trim($xp->evaluate('normalize-space(catESAD_cu:GoodsDescription)', $goodsNode));
    }

    /**
     * Извлекает документы (PresentedDocument) по товарной позиции.
     * Фильтрует только документы с префиксом кода из конфигурации (например "01*").
     *
     * @param \DOMXPath $xp
     * @param \DOMNode  $goodsNode
     * @return DtLicenseItem[]
     */
    protected function extractDocumentsForGoods($xp, $goodsNode) {
        $items = [];
        $prefixes = VedConfig::getAllowedDocumentModePrefixes();

        $nodes = $xp->query('esad:ESADout_CUPresentedDocument', $goodsNode);
        if (!$nodes) {
            return $items;
        }

        foreach ($nodes as $node) {
            $number   = trim($xp->evaluate('string(cat_ru:PrDocumentNumber)', $node));
            $date     = trim($xp->evaluate('string(cat_ru:PrDocumentDate)', $node));
            $name     = trim($xp->evaluate('string(cat_ru:PrDocumentName)', $node));
            $modeCode = trim($xp->evaluate('string(catESAD_cu:PresentedDocumentModeCode)', $node));

            if ($modeCode === '') {
                continue;
            }

            // фильтрация: код должен начинаться с префикса ("01")
            $allowed = false;
            foreach ($prefixes as $pref) {
                if (strpos($modeCode, $pref) === 0) {
                    $allowed = true;
                    break;
                }
            }

            if (!$allowed) {
                continue;
            }

            $items[] = new DtLicenseItem(
                $number,
                $date,
                $name,
                $modeCode
            );
        }

        return $items;
    }
}
