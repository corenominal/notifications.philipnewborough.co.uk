<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiFilter implements FilterInterface
{
    /**
     * Runs before incoming API requests to enforce CORS and API authentication rules.
     *
     * This filter:
     * - Sends CORS headers for cross-origin access.
     * - Short-circuits preflight `OPTIONS` requests.
     * - Requires an `apikey` header for all protected requests.
     * - Accepts either:
     *   - the configured master API key, or
     *   - a user-scoped API key validated via the external auth service (requires `user-uuid`).
     * - Enforces master-key-only access for `POST /api/notification`.
     *
     * On authentication failure, this method sends a `401 Unauthorized` response
     * with a JSON error payload and terminates execution.
     *
     * @param RequestInterface $request   The incoming HTTP request instance.
     * @param array<string>|null $arguments Optional filter arguments from route configuration.
     *
     * @return void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // CORS Policy
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, DELETE');
        header('Access-Control-Allow-Headers: apikey, user-uuid, email, Content-Type, Content-Length, Accept-Encoding, x-requested-with');
        if ('OPTIONS' === $_SERVER['REQUEST_METHOD']) {
            exit();
        }

        // Test API key is provided
        if (!$request->hasHeader('apikey')) {
            header('HTTP/1.1 401 Unauthorized', true, 401);
            exit(json_encode(['error' => 'No API key provided.']));
        }

        // Assign the API key
        $apikey = $request->header('apikey')->getValue();

        // Set success flag
        $success = false;
        
        // Test against 'apikeys.masterKey' .env values first, then database
        // Load API keys configuration
        $config = config('ApiKeys');
        if ($config->masterKey == $apikey) {
            $success = true;
        }

        if(!$success){
            // Reset success flag
            $success = true;
            // Test user UUID is provided
            if (!$request->hasHeader('user-uuid')) {
                header('HTTP/1.1 401 Unauthorized', true, 401);
                exit(json_encode(['error' => 'No user UUID provided.']));
            }
            // Get the user UUID from the header
            $user_uuid = $request->header('user-uuid')->getValue();
            // cURL GET request to auth server
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('Urls')->auth . 'api/keycheck/' . $user_uuid . '/' . $apikey);
            // Set 'apikey' header
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('apikey: ' . config('ApiKeys')->masterKey));
            // Return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec($ch);
            $response = json_decode($response);

            // Test for error response
            if(isset($response->error)){
                // Error response, set flag to false
                $success = false;
            }

            // Test for POST request to /api/notification endpoint, if so, require master key
            if ($request->getMethod() === 'post' && $request->getUri()->getPath() === '/api/notification') {
                if ($config->masterKey != $apikey) {
                    header('HTTP/1.1 401 Unauthorized', true, 401);
                    exit(json_encode(['error' => 'Invalid API key for this endpoint. Master key required.']));
                }
            }
        }

        // Test flag
        if (!$success) {
            header('HTTP/1.1 401 Unauthorized', true, 401);
            exit(json_encode(['error' => 'Invalid API key.']));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
