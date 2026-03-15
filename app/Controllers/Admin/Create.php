<?php

namespace App\Controllers\Admin;

use Ramsey\Uuid\Uuid;
use App\Models\NotificationModel;

class Create extends BaseController
{
    /**
     * Display the create notification form.
     *
     * @return string
     */
    public function index()
    {
        $data['js']    = ['admin/create'];
        $data['css']   = ['admin/create'];
        $data['title'] = 'Create Notification';
        return view('admin/create', $data);
    }

    /**
     * Handle the create notification form submission.
     *
     * Accepts a JSON body with:
     *   - title        (string, required)
     *   - body         (string, required)
     *   - url          (string, required) Must be a valid URL.
     *   - icon         (string, optional) Must be a valid URL if provided.
     *   - calltoaction (string, optional) Defaults to "More info".
     *   - user_uuid    (string, required) Recipient UUID or "everyone".
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function create()
    {
        $input = $this->request->getJSON(true);

        $title        = isset($input['title'])        ? trim((string) $input['title'])        : '';
        $body         = isset($input['body'])         ? trim((string) $input['body'])         : '';
        $url          = isset($input['url'])          ? trim((string) $input['url'])          : '';
        $icon         = isset($input['icon'])         ? trim((string) $input['icon'])         : '';
        $calltoaction = isset($input['calltoaction']) ? trim((string) $input['calltoaction']) : '';
        $user_uuid    = isset($input['user_uuid'])    ? trim((string) $input['user_uuid'])    : '';

        $errors = [];

        if ($title === '') {
            $errors['title'] = 'Title is required.';
        }
        if ($body === '') {
            $errors['body'] = 'Body is required.';
        }
        if ($url === '') {
            $errors['url'] = 'URL is required.';
        } elseif (! filter_var($url, FILTER_VALIDATE_URL)) {
            $errors['url'] = 'URL must be a valid URL.';
        }
        if ($icon !== '' && ! filter_var($icon, FILTER_VALIDATE_URL)) {
            $errors['icon'] = 'Icon must be a valid URL.';
        }
        if ($user_uuid === '') {
            $errors['user_uuid'] = 'Recipient is required.';
        }

        if (! empty($errors)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors'  => $errors,
            ]);
        }

        if ($calltoaction === '') {
            $calltoaction = 'More info';
        }

        $uuid  = Uuid::uuid4()->toString();
        $model = model(NotificationModel::class);

        $model->insert([
            'uuid'         => $uuid,
            'title'        => $title,
            'body'         => $body,
            'url'          => $url,
            'icon'         => $icon,
            'calltoaction' => $calltoaction,
            'user_uuid'    => $user_uuid,
        ]);

        if ($model->errors()) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to create notification.',
            ]);
        }

        // Log the creation with UUID, title and recipient
        logit('Created notification. UUID: ' . $uuid . ' Title: ' . $title . ' User UUID: ' . $user_uuid, 0);

        return $this->response->setJSON([
            'success' => true,
            'uuid'    => $uuid,
        ]);
    }

    /**
     * Handle AJAX upload of an icon.
     * Expects a single file field named "icon". Validates PNG, square and >=256x256.
     * Uploads first to writable/uploads then moves to public/uploads/icons with a UUID filename.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function iconUpload()
    {
        $file = $this->request->getFile('icon');

        if (! $file || ! $file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'No file uploaded.']);
        }

        // Must be PNG
        $ext = strtolower($file->getClientExtension() ?: $file->getExtension());
        if ($ext !== 'png') {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Icon must be a PNG file.']);
        }

        $tmpName = $file->getTempName();
        if (! is_file($tmpName)) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Uploaded file missing.']);
        }

        $info = @getimagesize($tmpName);
        if (! $info) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Unable to read image.']);
        }

        [$width, $height, $type] = $info;
        if ($type !== IMAGETYPE_PNG) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Image must be PNG.']);
        }
        if ($width !== $height) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Image must be square.']);
        }
        if ($width < 256 || $height < 256) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Image must be at least 256x256 pixels.']);
        }

        // Two-step move: writable/uploads then public/uploads/icons
        $uuid = Uuid::uuid4()->toString();
        $writeDir = WRITEPATH . 'uploads/';
        if (! is_dir($writeDir)) {
            mkdir($writeDir, 0755, true);
        }
        // Test the write directory is writable
        if (! is_writable($writeDir)) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Upload directory is not writable.']);
        }

        $tmpFilename = $uuid . '.tmp';
        try {
            $file->move($writeDir, $tmpFilename);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to save uploaded file.']);
        }

        $src = $writeDir . $tmpFilename;
        $destDir = FCPATH . 'uploads/icons/';
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        // Test the destination directory is writable
        if (! is_writable($destDir)) {
            @unlink($src);
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Destination directory is not writable.']);
        }

        $dest = $destDir . $uuid . '.png';
        if (! @rename($src, $dest)) {
            // fallback to copy
            if (! @copy($src, $dest)) {
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Failed to move uploaded file.']);
            }
            @unlink($src);
        }

        $url = base_url('uploads/icons/' . $uuid . '.png');

        return $this->response->setJSON(['success' => true, 'url' => $url, 'filename' => $uuid . '.png']);
    }

    /**
     * Proxy a user search request to the auth service.
     * Keeps the API master key server-side.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function usersSearch()
    {
        $q = $this->request->getGet('q');
        $q = $q !== null ? substr(trim((string) $q), 0, 200) : '';

        $authBase = rtrim((string) config('Urls')->auth, '/');
        $apiKey   = (string) config('ApiKeys')->masterKey;

        if ($authBase === '') {
            return $this->response->setStatusCode(502)->setJSON(['success' => false, 'message' => 'Auth service not configured.']);
        }

        $url = $authBase . '/api/users/search?q=' . urlencode($q);

        $client = \Config\Services::curlrequest();
        try {
            $authResponse = $client->get($url, [
                'headers'     => ['apikey' => $apiKey],
                'http_errors' => false,
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(502)->setJSON(['success' => false, 'message' => 'Unable to reach auth service.']);
        }

        return $this->response
            ->setStatusCode($authResponse->getStatusCode())
            ->setBody($authResponse->getBody())
            ->setContentType('application/json');
    }

    /**
     * Return a JSON list of existing icons in public/uploads/icons.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function iconsList()
    {
        $dir = FCPATH . 'uploads/icons/';
        $icons = [];
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') continue;
                if (strtolower(pathinfo($f, PATHINFO_EXTENSION)) !== 'png') continue;
                $icons[] = base_url('uploads/icons/' . $f);
            }
        }

        return $this->response->setJSON(['success' => true, 'icons' => $icons]);
    }
}
