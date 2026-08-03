<?php

namespace App\Services;

use App\Models\Registrant;
use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Parses a client's xlsx report (e.g. "Report from Rozan.xlsx") containing an
 * APPROVAL STATUS column and imports the confirmations as CLIENT MARKINGS
 * (client_remark_action = approve / reject / waitlist) — WITHOUT changing the
 * registrant's real status. Status is left untouched (admin decides separately).
 *
 * Expected columns (header row): BUSINESS EMAIL, FULL NAME, APPROVAL STATUS.
 * APPROVAL STATUS mapping:
 *   - APPROVED                        -> approve
 *   - DECLINE / DECLINE (...reason)   -> reject  (reason stored as client_remark)
 *   - WAITING LIST                    -> waitlist (also sets waitlisted = true)
 *   - anything else                   -> skipped (reported in the preview)
 *
 * The DECLINE reason can live in two places:
 *   1. the parenthetical, e.g. "DECLINE (COMPETITOR)" -> "Competitor";
 *   2. a separate note column whose header is exactly "Source" (distinct from
 *      the UTM "SOURCE" column), e.g. "Declined from Metrodata Team".
 */
class ClientConfirmationImporter
{
    /**
     * Parse an .xlsx file into raw rows.
     *
     * @return array<int, array{email:string,name:string,status:string}>
     */
    public function parseFile(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new RuntimeException('Tidak dapat membuka file Excel.');
        }

