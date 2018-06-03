<?php declare(strict_types = 1);

namespace App\Exception;

class InvalidUrlException extends DomainValidationException
{
    const NO_HANDLER_AVAILABLE = 990;

    public static function create()
    {
        return self::createException(
            'Invalid URL address, no any handler is able to handle it',
            'No handler available',
            null,
            self::NO_HANDLER_AVAILABLE
        );
    }

    public function getFieldName(): string
    {
        return 'url';
    }
}
