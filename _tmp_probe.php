<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

// 1) Try EMQX HTTP API ports (unauthenticated → we just want to detect branding)
foreach ([18083, 8081, 8083, 1883] as $port) {
    $url = "http://165.22.50.240:$port/";
    $ctx = stream_context_create(["http"=>["timeout"=>3, "ignore_errors"=>true]]);
    $body = @file_get_contents($url, false, $ctx);
    $hdr  = $http_response_header ?? [];
    $status = 0;
    if (preg_match('/\s(\d{3})\s/', $hdr[0] ?? '', $m)) $status = (int)$m[1];
    $snip = is_string($body) ? substr($body,0,80) : '';
    printf("HTTP :%d → status=%d snip=%s\n", $port, $status, preg_replace('/\s+/',' ',trim($snip)));
}

// 2) MQTT $SYS broker version (works for Mosquitto & EMQX)
try {
    $conn = (new ConnectionSettings)->setConnectTimeout(3);
    $m = new MqttClient('165.22.50.240', 1883, 'probe-'.uniqid(), MqttClient::MQTT_3_1_1);
    $m->connect($conn);
    $found = [];
    $m->subscribe('$SYS/broker/version', function($t,$msg) use (&$found){ $found[] = "$t = $msg"; }, 0);
    $m->subscribe('$SYS/brokers/+/version', function($t,$msg) use (&$found){ $found[] = "$t = $msg"; }, 0);
    $start = microtime(true);
    while (microtime(true)-$start < 3) { $m->loop(0.5, true); if ($found) break; }
    echo "SYS version: ".(count($found)? implode('; ', $found) : '(none)')."\n";
    $m->disconnect();
} catch (\Throwable $e) {
    echo "MQTT $SYS probe error: ".$e->getMessage()."\n";
}
