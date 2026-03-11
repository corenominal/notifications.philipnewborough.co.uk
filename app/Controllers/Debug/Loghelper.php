<?php

namespace App\Controllers\Debug;

class Loghelper extends BaseController
{
    public function logit_example()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        $data['dump'] = logit('This is a log message from the logit helper function running on the Loghelper debug controller.', 0);

        $data['js']    = ['debug/debug'];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/default', $data);
    }
}
