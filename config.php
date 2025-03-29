<?php
require __DIR__ . '/vendor/autoload.php';

if (!function_exists('getEnvVar')) {
    function getEnvVar($key)
    {
        static $envLoaded = false;

        if (!$envLoaded) {
            if (file_exists(__DIR__ . '/.env')) {
                $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
                try {
                    $dotenv->load();
                } catch (Exception $e) {
                }
            }
            $envLoaded = true;
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        $systemEnv = getenv($key);
        if ($systemEnv !== false) {
            return $systemEnv;
        }

        return null;
    }
}

$config = [
    'MONGODB_URI' => getEnvVar('MONGODB_URI'),
    'HOST' => getEnvVar('HOST'),

    'STRIPE_SECRET_KEY' => getEnvVar('STRIPE_SECRET_KEY'),
    'STRIPE_PUBLISHABLE_KEY' => getEnvVar('STRIPE_PUBLISHABLE_KEY'),

    'SMTP_HOST' => getEnvVar('SMTP_HOST'),
    'SMTP_USERNAME' => getEnvVar('SMTP_USERNAME'),
    'SMTP_PASSWORD' => getEnvVar('SMTP_PASSWORD'),
    'SMTP_PORT' => getEnvVar('SMTP_PORT'),
    'SMTP_FROM_ADDRESS' => getEnvVar('SMTP_FROM_ADDRESS'),
    'SMTP_FROM_NAME' => getEnvVar('SMTP_FROM_NAME'),

    'BACKEND_URL' => getEnvVar('BACKEND_URL'),

    'EMAILJS_PUBLIC_KEY' => getEnvVar('EMAILJS_PUBLIC_KEY'),
    'EMAILJS_SERVICE_ID' => getEnvVar('EMAILJS_SERVICE_ID'),
    'EMAILJS_TEMPLATE_ID' => getEnvVar('EMAILJS_TEMPLATE_ID')
];

$requiredVars = [
    'MONGODB_URI',
    'HOST',
    'STRIPE_SECRET_KEY',
    'STRIPE_PUBLISHABLE_KEY',
    'SMTP_HOST',
    'SMTP_USERNAME',
    'SMTP_PASSWORD',
    'SMTP_PORT',
    'SMTP_FROM_ADDRESS',
    'SMTP_FROM_NAME',
    'BACKEND_URL',
    'EMAILJS_PUBLIC_KEY',
    'EMAILJS_SERVICE_ID',
    'EMAILJS_TEMPLATE_ID'
];

foreach ($requiredVars as $var) {
    if (!$config[$var]) {
        die("Required environment variable not found: $var");
    }
}

return $config;