<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

// Configured in Config/Filters.php
class OptionalAuthFilter implements FilterInterface
{
    /**
     * Before Filter - Optionally hydrates user session on public routes
     *
     * If both cookies are present and session is not yet populated (or token
     * has changed), attempts to validate with the auth server and populate
     * the session. Never redirects — always continues to the requested resource.
     *
     * @param RequestInterface $request The current request object
     * @param array|null $arguments Filter arguments (unused)
     *
     * @return void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        helper('cookie');
        $user_uuid = get_cookie('user_uuid');
        $token     = get_cookie('token');

        // No cookies
        if (!$user_uuid || !$token) {
            // If session is populated but cookies are gone, clear the session immediately.
            // session->destroy() only removes storage; remove() also clears $_SESSION for this request.
            if ($session->get('user_uuid') && $session->get('token')) {
                $session->remove(['id', 'user_uuid', 'username', 'email', 'realname', 'created_at', 'token', 'is_admin', 'groups']);
                $session->destroy();
            }
            return;
        }

        // Cookies present but session not set (or token mismatch) — populate session
        if (!$session->get('token') || $session->get('token') !== $token) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('Urls')->auth . 'api/session/' . $user_uuid . '/' . $token);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['apikey: ' . config('ApiKeys')->masterKey]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $response = json_decode(curl_exec($ch));
            curl_close($ch);

            // Auth server error — continue without a session
            if (!$response || isset($response->error)) {
                return;
            }

            $session->set([
                'id'         => $response->id,
                'user_uuid'  => $response->uuid,
                'username'   => $response->username,
                'email'      => $response->email,
                'realname'   => $response->realname,
                'created_at' => date('Y-m-d H:i:s'),
                'token'      => $token,
                'is_admin'   => $response->is_admin,
                'groups'     => $response->groups,
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
