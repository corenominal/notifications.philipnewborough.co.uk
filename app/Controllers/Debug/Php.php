<?php

namespace App\Controllers\Debug;

class Php extends BaseController
{
    public function show_php_info()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        return phpinfo();
    }

    public function show_available_extensions()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        $data['dump'] = get_loaded_extensions();

        $data['js']    = ['debug/debug'];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/default', $data);
    }

    public function show_upload_max_filesize()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        $data['dump'] = ini_get('upload_max_filesize');

        $data['js']    = ['debug/debug'];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/default', $data);
    }
}
