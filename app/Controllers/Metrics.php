<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Metrics extends ResourceController
{
    public function receive()
    {
        $json = $this->request->getJSON(true);
        if (!$json) {
            return $this->respond(['status' => 'no data'], 400);
        }

        // 1. Interrogate Session for User Info
        // This assumes you are using CI4 Shield or standard Session library
        $userUuid = session()->get('user_uuid') ?? null;
        $username = session()->get('username') ?? 'guest';
        $isAdmin  = session()->get('is_admin') ? 1 : 0;

        // 2. Anonymize IP
        $ip = $this->request->getIPAddress();
        $anonymizedIp = $this->anonymizeIp($ip);

        // 3. User agent parsing (basic)
        $useragent = $this->request->getUserAgent();

        // 4. Prepare row for insertion
        $data = [
            'domain'        => $json['domain'] ?? 'unknown',
            'path'          => $json['path'] ?? '/',
            'user_uuid'     => $userUuid,
            'username'      => $username,
            'is_admin'      => $isAdmin,
            'device_type'   => $json['deviceType'] ?? 'desktop',
            'window_width'  => $json['windowWidth'] ?? 0,
            'window_height' => $json['windowHeight'] ?? 0,
            'useragent'    => $useragent->getAgentString(),
            'anonymized_ip' => $anonymizedIp,
            'load_time_ms'  => $json['interactiveTime'] ?? 0,
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        // 5. Make curl call to API endpoint
        $apiUrl = config('Urls')->metrics . 'api/metrics/receive';
        $apiKey = config('ApiKeys')->masterKey;
        $payload = json_encode($data);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_TIMEOUT        => 2,
            CURLOPT_HTTPHEADER     => [
            'Accept: application/json',
            'Content-Type: application/json',
            'apikey: ' . $apiKey,
            ],
        ]);

        try {
            $result = curl_exec($ch);
            if ($result === false) {
            throw new \Exception(curl_error($ch));
            } else {
                return $this->response->setJSON($result);
            }
        } catch (\Exception $e) {
            log_message('error', 'Metrics API call failed: ' . $e->getMessage());
        } finally {
            curl_close($ch);
        }

        // Send back JSON response
        // return $this->response->setJSON($data);
    }

    private function anonymizeIp($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return preg_replace('/[0-9]+$/', '0', $ip);
        }
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $parts = explode(':', $ip);
            // Keep first 3 segments (48 bits), zero out the rest
            return implode(':', array_slice($parts, 0, 3)) . '::';
        }

        return $ip;
    }
}