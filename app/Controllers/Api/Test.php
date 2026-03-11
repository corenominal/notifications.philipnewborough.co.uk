<?php

namespace App\Controllers\Api;

class Test extends BaseController
{
    /**
     * Handles the ping request and returns a JSON response.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response containing the status and message.
     */
    public function ping()
    {
        // Return the records
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'pong',
        ]);
    }
}