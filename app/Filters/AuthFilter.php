<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

// Configured in Config/Filters.php
class AuthFilter implements FilterInterface
{
    /**
     * Before Filter - Authenticates user session against auth server
     *
     * Validates user authentication by checking cookies and session data.
     * Makes a cURL request to the auth server to verify the token is still valid.
     *
     * @param RequestInterface $request The current request object
     * @param array|null $arguments Filter arguments (unused)
     * 
     * @return void|RedirectResponse Returns redirect response if:
     *         - User cookies are missing
     *         - Session token doesn't match cookie token
     *         - Auth server returns an error response
     *         Otherwise continues to the requested resource
     *
     * @throws \Exception If cURL request fails
     *
     * Cookie Requirements:
     *         - user_uuid: User's unique identifier
     *         - token: Session authentication token
     *
     * Session Data Set:
     *         - id: User ID
     *         - user_uuid: User UUID
     *         - username: Username
     *         - email: User email
     *         - realname: User's full name
     *         - created_at: Session creation timestamp
     *         - token: Authentication token
     *         - is_admin: Admin status boolean
     *         - groups: User group assignments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        // Get token cookie
        helper('cookie');
        $user_uuid = get_cookie('user_uuid');
        $token = get_cookie('token');
        // Set the URI
        $uri = $request->getUri();
        // Test if cookie is set
        if (!$user_uuid || !$token) {
            // Destroy session any existing session data
            $session->destroy();
            // Redirect to login page if cookie is not set
            return redirect()->to(config('Urls')->auth . 'login?redirect=' . urlencode($uri));
        }
        
        // Test session is set and matches cookie
        if(!$session->get('token') || $session->get('token') != $token)
        {
            // cURL GET request to auth server
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, config('Urls')->auth . 'api/session/' . $user_uuid . '/' . $token);
            // Set 'apikey' header
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('apikey: ' . config('ApiKeys')->masterKey));
            // Return the transfer as a string
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // $output contains the output string
            $response = curl_exec($ch);

            $response = json_decode($response);

            // Test for error response
            if(isset($response->error)){
                // Error response, so redirect to login
                return redirect()->to(config('Urls')->auth . '/login?redirect=' . urlencode($uri));
                exit;
            }

            // Set session data
            $data = array(
                'id' => $response->id,
                'user_uuid' => $response->uuid,
                'username' => $response->username,
                'email' => $response->email,
                'realname' => $response->realname,
                'created_at' => date('Y-m-d H:i:s'),
                'token' => $token,
                'is_admin' => $response->is_admin,
                'groups' => $response->groups,
                'apikey' => $response->apikey,
            );

            // Set session data
            $session->set($data);
        }

        // // This site is only for admins
        // if(!$session->get('is_admin')){
        //     // Destroy session any existing session data
        //     $session->destroy();
        //     // Redirect to login page if cookie is not set
        //     return redirect()->to('/unauthorised');
        // }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
    }
}
