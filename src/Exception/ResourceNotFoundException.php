<?php declare(strict_types = 1);

namespace App\Exception;

class ResourceNotFoundException extends ApplicationException
{
    const NO_RESOURCE_AVAILABLE = 2;

    public static function create()
    {
        return new self('Resource not found', self::NO_RESOURCE_AVAILABLE);
    }
}
