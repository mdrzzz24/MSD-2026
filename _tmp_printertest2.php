<?php
set_time_limit(40);
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
$s = app(App\Services\MqttService::class);

// Unit-check parseOnline via reflection
$ref = new ReflectionMethod($s, 'parseOnline');
$ref->setAccessible(true);
foreach (['online','{"status":"online"}','offline','1','true','ready','OFFLINE'] as $v) {
    echo "parseOnline('$v') = ".var_export($ref->invoke($s, $v), true)."\n"; flush();
}

// 1) No status published yet (fresh admin id)
Cache::forget('mqtt.printer.77');
echo "1) no status: ".var_export($s->printerActive(77), true)."\n"; flush();

// 2) Publish retained 'online' then check
$c = (new ConnectionSettings)->setConnectTimeout(3);
$p = new MqttClient('165.22.50.240', 1883, 'tp-'.uniqid(), MqttClient::MQTT_3_1_1);
$p->connect($c); $p->publish('print/admin-77/status', 'online', 0, true); $p->disconnect();
echo "published online\n"; flush();
Cache::forget('mqtt.printer.77');
echo "2) online: ".var_export($s->printerActive(77), true)."\n"; flush();

// 3) Publish retained 'offline' then check
$p2 = new MqttClient('165.22.50.240', 1883, 'tp-'.uniqid(), MqttClient::MQTT_3_1_1);
$p2->connect($c); $p2->publish('print/admin-77/status', 'offline', 0, true); $p2->disconnect();
echo "published offline\n"; flush();
Cache::forget('mqtt.printer.77');
echo "3) offline: ".var_export($s->printerActive(77), true)."\n"; flush();

// Cleanup retained status
$p3 = new MqttClient('165.22.50.240', 1883, 'tp-'.uniqid(), MqttClient::MQTT_3_1_1);
$p3->connect($c); $p3->publish('print/admin-77/status', '', 0, true); $p3->disconnect();
Cache::forget('mqtt.printer.77');
echo "cleaned up\n";
