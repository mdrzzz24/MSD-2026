<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$agendum = \App\Models\AgendaItem::find(94);
$agendum->feedbackQuestions()->delete();
$agendum->update(['feedback_enabled' => 0]);
echo 'Session #94: questions cleared, feedback disabled (reverted)' . PHP_EOL;

$reg = \App\Models\Registrant::where('email', 'temp-cf@test.com')->first();
if ($reg) {
    echo 'Deleting temp registrant id=' . $reg->id . PHP_EOL;
    $reg->delete();
} else {
    echo 'Temp registrant already gone' . PHP_EOL;
}
echo 'Temp feedback rows: ' . \App\Models\AgendaFeedback::where('email', 'temp-cf@test.com')->count() . PHP_EOL;
