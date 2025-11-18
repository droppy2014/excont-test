<?php

namespace App\VedCreator\Builders;

use App\VedCreator\Models\Iz\IzDocument;
use App\VedCreator\Models\Iz\IzItem;

/**
 * Построитель ИЗ (Идентификационное заключение).
 * Загружает XML-документ ИЗ, извлекает необходимые поля и
 * собирает объект IzDocument с таблицей товаров.
 */
class IzBuilder {

    /**
     * Построить объект IzDocument из XML-файла.
     *
     * @param string $path Путь к XML-файлу ИЗ.
     * @return IzDocument
     * @throws \DOMException
     */
    public function build($path) {

        $dom = new \DOMDocument();
        $dom->load($path);

        $xp = new \DOMXPath($dom);
        $this->registerNamespaces($xp);

        $number         = $this->extractNumber($xp);
        $inn            = $this->extractInnFromNumber($number);
        $permitDate     = $this->extractPermitDate($xp);
        $contract       = $this->extractContract($xp);
        $operationInfo  = $this->extractOperationInfo($xp);
        $recipient      = $this->extractRecipient($xp);
        $permitResult   = $this->extractPermitResult($xp);
        $goodsTable     = $this->extractGoodsTable($xp);

        return new IzDocument(
            $number,
            $inn,
            $permitDate,
            $contract,
            $operationInfo,
            $recipient,
            $permitResult,
            $goodsTable
        );
    }

    /**
     * Регистрирует namespaces, необходимые для XPath-запросов.
     *
     * @param \DOMXPath $xp
     * @return void
     */
    protected function registerNamespaces($xp) {
        $xp->registerNamespace('fd', 'urn:customs.ru:Information:CustomsDocuments:FreeDoc:5.8.0');
        $xp->registerNamespace('cat_ru', 'urn:customs.ru:CommonAggregateTypes:5.8.0');
        $xp->registerNamespace('clt_ru', 'urn:customs.ru:CommonLeafTypes:5.8.0');
    }

    /**
     * Извлекает номер ИЗ (DocumentNumber).
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractNumber($xp) {
        $num = $xp->evaluate('string(/fd:FreeDoc/fd:DocumentHead/fd:DocumentNumber)');
        if ($num === '') {
            $num = $xp->evaluate('string(/fd:FreeDoc/DocumentNumber)');
        }
        return $num !== '' ? $num : null;
    }

    /**
     * Извлекает ИНН из номера документа.
     * В ИЗ ИНН обычно расположен после последнего "/".
     *
     * @param string|null $number
     * @return string|null
     */
    protected function extractInnFromNumber($number) {
        if (!$number) {
            return null;
        }

        $pos = strrpos($number, '/');
        if ($pos === false) {
            return null;
        }

        $inn = trim(substr($number, $pos + 1));
        return $inn !== '' ? $inn : null;
    }

    /**
     * Извлекает дату документа (DocumentDate).
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractPermitDate($xp) {
        $date = $xp->evaluate('string(/fd:FreeDoc/fd:DocumentHead/fd:DocumentDate)');
        if ($date === '') {
            $date = $xp->evaluate('string(/fd:FreeDoc/DocumentDate)');
        }
        return $date !== '' ? $date : null;
    }

    /**
     * Извлекает текстовый параграф из секции TextSection по индексу.
     *
     * @param \DOMXPath $xp
     * @param int $index Порядковый индекс TextSection в документе.
     * @return string|null
     */
    protected function extractTextSectionPara($xp, $index) {
        $expr = '/fd:FreeDoc/fd:DocumentBody/fd:TextSection[' . intval($index) . ']/fd:TextPara';
        $text = $xp->evaluate('string(' . $expr . ')');
        return $text !== '' ? trim($text) : null;
    }

    /**
     * Извлекает данные контракта из TextSection[2].
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractContract($xp) {
        return $this->extractTextSectionPara($xp, 2);
    }

    /**
     * Извлекает сведения об операции из TextSection[3].
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractOperationInfo($xp) {
        return $this->extractTextSectionPara($xp, 3);
    }

    /**
     * Извлекает данные о получателе из TextSection[5].
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractRecipient($xp) {
        return $this->extractTextSectionPara($xp, 5);
    }

    /**
     * Извлекает результат разрешения (п.4.5.2.3 TextPara[8]).
     *
     * @param \DOMXPath $xp
     * @return string|null
     */
    protected function extractPermitResult($xp) {
        return $this->extractTextSectionPara($xp, 8);
    }

    /**
     * Извлекает таблицу товаров (TableBody → TableRow).
     *
     * @param \DOMXPath $xp
     * @return IzItem[]
     */
    protected function extractGoodsTable($xp) {
        $items = [];

        $rows = $xp->query('/fd:FreeDoc/fd:DocumentBody/fd:Table/fd:TableBody/fd:TableRow');
        if (!$rows || $rows->length === 0) {
            return $items;
        }

        foreach ($rows as $index => $row) {

            // Первая строка — заголовки таблицы
            if ($index === 0) {
                continue;
            }

            $cells = $xp->query('fd:TableCell', $row);
            if (!$cells || $cells->length < 4) {
                continue;
            }

            $rowNumber = rtrim($this->extractCellText($cells->item(0)), ". \t\n\r\0\x0B");
            $goodsName = $this->extractCellMultiline($xp, $cells->item(1));
            $tnved     = $this->extractCellText($cells->item(2));
            $techDesc  = $this->extractCellMultiline($xp, $cells->item(3));

            if ($goodsName === '' && $tnved === '' && $techDesc === '') {
                continue;
            }

            $items[] = new IzItem(
                $rowNumber,
                $goodsName,
                $tnved,
                $techDesc
            );
        }

        return $items;
    }

    /**
     * Извлекает простой текст из ячейки таблицы.
     *
     * @param \DOMNode|null $cellNode
     * @return string
     */
    protected function extractCellText($cellNode) {
        if (!$cellNode) {
            return '';
        }
        return trim($cellNode->textContent);
    }

    /**
     * Извлекает многострочный текст из ячейки:
     * - если есть несколько <TextPara>, объединяет их
     * - иначе использует обычный textContent
     *
     * @param \DOMXPath $xp
     * @param \DOMNode|null $cellNode
     * @return string
     */
    protected function extractCellMultiline($xp, $cellNode) {
        if (!$cellNode) {
            return '';
        }

        $parts = [];
        $paras = $xp->query('fd:TextPara', $cellNode);

        if ($paras && $paras->length > 0) {
            foreach ($paras as $p) {
                $text = trim($p->textContent);
                if ($text !== '') {
                    $parts[] = $text;
                }
            }
        } else {
            $text = trim($cellNode->textContent);
            if ($text !== '') {
                $parts[] = $text;
            }
        }

        return implode("\n", $parts);
    }
}
