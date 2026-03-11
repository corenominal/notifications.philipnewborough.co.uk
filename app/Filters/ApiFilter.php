<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiFilter implements FilterInterface
{
    /**
     * Filter to validate API requests before they are processed.
     *
     * This filter handles CORS policies and API key validation. It performs the following checks:
     * - Sets CORS headers to allow cross-origin requests
     * - Validates that an API key is provided in the request headers
     * - Checks the API key against the master key first
     * - If the master key doesn't match, validates against the authentication server
     * - Requires a user UUID header for non-master-key requests
     *
     * @param RequestInterface $request The incoming HTTP request object
     * @param array|null $arguments Additional arguments passed to the filter
     * @return void Exits with a JSON error response if validation fails
     *
     * @throws void Exits with 401 Unauthorized status and JSON error message if:
     *         - No API key is provided
     *         - No user UUID is provided (when master key doesn't match)
     *         - API key validation fails against the auth server
     *         - API key is invalid
     *
     * @uses config('ApiKeys') Configuration object containing master API key
     * @uses config('Urls') Configuration object containing auth server URL
     * @uses curl_init() To initialize cURL connection to auth server
     * @uses curl_setopt() To configure cURL options (URL, headers, return transfer)
     * @uses curl_exec() To execute the cURL request to auth server
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // CORS Policy
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PATCH, PUT, DELETE');
        header('Access-Control-Allow-Headers: apikey, user-uuid, email, Content-Type, Content-Length, Accept-Encoding');
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
