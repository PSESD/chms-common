<?php
/**
 * Clock Hour Management System
 *
 * @copyright Copyright (c) 2016 Puget Sound Educational Service District
 * @license   MIT
 */
namespace CHMS\Common\Helpers;

class PdoDetector
{
    public static function detect()
    {
        if (env('DATABASE_URL')) {
            $url = parse_url(env('DATABASE_URL'));
            $components = [
                'scheme' => 'DB_CONNECTION',
                'user' => 'DB_USERNAME',
                'host' => 'DB_HOST',
                'pass' => 'DB_PASSWORD',
                'port' => 'DB_PORT',
                'path' => 'DB_DATABASE'
            ];
            $r = [];
            foreach ($components as $key => $envName) {
                if (isset($url[$key])) {
                    $value = $url[$key];
                    if ($key === 'path') {
                        $value = trim($value, '/');
                    }
                    $r[$envName] = $value;
                    putenv($envName.'='.$value);
                }
            }
        }
    }
}
