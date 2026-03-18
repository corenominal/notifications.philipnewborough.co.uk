<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    /**
     * Logs the current user out by destroying the active session
     * and redirecting to the configured authentication logout route.
     *
     * @return RedirectResponse Redirect response to the external/internal auth logout endpoint.
     */
    public function logout(): RedirectResponse
    {
        // Destroy the session
        session()->destroy();
        // Redirect to the auth logout page
        return redirect()->to(config('Urls')->auth . 'logout');
    }
}
