<?php

namespace App\Controllers\Debug;

class Session extends BaseController
{
    /**
     * Displays the current session data for debugging purposes.
     *
     * This method retrieves the session data stored in the `$_SESSION` superglobal
     * and prepares it for rendering in a debug view. It also includes metadata
     * such as the class name, function name, and additional assets (JavaScript and CSS).
     *
     * @return \CodeIgniter\HTTP\Response|string The rendered debug view with session data.
     */
    public function show_session_data()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        $data['dump'] = $_SESSION;

        $data['js']    = ['debug/debug'];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/default', $data);
    }
}
