<?php

/**
 * This file is part of the PHP Telegram Bot example-bot package.
 * https://github.com/php-telegram-bot/example-bot/
 *
 * (c) PHP Telegram Bot Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This file contains all the configuration options for the PHP Telegram Bot.
 *
 * It is based on the configuration array of the PHP Telegram Bot Manager project.
 *
 * Simply adjust all the values that you need and extend where necessary.
 *
 * Options marked as [Manager Only] are only required if you use `manager.php`.
 *
 * For a full list of all options, check the Manager Readme:
 * https://github.com/php-telegram-bot/telegram-bot-manager#set-extra-bot-parameters
 */

use Longman\TelegramBot\Entities\Update;

function getBotConfig() {

    $botToken = env('BOT_TOKEN', '');
    $botUsername = env('BOT_USERNAME', '');
    $hookUrl = env('BOT_HOOK_URL', '');
    $hookKey = env('BOT_PRIVATE_KEY', '');

    $commandPath = env('BOT_COMMANDS_PATH', null);
    $commandPath = $commandPath ? __DIR__.$commandPath : '';

    $dbHost = env('MYSQL_HOST', '');
    $dbUser = env('MYSQL_USERNAME', '');
    $dbPass = env('MYSQL_PASSWORD', '');
    $dbName = env('MYSQL_DATABASE', '');

    $hookAllowedUpdates = [
        Update::TYPE_MESSAGE,
        Update::TYPE_EDITED_MESSAGE,
        Update::TYPE_CALLBACK_QUERY,
        Update::TYPE_INLINE_QUERY,
        Update::TYPE_CHAT_MEMBER,
    ];

    return [
        // Add you bot's API key and name
        'api_key'      => $botToken,
        'bot_username' => $botUsername, // Without "@"
    
        // When using the getUpdates method, this can be commented out
        'webhook'      => [
            'url' => "$hookUrl?k=$hookKey",
            // Use self-signed certificate
            // 'certificate'     => __DIR__ . '/path/to/your/certificate.crt',
            // Limit maximum number of connections
            // 'max_connections' => 5,
            'allowed_updates' => $hookAllowedUpdates,
        ],
    
        // All command related configs go here
        'commands'     => [
            // Define all paths for your custom commands
            // DO NOT PUT THE COMMAND FOLDER THERE. IT WILL NOT WORK. 
            // Copy each needed Commandfile into the CustomCommand folder and uncommend the Line 49 below
            'paths'   => [
                $commandPath,
            ],
            // Here you can set any command-specific parameters
            'configs' => [
                // - Google geocode/timezone API key for /date command (see DateCommand.php)
                // 'date'    => ['google_api_key' => 'your_google_api_key_here'],
                // - OpenWeatherMap.org API key for /weather command (see WeatherCommand.php)
                // 'weather' => ['owm_api_key' => 'your_owm_api_key_here'],
                // - Payment Provider Token for /payment command (see Payments/PaymentCommand.php)
                // 'payment' => ['payment_provider_token' => 'your_payment_provider_token_here'],
            ],
        ],
    
        // Define all IDs of admin users
        'admins'       => [
            // 123,
        ],
    
        // Enter your MySQL database credentials
        'mysql'        => [
            'host'     => $dbHost,
            'user'     => $dbUser,
            'password' => $dbPass,
            'database' => $dbName,
        ],
    
        // Set custom Upload and Download paths
        // 'paths'        => [
        //     'download' => __DIR__ . '/Download',
        //     'upload'   => __DIR__ . '/Upload',
        // ],
    
        // Requests Limiter (tries to prevent reaching Telegram API limits)
        'limiter'      => [
            'enabled' => true,
        ],
    
        // ================== UPDATED ==================
    
        'validate_request' => false
    ];

}