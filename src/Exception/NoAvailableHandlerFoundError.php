<?php declare(strict_types = 1);

namespace App\Exception;

class NoAvailableHandlerFoundError extends ApplicationException
{
    const NO_HANDLER_AVAILABLE = 1;

    public static function create()
    {
        return new self('No handler available for selected URL', self::NO_HANDLER_AVAILABLE);
    }
}
