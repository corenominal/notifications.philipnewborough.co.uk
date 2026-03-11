<?php

namespace App\Controllers;

class Unauthorised extends BaseController
{
    /**
     * Handles the unauthorized access page.
     *
     * @return \CodeIgniter\HTTP\Response|string The rendered view for unauthorized access.
     */
    public function index()
    {
        // Array of javascript files to include
        $data['js'] = ['unauthorised'];
        // Array of CSS files to include
        $data['css'] = ['unauthorised'];
        $data['title'] = 'Access Denied';
        // Return login form view
        return $this->response->setStatusCode(403)->setBody(view('unauthorised', $data));
    }
}
