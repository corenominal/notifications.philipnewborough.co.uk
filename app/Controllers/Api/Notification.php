<?php

namespace App\Controllers\Api;

use Ramsey\Uuid\Uuid;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

/**
 * Handles API endpoints for managing notifications, including inserting,
 * clearing, and marking notifications as read, as well as dispatching
 * Web Push notifications to subscribed endpoints.
 */
class Notification extends BaseController
{
    /**
     * Inserts one or more notifications into the database and dispatches
     * Web Push notifications to the relevant subscriber endpoints.
     *
     * Accepts a JSON body with the following fields:
     *   - title       (string, required) Notification title.
     *   - body        (string, required) Notification body text.
     *   - url         (string, required) Target URL (must be a valid URL).
     *   - icon        (string, required) Icon URL (must be a valid URL).
     *   - user_uuid   (string|string[], required) One or more recipient UUIDs.
     *   - calltoaction (string, optional) Call-to-action label; defaults to 'More info'.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response containing the
     *         inserted notification data, or an error message on failure.
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
        $requiredFields = ['title', 'body', 'url', 'icon', 'user_uuid'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required field: ' . $field]);
            }
        }

        // Trim all fields
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // Test none of the required fields are empty
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Empty required field: ' . $field]);
            }
        }

        // Test URL is valid
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid URL']);
        }

        // Test icon is valid
        if (!filter_var($data['icon'], FILTER_VALIDATE_URL)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid icon URL']);
        }

        // Test if calltoaction is set, if not set to default
        if (!isset($data['calltoaction'])) {
            $data['calltoaction'] = 'More info';
        }
        // Test if calltoaction is empty, if so set to default
        if (empty($data['calltoaction'])) {
            $data['calltoaction'] = 'More info';
        }

        // Load Notifications model
        $model = model('NotificationModel');

        // Test if user_uuid is an array
        if (is_array($data['user_uuid'])) {
            // Insert notification for each user_uuid
            $ids = [];
            $uuids = [];
            $user_uuids = [];
            foreach ($data['user_uuid'] as $user_uuid) {
                // Create UUID for notification
                $data['uuid'] = Uuid::uuid4()->toString();
                // Set user_uuid
                $data['user_uuid'] = $user_uuid;
                // Insert notification into database
                $model->insert($data);
                if ($model->errors()) {
                    return $this->response->setStatusCode(500)->setJSON(['error' => 'Database error: ' . implode(', ', $model->errors())]);
                }
                // Send push notification
                $this->pushNotification($data);
                // Get the inserted notification ID
                $ids[] = $model->insertID();
                // Get the inserted notification UUID
                $uuids[] = $data['uuid'];
                // Add the user_uuid to the list
                $user_uuids[] = $user_uuid;
            }
            // Set the UUIDs, IDs and user_uuids in the response
            $data['uuids'] = $uuids;
            $data['ids'] = $ids;
            $data['user_uuids'] = $user_uuids;
            // Unset the user_uuid and UUID
            unset($data['user_uuid']);
            unset($data['uuid']);
        } else {
            // Insert notification for single user_uuid
            // Create UUID for notification
            $data['uuid'] = Uuid::uuid4()->toString();
            // Insert notification into database
            $model->insert($data);
            if ($model->errors()) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Database error: ' . implode(', ', $model->errors())]);
            }
            // Send push notification
            $this->pushNotification($data);
            // Get the inserted notification ID
            $data['id'] = $model->insertID();
        }

        // Return data as JSON response
        return $this->response->setJSON($data);
    }

    /**
     * Dispatches a Web Push notification to all subscriptions belonging to
     * the given user. Subscriptions that return a 404 or 410 status are
     * automatically removed from the database.
     *
     * @param array $data Notification data including at minimum:
     *                    - user_uuid (string)
     *                    - title     (string)
     *                    - body      (string)
     *                    - url       (string)
     * @return void
     */
    private function pushNotification($data){
        $config = config('WebPush');
        // VAPID authentication info
        $auth = [
            'VAPID' => [
                'subject'    => $config->subject,
                'publicKey'  => $config->publicKey,
                'privateKey' => $config->privateKey,
            ]
        ];
        $webPush = new WebPush($auth);
        // Load Subscriptions model
        $model = model('SubscriptionModel');
        // Get all subscriptions for the user_uuid
        $subscriptions = $model->where('user_uuid', $data['user_uuid'])->findAll();
        // If no subscriptions, return
        if (empty($subscriptions)) {
            return;
        }
        // Loop through the subscriptions and send the notification
        foreach ($subscriptions as $subscription) {
            // Create a Subscription object
            $sub = Subscription::create([
                'endpoint' => $subscription['endpoint'],
                'keys' => [
                    'p256dh' => $subscription['p256dh'],
                    'auth' => $subscription['auth'],
                ],
            ]);
            // Create payload for the notification
            $payload = json_encode([
                'title' => $data['title'],
                'body'  => $data['body'],
                'url'   => $data['url'],
            ]);
            // Queue the notification
            $webPush->queueNotification($sub, $payload);

            // Send queued notifications and handle failures
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    // Successfully sent notification
                    // logit("Notification sent successfully to $endpoint");
                } else {
                    // logit("Failed to send notification to $endpoint: " . $report->getReason());
                    $statusCode = $report->getResponse() ? $report->getResponse()->getStatusCode() : 0;
                    if (in_array($statusCode, [404, 410])) {
                        // If the endpoint is no longer valid, remove it from the database
                        $model->where('endpoint', $endpoint)->delete();
                        // logit("Removed expired subscription for endpoint: $endpoint");
                    }
                }
            }
        }
    }

    /**
     * Marks a single notification as cleared for the authenticated user.
     *
     * Reads the notification ID from the JSON request body and the user UUID
     * from the `user-uuid` request header. If the notification has not already
     * been cleared, a record is inserted into the cleared table.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response containing the
     *         cleared record data, or an error message on failure.
     */
    public function clear(){
        // Try to get the JSON data from the request
        try {
            $data = json_decode($this->request->getBody(), true);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON']);
        }

        // Set user_uuid from user-uuid header
        $data['user_uuid'] = $this->request->getHeaderLine('user-uuid');

        // Test for required fields
        $requiredFields = ['id', 'user_uuid'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required field: ' . $field]);
            }
        }

        // Trim all fields
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // Test none of the required fields are empty
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Empty required field: ' . $field]);
            }
        }

        // Load Notifications model
        $model = model('NotificationModel');

        // Test notification exists
        $notification = $model->find($data['id']);
        if (!$notification) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Notification not found']);
        }

        // Load Cleared model
        $model = model('ClearedModel');

        // id to notification_id
        $data['notification_id'] = $data['id'];
        unset($data['id']);

        // Test if notification is already cleared
        $cleared = $model->where('notification_id', $data['notification_id'])->where('user_uuid', $data['user_uuid'])->first();
        // If not cleared, insert into cleared table
        if (!$cleared) {
            $model->insert($data);
            if ($model->errors()) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Database error: ' . implode(', ', $model->errors())]);
            }
            // Get the inserted cleared ID
            $data['id'] = $model->insertID();
        }

        // Return data as JSON response
        return $this->response->setJSON($data);
    }

    /**
     * Marks all uncleared notifications as cleared for the authenticated user.
     *
     * Reads the user UUID from the `user-uuid` request header and inserts a
     * cleared record for every notification that targets the user (or 'everyone')
     * and does not yet have a corresponding cleared entry.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response with a success
     *         message, or an error message on failure.
     */
    public function clearall(){
        // Set user_uuid from user-uuid header
        $data['user_uuid'] = $this->request->getHeaderLine('user-uuid');

        // Test for required fields
        $requiredFields = ['user_uuid'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required field: ' . $field]);
            }
        }

        // Trim all fields
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // Test none of the required fields are empty
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Empty required field: ' . $field]);
            }
        }

        // Load Notifications model
        $model = model('NotificationModel');

        // Get all notifications that do not have a cleared record
        // in the cleared table
        $sql = "SELECT id FROM notifications 
                WHERE id NOT IN (
                    SELECT notification_id 
                    FROM `cleared` WHERE notification_id = notifications.id AND user_uuid = '" . $data['user_uuid'] . "')
                AND (user_uuid = '" . $data['user_uuid'] . "' OR user_uuid = 'everyone');";
        $notifications = $model->query($sql)->getResultArray();

        // Load Cleared model
        $model = model('ClearedModel');
        // For each notification, insert into cleared table
        foreach ($notifications as $notification) {
            $data['notification_id'] = $notification['id'];
            unset($notification['id']);
            // No need to test if notification exists as handled by the above query
            $model->insert($data);
            if ($model->errors()) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Database error: ' . implode(', ', $model->errors())]);
            }
        }

        // Return data as JSON response
        return $this->response->setJSON(['success' => 'All notifications cleared']);
    }

    /**
     * Marks a single notification as read for the authenticated user.
     *
     * Reads the notification ID from the JSON request body and the user UUID
     * from the `user-uuid` request header. If the notification has not already
     * been read, a record is inserted into the read table.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response containing the
     *         read record data, or an error message on failure.
     */
    public function read(){
        // Try to get the JSON data from the request
        try {
            $data = json_decode($this->request->getBody(), true);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid JSON']);
        }

        // Set user_uuid from user-uuid header
        $data['user_uuid'] = $this->request->getHeaderLine('user-uuid');

        // Test for required fields
        $requiredFields = ['id', 'user_uuid'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required field: ' . $field]);
            }
        }

        // Trim all fields
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // Test none of the required fields are empty
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Empty required field: ' . $field]);
            }
        }

        // Load Notifications model
        $model = model('NotificationModel');

        // Test notification exists
        $notification = $model->find($data['id']);
        if (!$notification) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Notification not found']);
        }

        // Load Read model
        $model = model('ReadModel');

        // id to notification_id
        $data['notification_id'] = $data['id'];
        unset($data['id']);

        // Test if notification is already read
        $read = $model->where('notification_id', $data['notification_id'])->where('user_uuid', $data['user_uuid'])->first();
        // If not read, insert into read table
        if (!$read) {
            $model->insert($data);
            if ($model->errors()) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Database error: ' . implode(', ', $model->errors())]);
            }
            // Get the inserted read ID
            $data['id'] = $model->insertID();
        }

        // Return data as JSON response
        return $this->response->setJSON($data);
    }

    /**
     * Marks all unread notifications as read for the authenticated user.
     *
     * Reads the user UUID from the `user-uuid` request header and inserts a
     * read record for every notification that targets the user (or 'everyone')
     * and does not yet have a corresponding read entry.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response with a success
     *         message, or an error message on failure.
     */
    public function readall(){
        // Get user_uuid from user-uuid header
        $data['user_uuid'] = $this->request->getHeaderLine('user-uuid');
        // Test for required fields
        $requiredFields = ['user_uuid'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing required field: ' . $field]);
            }
        }

        // Trim all fields
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        // Test none of the required fields are empty
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Empty required field: ' . $field]);
            }
        }

        // Load Notifications model
        $model = model('NotificationModel');

        // Get all notifications that do not have a read record
        // in the read table
        $sql = "SELECT id FROM notifications 
                WHERE id NOT IN (
                    SELECT notification_id 
                    FROM `read` WHERE notification_id = notifications.id AND user_uuid = '" . $data['user_uuid'] . "')
                AND (user_uuid = '" . $data['user_uuid'] . "' OR user_uuid = 'everyone');";
        $notifications = $model->query($sql)->getResultArray();

        // Load Read model
        $model = model('ReadModel');
        // For each notification, insert into read table
        foreach ($notifications as $notification) {
            $data['notification_id'] = $notification['id'];
            unset($notification['id']);
            // No need to test if notification exists as handled by the above query
            $model->insert($data);
            if ($model->errors()) {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Database error: ' . implode(', ', $model->errors())]);
            }
        }

        // Return data as JSON response
        return $this->response->setJSON(['success' => 'All notifications marked as read']);
    }
}