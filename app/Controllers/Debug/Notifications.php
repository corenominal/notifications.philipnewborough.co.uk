<?php

namespace App\Controllers\Debug;

use App\Libraries\Notification;

class Notifications extends BaseController
{
    /**
     * Sends a notification to a user.
     *
     * This method creates a notification with a title, body, icon, and URL,
     * and sends it to the user specified by the current session's username.
     * The result of the notification sending process is stored in the `$data['dump']` variable.
     * The method also prepares additional data for rendering a debug view.
     *
     * @return \CodeIgniter\HTTP\Response|string The rendered view for debugging.
     */
    public function send_notification_to_user()
    {
        // Get the class name
        $class         = str_replace(__NAMESPACE__, '', static::class);
        $data['class'] = ltrim($class, '\\');
        // Store the function name
        $data['function'] = __FUNCTION__;

        $notification = new Notification();
        $notification->setTitle('Test Title');
        $notification->setBody('This is a test notification body.');
        $notification->setIcon(config('Urls')->notifications . 'icon.png');
        $notification->setUrl(config('Urls')->notifications . 'debug/notifications');
        $notification->setCalltoaction('Test Call to Action');
        $notification->setUseruuid($_SESSION['user_uuid']);

        $data['dump'] = $notification->send();

        $data['js']    = ['debug/debug'];
        $data['css']   = [];
        $data['title'] = 'Debug';

        return view('debug/default', $data);
    }
}
