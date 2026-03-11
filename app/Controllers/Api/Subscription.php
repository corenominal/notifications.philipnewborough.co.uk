<?php

namespace App\Controllers\Api;

class Subscription extends BaseController
{
    /**
     * Handles the insertion of a new subscription.
     *
     * This method expects a JSON payload in the request body containing the following required fields:
     * - user_uuid: string, the UUID of the user.
     * - endpoint: string, the push notification endpoint.
     * - p256dh: string, the user's public key.
     * - auth: string, the user's authentication secret.
     *
     * The method performs the following steps:
     * 1. Attempts to decode the JSON payload from the request body.
     * 2. Validates that all required fields are present and not empty.
     * 3. Trims whitespace from all string fields.
     * 4. Inserts the subscription data into the database using the Subscriptions model.
     * 5. Returns a JSON response with the inserted data or an error message if validation or insertion fails.
     *
     * @return \CodeIgniter\HTTP\Response JSON response containing the inserted data or an error message.
     */
    public function insert()
    {
        // Try to get the JSON data from the request
        try {
            $data = json_decode($this->request->getBody(), true);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON']);
        }

        // Test for required fields
        $requiredFields = ['user_uuid', 'endpoint', 'keys'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required field: ' . $field]);
            }
        }

        // Validate that the keys field contains the required subfields
        if (!isset($data['keys']['p256dh']) || !isset($data['keys']['auth'])) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required keys: p256dh or auth']);
        }

        // Set $data['p256dh'] and $data['auth'] from the keys subfield
        $data['p256dh'] = $data['keys']['p256dh'];
        $data['auth'] = $data['keys']['auth'];
        // Remove the keys subfield as it's no longer needed
        unset($data['keys']);

        // Load Subscriptions model
        $model = model('Subscriptions');
        // Insert the subscription data
        $model->insert($data);
            if ($model->errors()) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Database error: ' . implode(', ', $model->errors())]);
            }

        // Return data as JSON response
        return $this->response->setJSON($data);
    }
}