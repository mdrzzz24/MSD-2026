<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

/**
 * Minimal, dependency-free .xlsx (Excel 2007+) writer.
 *
 * Generates a valid spreadsheet from an array of headers + rows and streams it
 * as a download. No third-party packages required (uses PHP's built-in
 * ZipArchive). Cells support bold headers, numbers, and long text (links).
 */
class XlsxExporter
{
    /**
     * Stream a download response with the given sheet content.
     *
     * @param  array<string>  $headers
     * @param  array<array<mixed>>  $rows
     */
    public static function download(array $headers, array $rows, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows) {
            self::output($headers, $rows);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Write the .xlsx binary directly to php://output.
     *
     * @param  array<string>  $headers
     * @param  array<array<mixed>>  $rows
     */
    public static function output(array $headers, array $rows): void
    {
        $tmp = tempnam(sys_get_temp_dir(), 'xlsx');
        if ($tmp === false) {
            throw new \RuntimeException('Could not create a temporary file for the export.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tmp, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tmp);
            throw new \RuntimeException('Could not create the spreadsheet archive.');
        }

        $zip->addFromString('[Content_Types].xml', self::contentTypes());
        $zip->addFromString('_rels/.rels', self::rootRels());
        $zip->addFromString('xl/workbook.xml', self::workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', self::sheetXml($headers, $rows));
        $zip->close();

        readfile($tmp);
        @unlink($tmp);
    }

    private static function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>';
    }

    private static function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    private static function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Sessions" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private static function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private static function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2">'
            .'<font><sz val="11"/><name val="Calibri"/></font>'
            .'<font><b/><sz val="11"/><name val="Calibri"/></font>'
            .'</fonts>'
            .'<fills count="2">'
            .'<fill><patternFill patternType="none"/></fill>'
            .'<fill><patternFill patternType="gray125"/></fill>'
            .'</fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="2">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    /**
     * @param  array<string>  $headers
     * @param  array<array<mixed>>  $rows
     */
    private static function sheetXml(array $headers, array $rows): string
    {
        $numCols = count($headers);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';

        // Column widths (estimate from content, capped for links).
        $widths = [];
        foreach ($headers as $i => $header) {
            $widths[$i] = max(10, mb_strlen((string) $header) + 2);
        }
        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                if ($i >= $numCols) {
                    break;
                }
                $len = min(mb_strlen((string) $cell), 60);
                if ($len > $widths[$i]) {
                    $widths[$i] = $len;
                }
            }
        }
        $xml .= '<cols>';
        foreach ($widths as $i => $width) {
            $xml .= '<col min="'.($i + 1).'" max="'.($i + 1).'" width="'.min($width + 2, 70).'" customWidth="1"/>';
        }
        $xml .= '</cols>';

        $xml .= '<sheetData>';

        // Header row (bold).
        $xml .= '<row r="1">';
        foreach ($headers as $i => $header) {
            $xml .= self::cell($i + 1, 1, $header, true);
        }
        $xml .= '</row>';

        // Data rows.
        foreach ($rows as $rIdx => $row) {
            $r = $rIdx + 2;
            $xml .= '<row r="'.$r.'">';
            foreach ($row as $i => $value) {
                $xml .= self::cell($i + 1, $r, $value);
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private static function cell(int $col, int $row, mixed $value, bool $bold = false): string
    {
        $ref = self::colLetter($col).$row;

        if (is_int($value) || is_float($value)) {
            return '<c r="'.$ref.'"><v>'.$value.'</v></c>';
        }

        $escaped = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
        $style = $bold ? ' s="1"' : '';

        return '<c r="'.$ref.'" t="inlineStr"'.$style.'><is><t xml:space="preserve">'.$escaped.'</t></is></c>';
    }

    private static function colLetter(int $i): string
    {
        $letter = '';
        while ($i > 0) {
            $mod = ($i - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $i = intdiv($i - 1, 26);
        }

        return $letter;
    }
}
