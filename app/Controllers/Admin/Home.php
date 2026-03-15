<?php

namespace App\Controllers\Admin;

use Hermawan\DataTables\DataTable;

class Home extends BaseController
{
    /**
     * Display the Admin Dashboard page.
     *
     * Prepares view data for the dashboard, including:
     * - Datatables feature flag
     * - JavaScript asset list
     * - CSS asset list
     * - Page title
     *
     * @return string Rendered admin dashboard view output.
     */
    public function index()
    {
        // Datatables flag
        $data['datatables'] = true;
        // Array of javascript files to include
        $data['js'] = ['admin/home'];
        // Array of CSS files to include
        $data['css'] = ['admin/home'];
        // Set the page title
        $data['title'] = 'Admin Dashboard';    
        return view('admin/home', $data);
    }

    /**
     * Returns server-side DataTables JSON for the notifications table.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function datatable()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('notifications')
                      ->select('id, uuid, icon, title, body, calltoaction, created_at, user_uuid')
                      ->select("CASE WHEN user_uuid = 'everyone' THEN NULL ELSE EXISTS(SELECT 1 FROM `read` r WHERE r.notification_id = notifications.id AND r.user_uuid = notifications.user_uuid) END AS is_read", false)
                      ->select("CASE WHEN user_uuid = 'everyone' THEN NULL ELSE EXISTS(SELECT 1 FROM cleared c WHERE c.notification_id = notifications.id AND c.user_uuid = notifications.user_uuid) END AS is_cleared", false);

        return DataTable::of($builder)
            ->add('DT_RowId', function ($row) {
                return $row->uuid;
            }, 'first')
            ->edit('icon', function ($row) {
                $src = esc((string) $row->icon);
                return $src
                    ? '<img src="' . $src . '" alt="" style="width:2rem;height:2rem;object-fit:cover;">'
                    : '<span class="text-muted">—</span>';
            })
            ->edit('body', function ($row) {
                $body = (string) $row->body;
                return mb_strlen($body) > 100 ? esc(mb_substr($body, 0, 97)) . '...' : esc($body);
            })
            ->edit('is_read', function ($row) {
                if ($row->user_uuid === 'everyone') {
                    return '<span class="text-muted">—</span>';
                }
                return $row->is_read
                    ? '<span class="badge text-bg-success">Read</span>'
                    : '<span class="badge text-bg-secondary">Unread</span>';
            })
            ->edit('is_cleared', function ($row) {
                if ($row->user_uuid === 'everyone') {
                    return '<span class="text-muted">—</span>';
                }
                return $row->is_cleared
                    ? '<span class="badge text-bg-success">Cleared</span>'
                    : '<span class="badge text-bg-secondary">Not cleared</span>';
            })
            ->add('actions', function ($row) {
                $uuid = esc($row->uuid);
                return '<div class="btn-group btn-group-sm" role="group">'
                     . '<button type="button" class="btn btn-outline-primary btn-edit-row" data-uuid="' . $uuid . '" title="Edit"><i class="bi bi-pencil-fill"></i></button>'
                     . '<button type="button" class="btn btn-outline-primary btn-delete-row" data-uuid="' . $uuid . '" title="Delete"><i class="bi bi-trash-fill"></i></button>'
                     . '</div>';
            })
            ->hide('id')
            ->hide('uuid')
            ->hide('user_uuid')
            ->toJson(true);
    }

    public function delete()
    {
        $input = $this->request->getJSON(true);
        $uuids = $input['uuids'] ?? [];

        if (empty($uuids) || ! is_array($uuids)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No UUIDs provided.',
            ]);
        }

        // Sanitise: ensure all values are non-empty strings
        $uuids = array_filter(array_map('strval', $uuids), fn ($v) => $v !== '');

        if (empty($uuids)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'No valid UUIDs provided.',
            ]);
        }

        $model = model('NotificationModel');
        $db    = \Config\Database::connect();

        $db->table('notifications')
           ->whereIn('uuid', array_values($uuids))
           ->delete();

        return $this->response->setJSON([
            'success' => true,
            'deleted' => count($uuids),
        ]);
    }

    public function getNotification(string $uuid)
    {
        $db  = \Config\Database::connect();
        $row = $db->table('notifications')
                  ->select('id, uuid, title, body, url, calltoaction, user_uuid')
                  ->select("CASE WHEN user_uuid = 'everyone' THEN NULL ELSE EXISTS(SELECT 1 FROM `read` r WHERE r.notification_id = notifications.id AND r.user_uuid = notifications.user_uuid) END AS is_read", false)
                  ->select("CASE WHEN user_uuid = 'everyone' THEN NULL ELSE EXISTS(SELECT 1 FROM cleared c WHERE c.notification_id = notifications.id AND c.user_uuid = notifications.user_uuid) END AS is_cleared", false)
                  ->where('uuid', $uuid)
                  ->get()
                  ->getRowArray();

        if (! $row) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Notification not found.',
            ]);
        }

        return $this->response->setJSON(['success' => true, 'data' => $row]);
    }

    public function update()
    {
        $input = $this->request->getJSON(true);
        $uuid  = isset($input['uuid'])  ? trim((string) $input['uuid'])  : '';
        $title = isset($input['title']) ? trim((string) $input['title']) : '';
        $body  = isset($input['body'])  ? trim((string) $input['body'])  : '';
        $url          = isset($input['url'])          ? trim((string) $input['url'])          : '';
        $calltoaction = isset($input['calltoaction']) ? trim((string) $input['calltoaction']) : '';

        if ($uuid === '' || $title === '' || $body === '' || $url === '') {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'uuid, title, body, and url are required.',
            ]);
        }

        $db           = \Config\Database::connect();
        $notification = $db->table('notifications')
                           ->select('id, user_uuid')
                           ->where('uuid', $uuid)
                           ->get()
                           ->getRowArray();

        if (! $notification) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Notification not found.',
            ]);
        }

        $db->table('notifications')
           ->where('uuid', $uuid)
           ->update(['title' => $title, 'body' => $body, 'url' => $url, 'calltoaction' => $calltoaction]);

        // Handle read/cleared status toggles for non-everyone notifications
        if ($notification['user_uuid'] !== 'everyone') {
            $notificationId = (int) $notification['id'];
            $userUuid       = $notification['user_uuid'];

            if (array_key_exists('is_read', $input)) {
                $isRead   = (bool) $input['is_read'];
                $existing = $db->table('read')
                               ->where('notification_id', $notificationId)
                               ->where('user_uuid', $userUuid)
                               ->get()->getRowArray();
                if ($isRead && ! $existing) {
                    $db->table('read')->insert(['notification_id' => $notificationId, 'user_uuid' => $userUuid]);
                } elseif (! $isRead && $existing) {
                    $db->table('read')
                       ->where('notification_id', $notificationId)
                       ->where('user_uuid', $userUuid)
                       ->delete();
                }
            }

            if (array_key_exists('is_cleared', $input)) {
                $isCleared = (bool) $input['is_cleared'];
                $existing  = $db->table('cleared')
                                ->where('notification_id', $notificationId)
                                ->where('user_uuid', $userUuid)
                                ->get()->getRowArray();
                if ($isCleared && ! $existing) {
                    $db->table('cleared')->insert(['notification_id' => $notificationId, 'user_uuid' => $userUuid]);
                } elseif (! $isCleared && $existing) {
                    $db->table('cleared')
                       ->where('notification_id', $notificationId)
                       ->where('user_uuid', $userUuid)
                       ->delete();
                }
            }
        }

        return $this->response->setJSON(['success' => true]);
    }
}
