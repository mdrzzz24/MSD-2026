<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AdminBackupController extends Controller
{
    private const DISK = 'local';
    private const DIR = 'backups';
    private const FILENAME_PATTERN = '/^backup_\d{4}-\d{2}-\d{2}_\d{6}\.sql$/';

    public function index()
    {
        $files = collect(Storage::disk(self::DISK)->files(self::DIR))
            ->filter(fn ($path) => preg_match(self::FILENAME_PATTERN, basename($path)))
            ->map(fn ($path) => [
                'name'     => basename($path),
                'size'     => Storage::disk(self::DISK)->size($path),
                'modified' => Storage::disk(self::DISK)->lastModified($path),
            ])
            ->sortByDesc('modified')
            ->values();

        return view('admin.management.backup', compact('files'));
    }

    public function store(Request $request)
    {
        $db = $this->mysqlConnection();
        if (! $db) {
            return back()->with('error', 'Backup hanya didukung untuk koneksi database MySQL.');
        }

        Storage::disk(self::DISK)->makeDirectory(self::DIR);

        $filename = 'backup_' . now()->format('Y-m-d_His') . '.sql';
        $fullPath = Storage::disk(self::DISK)->path(self::DIR . '/' . $filename);

        $defaultsFile = $this->writeDefaultsFile($db);

        try {
            $handle = fopen($fullPath, 'wb');

            $result = Process::timeout(600)->run([
                env('DB_MYSQLDUMP_BIN', 'mysqldump'),
                '--defaults-extra-file=' . $defaultsFile,
                '--single-transaction',
                '--quick',
                '--routines',
                '--triggers',
                $db['database'],
            ], function ($type, $bytes) use ($handle) {
                if ($type === \Symfony\Component\Process\Process::OUT) {
                    fwrite($handle, $bytes);
                }
            });

            fclose($handle);

            if (! $result->successful()) {
                @unlink($fullPath);
                Log::error('Database backup failed: ' . $result->errorOutput());

                return back()->with('error', 'Backup gagal dijalankan. Periksa log server untuk detail.');
            }
        } finally {
            @unlink($defaultsFile);
        }

        return back()->with('success', 'Backup berhasil dibuat: <strong>' . e($filename) . '</strong>');
    }

    public function download(string $filename)
    {
        $this->validateFilename($filename);
        $path = self::DIR . '/' . $filename;

        if (! Storage::disk(self::DISK)->exists($path)) {
            abort(404);
        }

        return Storage::disk(self::DISK)->download($path);
    }

    public function destroy(string $filename)
    {
        $this->validateFilename($filename);
        Storage::disk(self::DISK)->delete(self::DIR . '/' . $filename);

        return back()->with('success', 'File backup dihapus.');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'confirm'       => 'required|in:RESTORE',
            'existing_file' => 'nullable|string',
            'backup_file'   => 'nullable|file',
        ]);

        $db = $this->mysqlConnection();
        if (! $db) {
            return back()->with('error', 'Restore hanya didukung untuk koneksi database MySQL.');
        }

        if ($request->hasFile('backup_file')) {
            $sqlPath = $request->file('backup_file')->getRealPath();
        } elseif ($request->filled('existing_file')) {
            $filename = $request->input('existing_file');
            $this->validateFilename($filename);
            $path = self::DIR . '/' . $filename;

            if (! Storage::disk(self::DISK)->exists($path)) {
                return back()->with('error', 'File backup tidak ditemukan.');
            }

            $sqlPath = Storage::disk(self::DISK)->path($path);
        } else {
            throw ValidationException::withMessages([
                'backup_file' => 'Pilih file backup yang ingin di-restore.',
            ]);
        }

        $defaultsFile = $this->writeDefaultsFile($db);

        try {
            $handle = fopen($sqlPath, 'rb');

            $result = Process::timeout(900)->input($handle)->run([
                env('DB_MYSQL_BIN', 'mysql'),
                '--defaults-extra-file=' . $defaultsFile,
                $db['database'],
            ]);

            fclose($handle);

            if (! $result->successful()) {
                Log::error('Database restore failed: ' . $result->errorOutput());

                return back()->with('error', 'Restore gagal dijalankan. Periksa log server untuk detail.');
            }
        } finally {
            @unlink($defaultsFile);
        }

        return back()->with('success', 'Database berhasil di-restore dari backup.');
    }

    private function mysqlConnection(): ?array
    {
        $name = config('database.default');
        $db = config("database.connections.{$name}");

        return ($db['driver'] ?? null) === 'mysql' ? $db : null;
    }

    private function writeDefaultsFile(array $db): string
    {
        $path = tempnam(sys_get_temp_dir(), 'my_cnf_');

        $lines = [
            '[client]',
            'host=' . $db['host'],
            'user=' . $db['username'],
            'password=' . $db['password'],
        ];

        // Prefer a unix socket when configured — takes precedence over host/port for local sockets.
        if (! empty($db['unix_socket'])) {
            $lines[] = 'socket=' . $db['unix_socket'];
        } else {
            $lines[] = 'port=' . $db['port'];
        }

        file_put_contents($path, implode("\n", $lines) . "\n");
        chmod($path, 0600);

        return $path;
    }

    private function validateFilename(string $filename): void
    {
        if (! preg_match(self::FILENAME_PATTERN, $filename)) {
            abort(400, 'Nama file backup tidak valid.');
        }
    }
}
