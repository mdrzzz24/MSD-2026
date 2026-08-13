<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanEncodingCommand extends Command
{
    protected $signature = 'data:clean-encoding {--dry-run : Report what would change without writing to the database}';

    protected $description = 'Decode over-encoded HTML entities (e.g. "&amp;amp;") in text columns so stored data stays clean';

    public function handle(): int
    {
        $tables = [
            'tracks'              => ['name', 'title', 'description'],
            'workshops'           => ['name', 'title', 'description'],
            'speakers'            => ['name', 'title', 'company', 'bio'],
            'agenda_items'        => ['title', 'topic_headline', 'description', 'key_highlights'],
            'agenda_item_speaker' => ['key_highlights', 'presentation_title', 'presentation_description'],
        ];

        $total = 0;
        $dry = (bool) $this->option('dry-run');

        foreach ($tables as $table => $columns) {
            foreach ($columns as $col) {
                // Only touch rows that actually contain an ampersand
                $rows = DB::table($table)->where($col, 'like', '%&%')->whereNotNull($col)->get(['id', $col]);

                foreach ($rows as $row) {
                    $original = $row->{$col};
                    if (! is_string($original) || $original === '') {
                        continue;
                    }

                    $cleaned = clean_text($original);
                    if ($cleaned !== $original) {
                        $total++;
                        $this->line("  [{$table}] #{$row->id} {$col}: " . substr($original, 0, 60) . '  =>  ' . substr($cleaned, 0, 60));
                        if (! $dry) {
                            DB::table($table)->where('id', $row->id)->update([$col => $cleaned]);
                        }
                    }
                }
            }
        }

        $this->info(($dry ? '[dry-run] Would update ' : 'Updated ') . $total . ' cell(s).');

        return self::SUCCESS;
    }
}
