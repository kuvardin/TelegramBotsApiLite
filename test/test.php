<?php

declare(strict_types=1);

require 'vendor/autoload.php';

$bot_token = $argv[1];
$chat_id = $argv[2];
$message_text = date('Y.m.d H:i:s');

$guzzle_http_client = new GuzzleHttp\Client();

$lite_bot = new Kuvardin\TelegramBotsApiLite\LiteBot($guzzle_http_client, $bot_token);

try {
    $message = $lite_bot->request('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $message_text,
    ]);

    print_r($message);
} catch (Kuvardin\TelegramBotsApiLite\Exceptions\LiteApiException $lite_api_exception) {
    echo "Lite API exception #{$lite_api_exception->getCode()}: {$lite_api_exception->getMessage()}\n";
}

