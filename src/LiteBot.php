<?php

declare(strict_types=1);

namespace Kuvardin\TelegramBotsApiLite;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;
use RuntimeException;

/**
 * Class LiteBot
 *
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class LiteBot
{
    public const PARSE_MODE_HTML = 'HTML';
    public const PARSE_MODE_MARKDOWN = 'Markdown';
    public const PARSE_MODE_MARKDOWN_V2 = 'MarkdownV2';
    public const PARSE_MODE_DEFAULT = self::PARSE_MODE_HTML;

    /**
     * @var Client
     */
    protected Client $guzzle_http_client;

    /**
     * @var string
     */
    protected string $token;

    /**
     * @var int
     */
    public int $connect_timeout = 7;

    /**
     * @var int
     */
    public int $request_timeout = 10;

    /**
     * @param Client $guzzle_http_client
     * @param string $token
     */
    public function __construct(Client $guzzle_http_client, string $token)
    {
        $this->guzzle_http_client = $guzzle_http_client;
        $this->token = $token;
    }

    /**
     * @param string $text
     * @param string $parse_mode
     * @return string
     */
    public static function filterString(string $text, string $parse_mode = self::PARSE_MODE_DEFAULT): string
    {
        switch ($parse_mode) {
            case self::PARSE_MODE_HTML:
                return str_replace(['<', '>', '&', '"'], ['&lt;', '&gt;', '&amp;', '&quot;'], $text);

            case self::PARSE_MODE_MARKDOWN:
                return str_replace(['_', '*', '`', '['], ['\_', '\*', '\`', '\['], $text);

            case self::PARSE_MODE_MARKDOWN_V2:
                return str_replace(
                    ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+',
                        '-', '=', '|', '{', '}', '.', '!',],
                    ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+',
                        '\-', '\=', '\|', '\{', '\}', '\.', '\!',],
                    $text);

        }

        throw new RuntimeException("Unknown parse mode: $parse_mode");
    }

    /**
     * @return Client
     */
    public function getGuzzleHttpClient(): Client
    {
        return $this->guzzle_http_client;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws Exceptions\LiteApiException
     * @throws GuzzleException
     * @throws JsonException
     */
    public function request(string $method, array $parameters = []): mixed
    {
        $uri = "https://api.telegram.org/bot{$this->token}/$method";

        $response = $this->guzzle_http_client->post($uri, [
            RequestOptions::CONNECT_TIMEOUT => $this->connect_timeout,
            RequestOptions::TIMEOUT => $this->request_timeout,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Accept-Encoding' => 'gzip',
            ],
            RequestOptions::JSON => $parameters,
        ]);


        $content = $response->getBody()->getContents();
        $content_decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if ($content_decoded['ok'] !== true) {
            throw new Exceptions\LiteApiException(
                $content_decoded['error_code'],
                $content_decoded['description'],
                $content_decoded['parameters'] ?? []
            );
        }

        return $content_decoded['result'];
    }
}