<?php

namespace App\Services;

use App\Models\Registrant;
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
