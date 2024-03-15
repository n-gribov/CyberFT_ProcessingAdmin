<?php

namespace app\models\documents\document;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Expression;

class DocumentsExcelReport
{
    private ActiveQuery $query;

    public function __construct(ActiveQuery $query)
    {
        $this->query = $query;
    }

    public function generate(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->writeHeader($sheet);
        $this->writeBody($sheet);

        return $this->writeSpreadSheetToString($spreadsheet);
    }

    public function getFileName(): string
    {
        list($startDate, $endDate) = $this->getReportPeriod();
        $fileName = 'Documents';
        if ($startDate) {
            $fileName .= "_$startDate-$endDate";
        }
        return "$fileName.xlsx";
    }

    private function getReportPeriod(): array
    {
        $query = clone $this->query;
        $record = $query
            ->select([
                'min' => new Expression("to_char(min(reg_time), 'dd.mm.yyyy')"),
                'max' => new Expression("to_char(max(reg_time), 'dd.mm.yyyy')"),
            ])
            ->asArray()
            ->one();

        return $record !== null
            ? [$record['min'], $record['max']]
            : [null, null];
    }

    private function attributes(): array
    {
        return [
            'message_code',
            'snd_full_swift_code',
            'sender_name',
            'rsv_full_swift_code',
            'receiver_name',
            'sender_msg_code',
            'send_status_name',
            'reg_time',
            'final_time',
            'message_length',
            'message_cnt',
            'message_sum',
            'curr_code',
        ];
    }

    private function attributesFormat(): array
    {
        return [
            'reg_time' => ['datetime', 'dd.MM.yyyy HH:mm:ss'],
            'final_time' => ['datetime', 'dd.MM.yyyy HH:mm:ss'],
        ];
    }

    private function getAttributeFormat(string $attribute): ?array
    {
        return $this->attributesFormat()[$attribute] ?? null;
    }

    private function columnsTitles(): array
    {
        $model = new DocumentSearch();
        return array_map(
            fn($attribute) => $model->getAttributeLabel($attribute),
            $this->attributes()
        );
    }

    private function writeSpreadSheetToString(Spreadsheet $spreadsheet): string
    {
        $writer = new Xlsx($spreadsheet);
        $tmpFilePath = tempnam('/tmp', 'excel-report');
        $writer->save($tmpFilePath);
        $content = file_get_contents($tmpFilePath);
        unlink($tmpFilePath);
        return $content;
    }

    private function writeHeader(Worksheet $sheet): void
    {
        $titles = $this->columnsTitles();
        $sheet
            ->getStyleByColumnAndRow(1, 1, count($titles), 1)
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                ],
                'borders' => [
                    'bottom' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        foreach ($titles as $i => $title) {
            $sheet
                ->getColumnDimensionByColumn($i + 1)
                ->setAutoSize(true);
            $sheet
                ->getCellByColumnAndRow($i + 1, 1)
                ->setValue($title);
        }
    }

    private function writeBody(Worksheet $sheet): void
    {
        $documents = $this->query
            ->asArray()
            ->all();

        foreach ($documents as $documentIndex => $document) {
            $this->writeBodyRow($sheet, $documentIndex, $document);
        }
    }

    private function writeBodyRow(Worksheet $sheet, int $documentIndex, array $document): void
    {
        foreach ($this->attributes() as $attributeIndex => $attribute) {
            $sheet
                ->getCellByColumnAndRow($attributeIndex + 1, $documentIndex + 2)
                ->setValue(
                    $this->renderCellValue($attribute, $document[$attribute])
                );
        }
    }

    private function renderCellValue(string $attribute, $value)
    {
        if ($value === null || $value === '') {
            return null;
        }
        $format = $this->getAttributeFormat($attribute);
        return $format !== null
            ? Yii::$app->formatter->format($value, $format)
            : $value;
    }
}
