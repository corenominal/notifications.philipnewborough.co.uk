<?php

namespace App\Controllers\Debug;

use App\Controllers\BaseController;

class Home extends BaseController
{
    /**
     * Handles the index action for the Debug/Home controller.
     *
     * This method performs the following tasks:
     * - Loads the 'filesystem' helper to work with file operations.
     * - Retrieves all filenames from the 'Controllers/Debug' directory.
     * - Filters out specific files defined in the `$excludes` array.
     * - Prepares a list of filtered filenames (converted to lowercase and without the `.php` extension).
     * - Sets up JavaScript and CSS assets for the view.
     * - Sets the page title to 'Debug'.
     * - Passes the prepared data to the 'debug/home' view for rendering.
     *
     * @return \CodeIgniter\HTTP\Response|string The rendered view.
     */
    public function index()
    {
        // Load the file helper
        helper('filesystem');

        // Get all files in this directory
        $files = get_filenames(APPPATH . 'Controllers/Debug');

        // Filter the files
        $data['files'] = [];
        $excludes      = [
            'Home.php',
            'BaseController.php',
            'Rerouter.php',
        ];

        foreach ($files as $file) {
            if (! in_array($file, $excludes, true)) {
                $data['files'][] = strtolower(str_replace('.php', '', $file));
            }
        }

        $data['js'] = [
            'debug/debug',
            'debug/debug-home',
        ];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/home', $data);
    }
}
