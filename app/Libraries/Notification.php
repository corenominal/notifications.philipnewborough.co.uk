<?php

namespace App\Libraries;

class Notification
{
    /**
     * Class Notification
     *
     * This class is responsible for handling notifications by interacting 
     * with the notification API.
     *
     * @property string $title         The title of the notification.
     * @property string $body          The body content of the notification.
     * @property string $url           The URL associated with the notification.
     * @property string $icon          The icon URL for the notification.
     * @property string $user_uuid      The user_uuid associated with the notification.
     * @property string $calltoaction  The call to action button text.
     */
    private $title;
    private $body;
    private $url;
    private $icon;
    private $user_uuid;
    private $calltoaction;

    /**
     * Set the title for the notification.
     *
     * @param string $title The title to set.
     * @return void
     */
    public function setTitle($title)
    {
        // Validate the title is a string
        if (!is_string($title)) {
            throw new \InvalidArgumentException("Title must be a string");
        }
        // Validate the title is not empty
        if (empty($title)) {
            throw new \InvalidArgumentException("Title cannot be empty");
        }
        $this->title = $title;
    }

    /**
     * Get the title of the notification.
     *
     * @return string The title of the notification.
     */
    public function getTitle()
    {
        return $this->title ?? null;
    }
    
    /**
     * Set the body for the notification.
     *
     * @param string $body The body to set.
     * @return void
     */
    public function setBody($body)
    {
        // Validate the body is a string
        if (!is_string($body)) {
            throw new \InvalidArgumentException("Body must be a string");
        }
        // Validate the body is not empty
        if (empty($body)) {
            throw new \InvalidArgumentException("Body cannot be empty");
        }
        $this->body = $body;
    }

    /**
     * Get the body of the notification.
     *
     * @return string The body of the notification.
     */
    public function getBody()
    {
        return $this->body ?? null;
    }

    /**
     * Set the URL for the notification.
     *
     * @param string $url The URL to set.
     * @return void
     */
    public function setUrl($url)
    {
        // Validate the URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid URL: {$url}");
        }
        $this->url = $url;
    }

    /**
     * Get the URL of the notification.
     *
     * @return string The URL of the notification.
     */
    public function getUrl()
    {
        return $this->url ?? null;
    }

    /**
     * Set the icon for the notification.
     *
     * @param string $icon The icon to set.
     * @return void
     */
    public function setIcon($icon)
    {
        // Validate the icon URL
        if (!filter_var($icon, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Invalid icon URL: {$icon}");
        }
        $this->icon = $icon;
    }

    /**
     * Get the icon of the notification.
     *
     * @return string The icon of the notification.
     */
    public function getIcon()
    {
        return $this->icon ?? null;
    }

    /**
     * Set the user_uuid for the notification.
     *
     * @param string|array $user_uuid The user_uuid to set.
     * @return void
     */
    public function setUseruuid($user_uuid)
    {
        // Validate the user_uuid is a string or an array
        if (!is_string($user_uuid) && !is_array($user_uuid)) {
            throw new \InvalidArgumentException("User_uuid must be a string or an array");
        }
        // Validate the user_uuid is not empty
        if (empty($user_uuid)) {
            throw new \InvalidArgumentException("Useruuid cannot be empty");
        }
        $this->user_uuid = $user_uuid;
    }

    /**
     * Get the user_uuid of the notification.
     *
     * @return string The user_uuid of the notification.
     */
    public function getUseruuid()
    {
        return $this->user_uuid ?? null;
    }

    /**
     * Set the call to action for the notification.
     *
     * @param string $calltoaction The call to action to set.
     * @return void
     */
    public function setCallToAction($calltoaction)
    {
        // Validate the call to action is a string
        if (!is_string($calltoaction)) {
            throw new \InvalidArgumentException("Call to action must be a string");
        }
        // Validate the call to action is not empty
        if (empty($calltoaction)) {
            throw new \InvalidArgumentException("Call to action cannot be empty");
        }
        $this->calltoaction = $calltoaction;
    }

    /**
     * Get the call to action of the notification.
     *
     * @return string The call to action of the notification.
     */
    public function getCallToAction()
    {
        return $this->calltoaction ?? null;
    }


    /**
     * Sends a notification payload to the Notifications API endpoint and returns the decoded JSON response.
     *
     * Required properties: title, body, url, icon, and user_uuid.
     * The call-to-action value is sent using the API field name `calltoaction` for compatibility.
     *
     * @return array<string, mixed> Decoded response body as an associative array.
     *
     * @throws \InvalidArgumentException If any required notification field is missing.
     * @throws \RuntimeException If the HTTP request fails or the API responds with a non-2xx status code.
     */
    public function send()
    {
        // Prepare the data to be sent
        $data = [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'icon' => $this->icon,
            'user_uuid' => $this->user_uuid,
            'calltoaction' => $this->calltoaction,
        ];

        // Throw an exception if any required fields are missing
        if (empty($this->title) || empty($this->body) || empty($this->url) || empty($this->icon) || empty($this->user_uuid)) {
            throw new \InvalidArgumentException("All fields are required");
        }

        // Send the notification using PHP native cURL
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => config('Urls')->notifications . '/api/notification',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
            'ApiKey: ' . config('ApiKeys')->masterKey,
            'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('cURL request failed: ' . $error);
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode < 200 || $httpCode >= 300) {
            // Show the response body for debugging
            throw new \RuntimeException('Notification API request failed with HTTP status ' . $httpCode);
        }

        // Wrap response so existing $response->getBody() call still works
        $response = new class($responseBody) {
            private $body;

            public function __construct($body)
            {
            $this->body = $body;
            }

            public function getBody()
            {
            return $this->body;
            }
        };

        // Decode the response
        $response = json_decode($response->getBody(), true);
        // Return response
        return $response;
    }
}