<?php declare(strict_types = 1);

namespace App\Exception;

class CollectorException extends ApplicationException
{
    const GENERIC_COLLECTOR_EXCEPTION = 3;

    /**
     * @param string $message
     * @param \Throwable|null $exception
     *
     * @return CollectorException
     */
    public static function createGenericException(string $message, \Throwable $exception = null)
    {
        return new self('Collector exception occurred' . ($message ? ', ' . $message : ''), self::GENERIC_COLLECTOR_EXCEPTION, $exception);
    }
}
