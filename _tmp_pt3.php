<?php
set_time_limit(25);
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
$s = app(App\Services\MqttService::class);

$c = (new ConnectionSettings)->setConnectTimeout(3)->setSocketTimeout(1);
// publish retained online
$p = new MqttClient('165.22.50.240', 1883, 'tp-'.uniqid(), MqttClient::MQTT_3_1_1);
$p->connect($c); $p->publish('print/admin-77/status', 'online', 0, true); $p->disconnect();
echo "published online\n"; flush();

$t0 = microtime(true);
Cache::forget('mqtt.printer.77');
echo "printerActive(77) = ".var_export($s->printerActive(77), true)." (".round(microtime(true)-$t0,1)."s)\n"; flush();

$t0 = microtime(true);
Cache::forget('mqtt.printer.77');
echo "printerActive(77) cached recheck = ".var_export($s->printerActive(77), true)." (".round(microtime(true)-$t0,1)."s)\n"; flush();

// cleanup
$p2 = new MqttClient('165.22.50.240', 1883, 'tp-'.uniqid(), MqttClient::MQTT_3_1_1);
$p2->connect($c); $p2->publish('print/admin-77/status', '', 0, true); $p2->disconnect();
Cache::forget('mqtt.printer.77');
echo "cleaned\n";
