<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
$s = app(App\Services\MqttService::class);

// Clean slate
Cache::forget('mqtt.printer.99');

// 1) No status published → not active
echo "printerActive(99) [no status]: ".var_export($s->printerActive(99), true)."\n";

// 2) Publish a retained "online" status for admin 99, then re-check
try {
    $c = (new ConnectionSettings)->setConnectTimeout(3);
    $p = new MqttClient('165.22.50.240', 1883, 'testpub-'.uniqid(), MqttClient::MQTT_3_1_1);
    $p->connect($c);
    $p->publish('print/admin-99/status', 'online', 0, true); // retained
    $p->disconnect();
    echo "published retained 'online' to print/admin-99/status\n";
} catch (\Throwable $e) { echo "publish error: ".$e->getMessage()."\n"; }

Cache::forget('mqtt.printer.99');
echo "printerActive(99) [online published]: ".var_export($s->printerActive(99), true)."\n";

// 3) Flip it to offline (simulating LWT), re-check
try {
    $c = (new ConnectionSettings)->setConnectTimeout(3);
    $p = new MqttClient('165.22.50.240', 1883, 'testpub2-'.uniqid(), MqttClient::MQTT_3_1_1);
    $p->connect($c);
    $p->publish('print/admin-99/status', 'offline', 0, true); // retained
    $p->disconnect();
    echo "published retained 'offline' to print/admin-99/status\n";
} catch (\Throwable $e) { echo "publish error: ".$e->getMessage()."\n"; }

Cache::forget('mqtt.printer.99');
echo "printerActive(99) [offline published]: ".var_export($s->printerActive(99), true)."\n";

// Cleanup: clear the retained status for admin 99
try {
    $c = (new ConnectionSettings)->setConnectTimeout(3);
    $p = new MqttClient('165.22.50.240', 1883, 'testpub3-'.uniqid(), MqttClient::MQTT_3_1_1);
    $p->connect($c);
    $p->publish('print/admin-99/status', '', 0, true); // empty retained = remove
    $p->disconnect();
    Cache::forget('mqtt.printer.99');
    echo "cleared retained status\n";
} catch (\Throwable $e) { echo "cleanup error: ".$e->getMessage()."\n"; }
