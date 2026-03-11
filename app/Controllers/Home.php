<?php

namespace App\Controllers;

class Home extends BaseController
{
    /**
     * Display the home page
     *
     * Renders the home view with associated stylesheets and scripts.
     * Sets up the page title and passes data to the view layer.
     *
     * @return string The rendered home view
     */
    public function index(): string
    {
        // Array of javascript files to include
        $data['js'] = ['home'];
        // Array of CSS files to include
        $data['css'] = ['home'];
        // Set the page title
        $data['title'] = 'Template Home';
        return view('home', $data);
    }
}
