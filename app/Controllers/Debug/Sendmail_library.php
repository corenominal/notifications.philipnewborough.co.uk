<?php

namespace App\Controllers\Debug;

use App\Libraries\Sendmail;

class Sendmail_library extends BaseController
{
    public function send_example_message()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        $data['dump'] = (new Sendmail())
                    ->setFrom(config('Email')->fromEmail)
                    ->setTo(config('Email')->toEmail)
                    ->setSubject('Test email from CodeIgniter 4')
                    ->setBody('<p>This is a test email sent from CodeIgniter 4 using the Sendmail library. There is nothing to see here, please move along.</p>')
                    ->setMailtype(Sendmail::MAILTYPE_HTML)
                    ->send();

        $data['js']    = ['debug/debug'];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/default', $data);
    }
}
