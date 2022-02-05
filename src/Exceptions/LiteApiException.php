<?php

declare(strict_types=1);

namespace Kuvardin\TelegramBotsApiLite\Exceptions;

use Exception;
use Throwable;

/**
 * Class LiteApiException
 *
 * @author Maxim Kuvardin <maxim@kuvard.in>
 */
class LiteApiException extends Exception
{
    /**
     * @var array
     */
    protected array $response_parameters;

    /**
     * @param int $code
     * @param string $message
     * @param array $response_parameters
     * @param Throwable|null $previous
     */
    public function __construct(int $code, string $message, array $response_parameters = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response_parameters = $response_parameters;
    }

    /**
     * @return bool
     */
    public function isMigrated(): bool
    {
        return $this->getMigratedToChatId() !== null;
    }

    /**
     * @return int|null
     */
    public function getMigratedToChatId(): ?int
    {
        return $this->response_parameters['migrate_to_chat_id'] ?? null;
    }

    /**
     * @return bool
     */
    public function isAntiflood(): bool
    {
        return $this->retryAfter() !== 0;
    }

    /**
     * @return int
     */
    public function retryAfter(): int
    {
        return $this->response_parameters['retry_after'] ?? 0;
    }

    /**
     * @return array
     */
    public function getResponseParameters(): array
    {
        return $this->response_parameters;
    }
}