<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

/**
 * Minimal, dependency-free .xlsx (Excel 2007+) writer.
 *
 * Generates a valid spreadsheet from an array of headers + rows and streams it
 * as a download. No third-party packages required (uses PHP's built-in
 * ZipArchive). Cells support bold headers, numbers, long text (links), and
 * embedded PNG images (e.g. QR codes) via imageCell().
 */
class XlsxExporter
{
    /**
     * Marker for a cell whose value is an embedded PNG image.
     *
     * @return array{__image__: string}
     */
    public static function imageCell(string $png): array
    {
        return ['__image__' => $png];
    }

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

        $images = []; // ['bytes' => png, 'col' => 0-based, 'row' => 0-based]
        $sheet = self::sheetXml($headers, $rows, $images);
        $hasImages = count($images) > 0;

        $zip->addFromString('[Content_Types].xml', self::contentTypes($hasImages));
        $zip->addFromString('_rels/.rels', self::rootRels());
        $zip->addFromString('xl/workbook.xml', self::workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::workbookRels());
        $zip->addFromString('xl/styles.xml', self::styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet);

        if ($hasImages) {
            foreach ($images as $i => $img) {
                $zip->addFromString('xl/media/image'.($i + 1).'.png', $img['bytes']);
            }
            $zip->addFromString('xl/drawings/drawing1.xml', self::drawingXml($images));
            $zip->addFromString('xl/drawings/_rels/drawing1.xml.rels', self::drawingRels(count($images)));
            $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', self::sheetRels());
        }

        $zip->close();

        readfile($tmp);
        @unlink($tmp);
    }

    private static function contentTypes(bool $hasImages): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';

        if ($hasImages) {
            $xml .= '<Default Extension="png" ContentType="image/png"/>'
                .'<Override PartName="/xl/drawings/drawing1.xml" ContentType="application/vnd.openxmlformats-officedocument.drawing+xml"/>';
        }

        return $xml.'</Types>';
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
     * @param  array<int, array{bytes: string, col: int, row: int}>  $images  (by reference, filled)
     */
    private static function sheetXml(array $headers, array $rows, array &$images): string
    {
        $numCols = count($headers);

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        // Column widths (estimate from content, capped for links).
        $widths = [];
        foreach ($headers as $i => $header) {
            $widths[$i] = max(10, mb_strlen((string) $header) + 2);
        }
        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                if ($i >= $numCols || self::isImage($cell)) {
                    continue;
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
                if (self::isImage($value)) {
                    // Register the image anchored to this cell, emit an empty cell.
                    $images[] = ['bytes' => $value['__image__'], 'col' => $i, 'row' => $rIdx + 1];
                    $xml .= '<c r="'.self::colLetter($i + 1).$r.'"/>';
                    continue;
                }
                $xml .= self::cell($i + 1, $r, $value);
            }
            $xml .= '</row>';
        }

        $xml .= '</sheetData>';

        if (count($images) > 0) {
            $xml .= '<drawing r:id="rId1"/>';
        }

        return $xml.'</worksheet>';
    }

    private static function isImage(mixed $value): bool
    {
        return is_array($value) && isset($value['__image__']);
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

    /**
     * @param  array<int, array{bytes: string, col: int, row: int}>  $images
     */
    private static function drawingXml(array $images): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<xdr:wsDr xmlns:xdr="http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing"'
            .' xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"'
            .' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">';

        foreach ($images as $i => $img) {
            $n = $i + 1;
            $xml .= '<xdr:oneCellAnchor>'
                .'<xdr:from>'
                .'<xdr:col>'.$img['col'].'</xdr:col><xdr:colOff>0</xdr:colOff>'
                .'<xdr:row>'.$img['row'].'</xdr:row><xdr:rowOff>0</xdr:rowOff>'
                .'</xdr:from>'
                .'<xdr:ext cx="1143000" cy="1143000"/>'
                .'<xdr:pic>'
                .'<xdr:nvPicPr><xdr:cNvPr id="'.($i + 2).'" name="image'.$n.'.png"/><xdr:cNvPicPr><a:picLocks noChangeAspect="1"/></xdr:cNvPicPr></xdr:nvPicPr>'
                .'<xdr:blipFill><a:blip r:embed="rId'.$n.'"/><a:stretch><a:fillRect/></a:stretch></xdr:blipFill>'
                .'<xdr:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="1143000" cy="1143000"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></xdr:spPr>'
                .'</xdr:pic>'
                .'<xdr:clientData/>'
                .'</xdr:oneCellAnchor>';
        }

        return $xml.'</xdr:wsDr>';
    }

    private static function drawingRels(int $count): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
        for ($i = 1; $i <= $count; $i++) {
            $xml .= '<Relationship Id="rId'.$i.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="../media/image'.$i.'.png"/>';
        }

        return $xml.'</Relationships>';
    }

    private static function sheetRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing" Target="../drawings/drawing1.xml"/>'
            .'</Relationships>';
    }
}
