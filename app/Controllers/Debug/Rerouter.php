<?php

namespace App\Controllers\Debug;

class Rerouter extends BaseController
{
    /**
     * Reroutes the request to a specified controller and method dynamically.
     *
     * @param string      $controller The controller class name (within the `App\Controllers\Debug` namespace).
     * @param string|bool $method     The method to call on the controller. Defaults to `index` if not provided.
     *
     * @return mixed The result of the called controller method.
     *
     * @throws \CodeIgniter\Exceptions\PageNotFoundException If the controller class or method does not exist.
     */
    public function reroute($controller, $method = false)
    {
        $class = ucwords($controller);
        $class = 'App\\Controllers\\Debug\\' . $class;

        if (! class_exists($class)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $controller = new $class();

        if ($method && ! method_exists($controller, $method)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        if (! $method) {
            return $controller->index();
        }

        return $controller->{$method}();
    }
}
