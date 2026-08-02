<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MQTT (badge printer) configuration
    |--------------------------------------------------------------------------
    |
    | When MQTT_ENABLED=true, pressing the "Print" button on the Onsite Event
    | page publishes a JSON message to the broker so the badge printer prints.
    |
    */

    'enabled' => (bool) env('MQTT_ENABLED', false),

    // Broker connection
    'host' => env('MQTT_HOST', '127.0.0.1'),
    'port' => (int) env('MQTT_PORT', 1883),

    // Optional credentials (leave empty when the broker does not require auth)
    'username' => env('MQTT_USERNAME', ''),
    'password' => env('MQTT_PASSWORD', ''),

    // Client identifier prefix (a unique suffix is appended per connection)
    'client_id' => env('MQTT_CLIENT_ID', 'msd26-server'),

    // Topic prefix. The printer listens on "print/admin-{adminId}", so the
    // full topic becomes: {topic_prefix}/admin-{adminId}, where {adminId} is
    // the id of the logged-in user (Auth::id()).
    'topic_prefix' => env('MQTT_TOPIC_PREFIX', 'print'),

    // Connection timeout (seconds) — publish fails fast when broker is down
    'connect_timeout' => (int) env('MQTT_CONNECT_TIMEOUT', 5),

];
