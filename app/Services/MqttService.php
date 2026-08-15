<?php

namespace App\Services;

use App\Models\Registrant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;

class MqttService
{
    /**
     * Whether MQTT publishing is enabled.
     */
    public function enabled(): bool
    {
        return config('mqtt.enabled', false);
    }

    /**
     * Real MQTT health check — true only when MQTT is enabled AND the broker
     * is actually reachable. The result is cached briefly so page loads don't
     * connect to the broker on every request; lets the UI automatically follow
     * the real MQTT state (auto ON/OFF based on the broker).
     */
    public function isActive(int $ttlSeconds = 30): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        return Cache::remember('mqtt.is_active', $ttlSeconds, function () {
            try {
                $username = config('mqtt.username');
                $password = config('mqtt.password');

                $connection = (new ConnectionSettings)
                    ->setUsername($username !== '' ? $username : null)
                    ->setPassword($password !== '' ? $password : null)
                    ->setConnectTimeout(3) // fail fast for a status check
                    ->setKeepAliveInterval(10);

                $mqtt = new MqttClient(
                    config('mqtt.host'),
                    (int) config('mqtt.port'),
                    $this->clientId(),
                    MqttClient::MQTT_3_1_1
                );

                $mqtt->connect($connection);
                $mqtt->disconnect();

                return true;
            } catch (\Throwable $e) {
                Log::debug('MQTT health check failed: ' . $e->getMessage());

                return false;
            }
        });
    }

    /**
     * Topic the printer app uses to report its presence/status.
     *
     * The printer publishes a retained JSON heartbeat to
     * `status/admin-{userId}` every ~10 seconds, e.g.:
     *   {"status":"active","userId":3,"topic":"print/admin-3","timestamp":"..."}
     * Because the message is retained, any new subscriber immediately
     * receives the latest status (no waiting for the next heartbeat).
     */
    public function printerStatusTopic(int $adminId): string
    {
        return 'status/admin-' . $adminId;
    }

    /**
     * Whether the printer for a given admin is reporting ONLINE.
     *
     * Subscribes briefly to the printer's retained status topic
     * (status/admin-{adminId}) and reads the JSON "status" field:
     *   "active"   → printer online
     *   "inactive" → printer offline (published by the app or its Last Will)
     * If the status is still "active" but the heartbeat timestamp is stale
     * (>15s old), the printer is treated as offline (strict detection).
     *
     * Returns false when no status has been reported. Cached briefly to avoid
     * connecting to the broker on every page load.
     */
    public function printerActive(int $adminId, int $ttlSeconds = 30, bool $live = false): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        // A separate cache key for the live poll endpoint so its short TTL is
        // not blocked by the longer (30s) value cached by the page load.
        $key = $live ? 'mqtt.printer.live.' . $adminId : 'mqtt.printer.' . $adminId;

        return Cache::remember($key, $ttlSeconds, function () use ($adminId) {
            $topic = $this->printerStatusTopic($adminId);
            $online = null;

            try {
                $username = config('mqtt.username');
                $password = config('mqtt.password');

                $connection = (new ConnectionSettings)
                    ->setUsername($username !== '' ? $username : null)
                    ->setPassword($password !== '' ? $password : null)
                    ->setConnectTimeout(3)
                    ->setSocketTimeout(1)
                    ->setKeepAliveInterval(10);

                $mqtt = new MqttClient(
                    config('mqtt.host'),
                    (int) config('mqtt.port'),
                    $this->clientId(),
                    MqttClient::MQTT_3_1_1
                );

                $mqtt->connect($connection);
                $mqtt->subscribe($topic, function ($t, $message) use (&$online) {
                    $online = $this->parseOnline($message);
                }, 0);

                // Retained messages are delivered right after subscribing.
                // Use loopOnce() (NOT loop()) — loop() never exits while there is
                // an active subscription, which would hang the request.
                $start = microtime(true);
                while (microtime(true) - $start < 2 && $online === null) {
                    $mqtt->loopOnce(microtime(true), true);
                }
                $mqtt->disconnect();
            } catch (\Throwable $e) {
                Log::debug('MQTT printer status check failed: ' . $e->getMessage());

                return false;
            }

            // No status reported → printer is considered not active.
            return $online ?? false;
        });
    }

    /**
     * Presence check for MANY printers in a single MQTT connection.
     *
     * Returns [adminId => bool] for the given admin ids. Only the ids that are
     * not already cached are queried; they are all subscribed in ONE connection
     * (retained status messages arrive immediately), so checking e.g. 13 admins
     * takes ~1-2s instead of ~2s each. Results are cached to amortize cost.
     *
     * @param  array<int>  $adminIds
     * @return array<int, bool>
     */
    public function printersActive(array $adminIds, int $ttlSeconds = 90): array
    {
        $result = array_fill_keys(array_values($adminIds), false);

        if (!$this->enabled()) {
            return $result;
        }

        $needed = [];
        foreach ($adminIds as $id) {
            $cached = Cache::get('mqtt.printer.' . $id);
            if ($cached !== null) {
                $result[$id] = (bool) $cached;
            } else {
                $needed[] = $id;
            }
        }

        if (!$needed) {
            return $result;
        }

        $onlineMap = [];
        try {
            $username = config('mqtt.username');
            $password = config('mqtt.password');

            $connection = (new ConnectionSettings)
                ->setUsername($username !== '' ? $username : null)
                ->setPassword($password !== '' ? $password : null)
                ->setConnectTimeout(3)
                ->setSocketTimeout(1)
                ->setKeepAliveInterval(10);

            $mqtt = new MqttClient(
                config('mqtt.host'),
                (int) config('mqtt.port'),
                $this->clientId(),
                MqttClient::MQTT_3_1_1
            );

            $mqtt->connect($connection);

            // Subscribe to every printer's status topic on the same connection.
            foreach ($needed as $id) {
                $mqtt->subscribe($this->printerStatusTopic($id), function ($t, $message) use (&$onlineMap) {
                    if (preg_match('/admin-(\d+)$/', $t, $m)) {
                        $onlineMap[(int) $m[1]] = $this->parseOnline($message);
                    }
                }, 0);
            }

            // Retained messages are delivered right after subscribing; wait
            // until we have all of them or a short budget elapses.
            $start = microtime(true);
            while (microtime(true) - $start < 1.5 && count($onlineMap) < count($needed)) {
                $mqtt->loopOnce(microtime(true), true);
            }
            $mqtt->disconnect();
        } catch (\Throwable $e) {
            Log::debug('MQTT batch printer status check failed: ' . $e->getMessage());
        }

        foreach ($needed as $id) {
            $online = $onlineMap[$id] ?? false;
            Cache::put('mqtt.printer.' . $id, $online, $ttlSeconds);
            $result[$id] = $online;
        }

        return $result;
    }

    /**
     * Interpret a printer status payload.
     *
     * Handles the retained JSON heartbeat published by the printer app, e.g.
     * {"status":"active","userId":3,"topic":"print/admin-3","timestamp":"..."}
     * plus plain-text fallbacks ("active"/"online"/"1"/...).
     */
    protected function parseOnline(string $message): bool
    {
        $status = null;
        $timestamp = null;

        if (str_starts_with(ltrim($message), '{')) {
            try {
                $data = json_decode($message, true);
                if (is_array($data)) {
                    $status = strtolower(trim((string) ($data['status'] ?? '')));
                    $timestamp = $data['timestamp'] ?? null;
                }
            } catch (\Throwable $e) {
                $status = null;
            }
        }

        if ($status === null || $status === '') {
            $status = strtolower(trim($message));
        }

        $online = in_array($status, ['active', 'online', '1', 'true', 'on', 'ready', 'connected'], true);

        // Strict detection: still marked active but the heartbeat is stale (>15s).
        if ($online && $timestamp) {
            $ts = strtotime((string) $timestamp);
            if ($ts !== false && (time() - $ts) > 15) {
                return false;
            }
        }

        return $online;
    }

    /**
     * Unique client identifier (kept short; MQTT v3.1 limits ids to 23 chars).
     */
    protected function clientId(): string
    {
        $base = config('mqtt.client_id', 'msd26-server');
        return substr($base, 0, 18) . '-' . substr(uniqid(), -6);
    }

    /**
     * Publish a JSON payload to a topic. Returns true on success.
     */
    public function publish(string $topic, array $payload): bool
    {
        if (!$this->enabled()) {
            return false;
        }

        try {
            $username = config('mqtt.username');
            $password = config('mqtt.password');

            $connection = (new ConnectionSettings)
                ->setUsername($username !== '' ? $username : null)
                ->setPassword($password !== '' ? $password : null)
                ->setConnectTimeout(config('mqtt.connect_timeout', 5))
                ->setKeepAliveInterval(10);

            $mqtt = new MqttClient(
                config('mqtt.host'),
                (int) config('mqtt.port'),
                $this->clientId(),
                MqttClient::MQTT_3_1_1
            );

            $mqtt->connect($connection);
            $mqtt->publish($topic, json_encode($payload), 1);
            $mqtt->disconnect();

            return true;
        } catch (\Throwable $e) {
            Log::warning('MQTT publish failed on topic [' . $topic . ']: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Publish badge print messages (one per registrant) to the admin's topic
     * using a single MQTT connection. Topic: {topic_prefix}/admin-{adminId}
     * Printed participants are automatically marked as checked-in.
     *
     * @param \Illuminate\Support\Collection|array $registrants
     * @return int[] ids of the registrants whose badges were sent
     */
    public function publishBadges($registrants, int $adminId): array
    {
        if (!$this->enabled()) {
            return [];
        }

        // Topic uses the id of the logged-in user, e.g. print/admin-11 when
        // the user's id is 11 (matches the printer's subscribed topic).
        $topic = config('mqtt.topic_prefix', 'print') . '/admin-' . $adminId;
        $printedIds = [];

        try {
            $username = config('mqtt.username');
            $password = config('mqtt.password');

            $connection = (new ConnectionSettings)
                ->setUsername($username !== '' ? $username : null)
                ->setPassword($password !== '' ? $password : null)
                ->setConnectTimeout(config('mqtt.connect_timeout', 5))
                ->setKeepAliveInterval(10);

            $mqtt = new MqttClient(
                config('mqtt.host'),
                (int) config('mqtt.port'),
                $this->clientId(),
                MqttClient::MQTT_3_1_1
            );

            $mqtt->connect($connection);

            foreach ($registrants as $r) {
                $mqtt->publish($topic, json_encode($this->badgePayload($r)), 1);
                $printedIds[] = $r->id;
            }

            $mqtt->disconnect();

            // A printed badge means the participant has arrived on site: mark them checked-in.
            if ($printedIds) {
                \App\Models\Registrant::whereIn('id', $printedIds)
                    ->whereNull('checked_in_at')
                    ->update(['checked_in_at' => now()]);
            }
        } catch (\Throwable $e) {
            Log::warning('MQTT badge publish failed on topic [' . $topic . ']: ' . $e->getMessage());
        }

        return $printedIds;
    }

    /**
     * Build the badge print payload expected by the printer.
     */
    public function badgePayload(Registrant $r): array
    {
        $firstName = $r->first_name;
        $lastName  = $r->last_name;

        // Fall back to splitting the full name when first/last name are empty
        if (!$firstName && !$lastName && $r->name) {
            $parts = preg_split('/\s+/', trim($r->name));
            $firstName = $parts[0] ?? '';
            $lastName  = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
        }

        return [
            'objQRCode'    => $r->unique_code ?? ($r->qr_token ?? ''),
            'objName'      => $r->name ?? '',
            'objCompany'   => $r->company ?? '',
            'objFirstName' => $firstName ?? '',
            'objLastName'  => $lastName ?? '',
            'objJob'       => $r->job_title ?? ($r->job_role ?? ''),
            'objTrackCode' => $r->unique_code ?? '',
            'objTableNum'  => '',
        ];
    }
}
