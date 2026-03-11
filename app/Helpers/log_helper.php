<?php
/**
 * Logs a message to an external logging service.
 *
 * This function sends a log message along with the current domain and log level
 * to a remote logging API. It uses CodeIgniter's HTTP client to perform the request.
 *
 * @param string $msg   The log message to be sent. Maximum length is 255 characters.
 *                      If the message exceeds this length, it will be truncated.
 *                      The message should not contain any sensitive information.
 *                      It is recommended to use a descriptive message that provides
 *                      context about the log entry.
 *                      Example: "User login attempt failed for user: johndoe"
 *                      Note: The message should be in English for better readability.
 *                      Avoid using special characters or emojis in the message.
 *                      Example: "Database connection error" is preferred over
 *                      "Database connection error 😱".
 * @param int    $level The log level (default is 0).
 *                      0: Info (default) - General information about the application state.
 *                      1: Warning - Indicates a potential issue that may require attention.
 *                      2: Error - Indicates a failure in the application that needs to be addressed.
 *                      3: Critical - Indicates a serious error that may prevent the application from functioning.
 *                      4: Debug - Indicates a critical condition that requires immediate attention.
 *
 * @return \CodeIgniter\HTTP\ResponseInterface The response from the logging API.
 *
 * @throws \Exception If the HTTP client encounters an error during the request.
 */

function logit($msg, $level = 0)
{
    // Get current domain
    $currentDomain = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Load API keys configuration and set master key
    $config = config('ApiKeys');
    $apiKey = $config->masterKey;

    // Use CodeIgniter's HTTP client to send the log message
    $apiurl = config('Urls')->logs . 'api/log';
    $payload = json_encode([
        'domain' => $currentDomain,
        'message' => $msg,
        'level'   => $level,
    ]);

    $ch = curl_init($apiurl);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'ApiKey: ' . $apiKey,
            'Content-Type: application/json',
        ],
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_TIMEOUT        => 10,
    ]);

    $responseBody = curl_exec($ch);
    $statusCode   = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError    = curl_error($ch);

    curl_close($ch);

    $response = [
        'status_code' => $statusCode,
        'body'        => $responseBody,
        'error'       => $curlError,
    ];

    // Check for errors in the response
    if ($responseBody === false || $statusCode !== 200) {
        error_log('Failed to log message: ' . ($curlError ?: $responseBody));
    }
    // Optionally, you can return the response for further processing
    return $response;
}


