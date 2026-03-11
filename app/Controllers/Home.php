<?php

namespace App\Controllers;

class Home extends BaseController
{
    /**
     * Default controller action for the home route.
     *
     * Checks the current session for an `is_admin` flag and redirects accordingly:
     * - Redirects administrators to the admin dashboard (`/admin`).
     * - Redirects all other users to the configured top-level domain URL
     *   (`config('Urls')->tld`).
     *
     * @return \CodeIgniter\HTTP\RedirectResponse Redirect response to the appropriate destination.
     */
    public function index()
    {
        // If session has is_admin, redirect to admin dashboard
        if (session()->has('is_admin')) {
            return redirect()->to('/admin');
        } else {
            return redirect()->to(config('Urls')->tld);
        }

    }
}