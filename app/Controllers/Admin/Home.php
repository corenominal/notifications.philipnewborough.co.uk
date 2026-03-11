<?php

namespace App\Controllers\Admin;

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
}