        // ── Shared strings ──
        $strings = [];
        $shared = $zip->getFromName('xl/sharedStrings.xml');
        if ($shared !== false) {
            $xml = new SimpleXMLElement($shared);
            foreach ($xml->si as $si) {
                $strings[] = $this->nodeText($si);
            }
        }

        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheet === false) {
            throw new RuntimeException('File Excel tidak memiliki sheet data.');
        }

        $xml = new SimpleXMLElement($sheet);

        $header = [];
        $rows = [];
        $rowIndex = 0;

        foreach ($xml->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r']; // e.g. "A1"
                $type = (string) $c['t']; // '' | 's' | 'inlineStr' | ...
                $value = '';
                if (isset($c->v)) {
                    $value = (string) $c->v;
                    if ($type === 's' && $value !== '') {
                        $value = $strings[(int) $value] ?? '';
                    }
                } elseif ($type === 'inlineStr' && isset($c->is)) {
                    $value = $this->nodeText($c->is);
                }
                $cells[$this->colToIndex($ref)] = trim($value);
            }

            if ($rowIndex === 0) {
                $header = $cells;
            } elseif ($this->hasAnyCell($cells)) {
                $rows[] = $cells;
            }
            $rowIndex++;
        }

        $colEmail  = $this->findHeader($header, ['business email', 'email', 'email address', 'e-mail']);
        $colName   = $this->findHeader($header, ['full name', 'name', 'nama', 'participant name']);
        $colStatus = $this->findHeader($header, ['approval status', 'approve status', 'status', 'approved status']);
        // The DECLINE reason often lives in a separate note column whose header is exactly
        // "Source" (case-sensitive) — distinct from the UTM "SOURCE" column.
        $colReason = $this->findExactHeader($header, 'Source');

        $out = [];
        foreach ($rows as $cells) {
            $out[] = [
                'email'  => (string) ($cells[$colEmail] ?? ''),
                'name'   => (string) ($cells[$colName] ?? ''),
                'status' => (string) ($cells[$colStatus] ?? ''),
                'reason' => (string) ($cells[$colReason] ?? ''),
            ];
        }

        return $out;
    }

    /**
     * Build a preview (no DB writes) from parsed rows.
     *
     * @param array<int, array{email:string,name:string,status:string}> $rows
     * @return array{client_id:int,to_apply:array,skipped_reasons:array,total_rows:int}
     */
    public function buildPreview(array $rows, int $clientId): array
    {
        $toApply = [];
        $skippedReasons = [];

        foreach ($rows as $row) {
            $mapped = $this->mapStatus($row['status'], $row['reason'] ?? '');

            if ($mapped === null) {
                $label = $row['status'] !== '' ? $row['status'] : '(status kosong)';
                $skippedReasons[$label] = ($skippedReasons[$label] ?? 0) + 1;
                continue;
            }

            $reg = $this->findRegistrant($row['email'], $row['name']);
            if ($reg === null) {
                $key = 'Tidak ditemukan di registrants (' . ($row['email'] !== '' ? $row['email'] : $row['name']) . ')';
                $skippedReasons[$key] = ($skippedReasons[$key] ?? 0) + 1;
                continue;
            }

            $toApply[] = [
                'registrant_id'  => $reg->id,
                'email'          => $reg->email,
                'name'           => $reg->name,
                'excel_status'   => $row['status'],
                'action'         => $mapped['action'],
                'remark'         => $mapped['remark'],
                'current_status' => $reg->status,
            ];
        }

        return [
            'client_id'       => $clientId,
            'to_apply'        => $toApply,
            'skipped_reasons' => $skippedReasons,
            'total_rows'      => count($rows),
        ];
    }

    /**
     * Apply client markings to registrants (does NOT touch status).
     *
     * @param array<int, array{registrant_id:int,remark:?string,action:string}> $toApply
     * @return array{approve:int,reject:int,waitlist:int}
     */
    public function apply(array $toApply, int $clientId): array
    {
        $counts = ['approve' => 0, 'reject' => 0, 'waitlist' => 0];
        $now = now();

        foreach ($toApply as $item) {
            $reg = Registrant::find($item['registrant_id']);
            if ($reg === null) {
                continue;
            }

            $reg->update([
                'client_remark'        => isset($item['remark']) && $item['remark'] !== ''
                    ? mb_substr($item['remark'], 0, 2000)
                    : null,
                'client_remark_action' => $item['action'],
                'client_remarked_by'   => $clientId,
                'client_remarked_at'   => $now,
                'waitlisted'           => $item['action'] === 'waitlist',
            ]);

            $counts[$item['action']] = ($counts[$item['action']] ?? 0) + 1;
        }

        return $counts;
    }

    /**
     * Map an Excel APPROVAL STATUS value to a client marking action.
     *
     * @return array{action:string,remark:?string}|null null when the value is not a recognised confirmation
     */
    private function mapStatus(string $status, string $excelReason = ''): ?array
    {
        $s = strtoupper(trim($status));

        if ($s === 'APPROVED') {
            return ['action' => 'approve', 'remark' => null];
        }

        if ($s === 'WAITING LIST' || $s === 'WAITLIST') {
            return ['action' => 'waitlist', 'remark' => null];
        }

        if (str_starts_with($s, 'DECLINE')) {
            $reason = null;
            // 1) Reason in the parenthetical, e.g. "DECLINE (COMPETITOR)".
            if (preg_match('/\(([^)]+)\)/', $s, $m)) {
                $reason = 'Reject reason: ' . ucwords(strtolower(trim($m[1])));
            } elseif (trim($excelReason) !== '') {
                // 2) Reason in the separate "Source" note column, e.g. "Declined from Metrodata Team".
                $cleaned = $this->cleanDeclineReason($excelReason);
                if ($cleaned !== '') {
                    $reason = 'Reject reason: ' . $cleaned;
                }
            }
            return ['action' => 'reject', 'remark' => $reason];
        }

        return null;
    }

    /**
     * Find a header column whose trimmed value equals $exact (case-sensitive).
     * Returns the LAST match, so e.g. "Source" (reason note) is preferred over an
     * earlier "SOURCE"/"UTM Source" column.
     *
     * @param array<int,string> $header
     */
    private function findExactHeader(array $header, string $exact): ?int
    {
        $found = null;
        foreach ($header as $col => $value) {
            if (trim((string) $value) === $exact) {
                $found = $col;
            }
        }
        return $found;
    }

    /**
     * Normalise a decline reason read from the Excel "Source" note column.
     * "DECLINE (COMPETITOR)" -> "Competitor", "DECLINED (INTERN)" -> "Intern".
     * A bare "DECLINE"/"DECLINED" is kept as-is so every reject row carries a reason.
     */
    private function cleanDeclineReason(string $raw): string
    {
        $r = trim($raw);
        if (preg_match('/^DECLINE(D)?\s*\(([^)]+)\)$/i', $r, $m)) {
            return ucwords(strtolower(trim($m[2])));
        }
        return $r;
    }

    /**
     * Find the registrant by email (preferred) then by name.
     */
    private function findRegistrant(string $email, string $name): ?Registrant
    {
        if ($email !== '') {
            $reg = Registrant::whereRaw('LOWER(email) = ?', [mb_strtolower($email)])
                ->orderBy('id')
                ->first();
            if ($reg !== null) {
                return $reg;
            }
        }

        if ($name !== '') {
            $reg = Registrant::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->orderBy('id')
                ->first();
            if ($reg !== null) {
                return $reg;
            }
        }

        return null;
    }

    /**
     * Concatenate all <t> text within a node (handles rich-text runs).
     */
    private function nodeText(SimpleXMLElement $node): string
    {
        $parts = [];
        foreach ($node->t as $t) {
            $parts[] = (string) $t;
        }
        return trim(implode('', $parts));
    }

    /**
     * Convert a cell reference like "C7" into a 1-based column index (C -> 3).
     */
    private function colToIndex(string $ref): int
    {
        $letters = preg_replace('/\d+/', '', $ref);
        $index = 0;
        $len = strlen($letters);
        for ($i = 0; $i < $len; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }
        return $index;
    }

    /**
     * Find a header column index matching any of the given aliases.
     *
     * @param array<int,string> $header
     * @param array<int,string> $aliases
     */
    private function findHeader(array $header, array $aliases): ?int
    {
        $normalized = [];
        foreach ($header as $col => $value) {
            $normalized[preg_replace('/[^a-z]/', '', strtolower(trim($value)))] = $col;
        }
        foreach ($aliases as $alias) {
            $key = preg_replace('/[^a-z]/', '', strtolower($alias));
            if (isset($normalized[$key])) {
                return $normalized[$key];
            }
        }
        return null;
    }

    /**
     * Whether the row contains at least one non-empty cell.
     *
     * @param array<int,string> $cells
     */
    private function hasAnyCell(array $cells): bool
    {
        foreach ($cells as $value) {
            if ($value !== '') {
                return true;
            }
        }
        return false;
    }
}
