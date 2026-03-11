<?php

namespace App\Controllers\Debug;

class Codeigniter_paths extends BaseController
{
    /**
     * Displays the paths used by the CodeIgniter framework.
     *
     * This method retrieves and organizes various paths used by the framework,
     * such as application, root, system, writable, and test paths. It also
     * provides the class name and function name for debugging purposes.
     *
     * @return \CodeIgniter\HTTP\Response|string The rendered view containing the debug information.
     */
    public function show_paths()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        // Array of CodeIgniter paths
        $paths = [
            'APPPATH'  => APPPATH,
            'ROOTPATH' => ROOTPATH,
            'SYSTEMPATH' => SYSTEMPATH,
            'WRITEPATH' => WRITEPATH,
            'TESTPATH' => TESTPATH,
        ];

        $data['dump'] = $paths;

        $data['js']    = ['debug/debug'];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/default', $data);
    }
}
