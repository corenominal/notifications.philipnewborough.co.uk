<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * User Configuration Class
 *
 * This class extends BaseConfig and handles user-specific configuration settings.
 * It stores basic user information including username and home directory path.
 *
 * @package Config
 * @property string $username The username for the user account
 * @property string $homedir  The home directory path for the user
 */
class User extends BaseConfig
{
    public $username = '';
    public $homedir = '';
}