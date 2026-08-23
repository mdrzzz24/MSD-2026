<?php

namespace App\Http\Controllers;

use App\Models\Registrant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDataCleaningController extends Controller
{
    /**
     * Show registrants grouped by their email domain so the super admin can
     * decide and apply a standardized company name for each group.
     */
    public function index()
    {
        $rows = Registrant::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->orderBy('email')
            ->get(['id', 'name', 'email', 'company']);

        // Group registrants by normalized email domain.
        $groups = [];
        foreach ($rows as $r) {
            $email = strtolower(trim((string) $r->email));
            if (! str_contains($email, '@')) {
                continue;
            }
            $domain = trim((string) Str::after($email, '@'));
            if ($domain === '') {
                continue;
            }
            $groups[$domain][] = [
                'id'      => $r->id,
                'name'    => $r->name,
                'email'   => $r->email,
                'company' => clean_text($r->company),
            ];
        }

        // Largest domains first, then alphabetically.
        uksort($groups, function ($a, $b) use ($groups) {
            $diff = count($groups[$b]) <=> count($groups[$a]);
            return $diff !== 0 ? $diff : strcmp($a, $b);
        });

        $domains = [];
        $variantTotal = 0;
        foreach ($groups as $domain => $members) {
            $companyCounts = [];
            foreach ($members as $m) {
                $company = trim((string) $m['company']);
                $companyCounts[$company] = ($companyCounts[$company] ?? 0) + 1;
            }
            // Most frequent values first, then alphabetically.
            uksort($companyCounts, function ($a, $b) use ($companyCounts) {
                $diff = $companyCounts[$b] <=> $companyCounts[$a];
                return $diff !== 0 ? $diff : strcmp($a, $b);
            });

            // Suggested standard name = most common non-empty company value.
            $suggested = '';
            foreach ($companyCounts as $name => $count) {
                if (trim($name) !== '') {
                    $suggested = $name;
                    break;
                }
            }

            $variantTotal += count($companyCounts);
            $domains[] = [
                'domain'        => $domain,
                'total'         => count($members),
                'companyCounts' => $companyCounts,
                'suggested'     => $suggested,
            ];
        }

        $stats = [
            'registrants'     => Registrant::count(),
            'withEmail'       => $rows->count(),
            'withoutEmail'    => max(0, Registrant::count() - $rows->count()),
            'domains'         => count($domains),
            'companyVariants' => $variantTotal,
        ];

        return view('admin.data-cleaning.index', compact('domains', 'stats'));
    }

    /**
     * Apply a standardized company name to all registrants in a domain group.
     * Supports both a per-domain apply (hidden "domain" field) and a bulk
     * "Apply All" (apply_all flag) that iterates every filled standard.
     */
    public function apply(Request $request)
    {
        $standards = (array) $request->input('standard', []);

        // Bulk mode: apply every filled standard name.
        if ($request->boolean('apply_all')) {
            $applied = 0;
            $filled = 0;
            foreach ($standards as $domain => $company) {
                $company = trim(clean_text((string) $company) ?? '');
                if ($company === '') {
                    continue;
                }
                $filled++;
                $applied += $this->applyDomain(strtolower(trim((string) $domain)), $company);
            }

            if ($filled === 0) {
                return back()->with('error', 'Nothing to apply — please fill in at least one standard company name.');
            }

            return back()->with(
                'success',
                "Applied standard company names to <strong>{$applied}</strong> registrant(s) across <strong>{$filled}</strong> domain(s)."
            );
        }

        // Per-domain mode: only the row whose Apply button was clicked.
        $domain = strtolower(trim((string) $request->input('domain')));
        $company = trim(clean_text((string) ($standards[$request->input('domain')] ?? '')) ?? '');

        if ($domain === '' || $company === '') {
            return back()->with('error', 'Please fill in the standard company name for this domain first.');
        }

        $count = $this->applyDomain($domain, $company);

        return back()->with(
            'success',
            "Updated <strong>{$count}</strong> registrant(s) in <strong>{$domain}</strong> → company: <strong>{$company}</strong>."
        );
    }

    /**
     * Registrants belonging to a domain (for the expandable drill-down).
     */
    public function members(Request $request)
    {
        $domain = strtolower(trim((string) $request->input('domain')));
        if ($domain === '') {
            return response()->json([]);
        }

        $rows = Registrant::query()
            ->whereRaw('LOWER(SUBSTRING_INDEX(email, "@", -1)) = ?', [$domain])
            ->orderBy('company')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'company']);

        return response()->json($rows->map(fn (Registrant $r) => [
            'id'      => $r->id,
            'name'    => $r->name,
            'email'   => $r->email,
            'company' => clean_text($r->company) ?: '',
        ]));
    }

    /**
     * Update the company for every registrant whose email domain matches.
     */
    private function applyDomain(string $domain, string $company): int
    {
        return Registrant::query()
            ->whereRaw('LOWER(SUBSTRING_INDEX(email, "@", -1)) = ?', [$domain])
            ->update(['company' => $company]);
    }
}
