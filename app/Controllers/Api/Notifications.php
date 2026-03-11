<?php

namespace App\Controllers\Api;


class Notifications extends BaseController
{
    /**
     * Retrieves a list of notifications for a specific user or for all users ('everyone').
     *
     * This method fetches notifications from the `notifications` table, excluding those
     * that have been cleared (exist in the `cleared` table). It also joins the `read` table
     * to determine the read status of each notification. The results are ordered by the
     * creation date in descending order and limited to a specified number.
     *
     * @param string $user_uuid The user uuid for which to retrieve notifications.
     * @param int $limit The maximum number of notifications to retrieve (default is 20).
     *
     * @return \CodeIgniter\HTTP\Response JSON response containing the list of notifications.
     */
    public function index($user_uuid, $limit = 20)
    {
        // Random chance (1 in 10) to run garbage collection
        if (rand(1, 10) == 1) {
            $this->garbageCollect();
        }

        // Load Notifications model
        $model = model('NotificationModel');

        // Get notifications for the user or for everyone,
        // excluding cleared notifications
        // Join with the read table to get read status
        $sql = "SELECT n.*,
                EXISTS (
                    SELECT 1
                    FROM `read` r
                    WHERE r.notification_id = n.id
                    AND r.user_uuid = ?
                ) AS `read`
                FROM notifications n
                WHERE n.user_uuid IN (?, 'everyone')
                AND NOT EXISTS (
                    SELECT 1
                    FROM cleared c
                    WHERE c.notification_id = n.id
                    AND c.user_uuid = ?
                )
                ORDER BY n.created_at DESC
                LIMIT " . (int)$limit;

        $query = $model->query($sql, [$user_uuid,$user_uuid,$user_uuid]);
        $notifications = $query->getResultArray();

        $unread = 0;
        // Loop through the notifications and check if they are read or not
        foreach ($notifications as $key => $notification) {
            // If the notification is not read, increment the unread count
            if ($notification['read'] == 0) {
                $unread++;
            }
        }

        // Return the notifications as JSON
        return $this->response->setJSON(['unread' => $unread, 'notifications' => $notifications]);
    }

    /**
     * Performs garbage collection on old notifications.
     *
     * This method deletes notifications that are older than 30 days from the database.
     * It removes the corresponding entries from the `notifications`, `read`, and `cleared` tables.
     *
     * Steps:
     * 1. Loads the Notifications model.
     * 2. Retrieves all notifications older than 30 days using a SQL query.
     * 3. Iterates through the retrieved notifications and deletes:
     *    - The notification from the `notifications` table.
     *    - Associated entries from the `read` table.
     *    - Associated entries from the `cleared` table.
     *
     * Note:
     * - The `created_at` column in the `notifications` table is used to determine the age of notifications.
     * - The method uses raw SQL queries for deletion in the `read` and `cleared` tables.
     */
    private function garbageCollect()
    {
        // Load Notifications model
        $model = model('NotificationModel');

        // Get all notifications older than 30 days
        $sql = "SELECT * FROM notifications WHERE created_at < NOW() - INTERVAL 30 DAY";
        $query = $model->query($sql);
        $notifications = $query->getResultArray();
        // Loop through the notifications and delete them
        foreach ($notifications as $notification) {
            // Delete the notification from the notifications table
            $model->delete($notification['id']);
            // Delete the notification from the read table
            $model->query("DELETE FROM `read` WHERE notification_id = ?", [$notification['id']]);
            // Delete the notification from the cleared table
            $model->query("DELETE FROM cleared WHERE notification_id = ?", [$notification['id']]);
        }
    }
}