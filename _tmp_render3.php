<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
try {
    Auth::loginUsingId(1);
    $agendum = App\Models\AgendaItem::findOrFail(2);
    // 1) normal path
    echo "normal shortUrl: " . $agendum->shortUrl() . "\n";
    // 2) simulate missing short_code (migration not run)
    $agendum->setRawAttributes(array_merge($agendum->getAttributes(), ['short_code' => null]));
    echo "fallback shortUrl (no short_code): " . $agendum->shortUrl() . "\n";
    // 3) simulate route missing
    $agendum->setRawAttributes(array_merge($agendum->getAttributes(), ['short_code' => 'fsmgfv']));
    \Illuminate\Support\Facades\Route::forget... // can't easily remove; skip
    $feedbacks = $agendum->feedback()->with('answers')->latest()->get();
    $questions = $agendum->feedbackQuestions;
    $html = view('admin.agenda.feedback', compact('agendum','feedbacks','questions'))->render();
    echo "RENDER OK, length=" . strlen($html) . "\n";
} catch (\Throwable $e) {
    echo "RENDER FAILED: " . get_class($e) . "\n";
    echo $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
