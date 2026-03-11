<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * API Keys Configuration
 *
 * This configuration class stores API authentication keys used throughout the application.
 * It extends BaseConfig to inherit core configuration functionality from CodeIgniter.
 * 
 * The $masterKey property is intended to hold a master API key that can be used
 * for authentication. It can be set in the .env file or directly in this class for 
 * development purposes.
 *
 * @package Config
 */
class ApiKeys extends BaseConfig
{
    public $masterKey = '';
}