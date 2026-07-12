<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TimeSlots ===\n";
foreach (\App\Models\TimeSlot::ordered()->get() as $ts) {
    $k = $ts->start_time . '-' . $ts->end_time;
    echo "{$ts->id}: {$ts->start_time} - {$ts->end_time} (order: {$ts->order}) key='$k'\n";
}

echo "\n=== Rooms ===\n";
foreach (\App\Models\Room::ordered()->get() as $r) {
    echo "{$r->id}: {$r->name}\n";
}

echo "\n=== ItemMap ===\n";
$items = \App\Models\AgendaItem::ordered()->get();
$rooms = \App\Models\Room::ordered()->get();
$roomNames = $rooms->pluck('name')->toArray();
echo "Room names: " . json_encode($roomNames) . "\n";

$itemMap = [];
foreach ($items as $item) {
    $key = $item->start_time . '-' . $item->end_time;
    $itemMap[$key][] = $item;
}

foreach ($itemMap as $key => $items) {
    echo "Key '$key':\n";
    foreach ($items as $item) {
        echo "  - id={$item->id} title={$item->title} room=" . ($item->room ?: 'null') . "\n";
    }
}

echo "\n=== Check 13:00-13:30 ===\n";
$ts = $timeSlots = \App\Models\TimeSlot::ordered()->get()->firstWhere('start_time', '13:00:00');
if ($ts) {
    $slotKey = $ts->start_time . '-' . $ts->end_time;
    echo "TimeSlot key: '$slotKey'\n";
    echo "Exists in itemMap: " . (isset($itemMap[$slotKey]) ? 'YES' : 'NO') . "\n";
    if (isset($itemMap[$slotKey])) {
        foreach ($itemMap[$slotKey] as $item) {
            echo "  - id={$item->id} title={$item->title} room=" . ($item->room ?: 'null') . "\n";
        }
    }
}

echo "\n=== Find Track A1 ===\n";
\$items = \App\Models\AgendaItem::where('title', 'LIKE', '%Track A1%')->get();
echo 'Found ' . \$items->count() . " items:\n";
foreach (\$items as \$item) {
    echo "id={\$item->id} created_at={\$item->created_at} start_time={\$item->start_time} end_time={\$item->end_time} room={\$item->room}\n";
}

echo "\n=== Newest 5 items ===\n";
\$newest = \App\Models\AgendaItem::orderBy('id', 'desc')->take(5)->get();
foreach (\$newest as \$item) {
    echo "id={\$item->id} title={\$item->title} start_time={\$item->start_time} end_time={\$item->end_time} room={\$item->room} created_at={\$item->created_at}\n";
}

echo "\n=== Check keys ===\n";
echo "TimeSlot 5 key: '" . \App\Models\TimeSlot::find(5)->start_time . "-" . \App\Models\TimeSlot::find(5)->end_time . "'\n";
echo "Track A1 key: '" . \App\Models\AgendaItem::find(7)->start_time . "-" . \App\Models\AgendaItem::find(7)->end_time . "'\n";
echo "Match: " . (\App\Models\TimeSlot::find(5)->start_time . '-' . \App\Models\TimeSlot::find(5)->end_time === \App\Models\AgendaItem::find(7)->start_time . '-' . \App\Models\AgendaItem::find(7)->end_time ? 'YES' : 'NO') . "\n";
