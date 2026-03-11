<?php

namespace App\Controllers;

class Errors extends BaseController
{
    public function show404()
    {
        $data['js'] = ['not_found'];
        $data['css'] = ['not_found'];
        $data['title'] = 'Page Not Found';
        return $this->response->setStatusCode(404)->setBody(view('not_found', $data));
    }
}
