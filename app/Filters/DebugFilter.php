<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

// Configured in Config/Filters.php
class DebugFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Test if user is authenticated as an IT user
        $session = session();
        $auth = $session->get('is_admin');
        if (!$auth) {
            // Redirect to unauthorised page
            return redirect()->to('/unauthorised');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
