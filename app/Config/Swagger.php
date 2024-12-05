<?php
namespace Config;

use CodeIgniter\Config\BaseConfig;

class Swagger extends BaseConfig
{
    public $validateResponses = true;
    public $strict = false;
    public $generateAlways = true;
    
    public $paths = [
        APPPATH . 'Controllers',
        APPPATH . 'Models'
    ];
}