<?php declare(strict_types = 1);

namespace App\Exception;

use App\Entity\LibraryElement;

class InvalidLibraryNameException extends DomainValidationException
{
    const INVALID_LIBRARY_ID_FORMAT = 1100;
    const INVALID_LIBRARY_ID_LENGTH = 1101;

    public static function createInvalidFormatError(string $suggestion = '')
    {
        return self::createException(
            'Invalid library name, please use url friendly syntax such as [a-z] characters, numbers and "-" as separators. ' .
            ($suggestion ? 'Suggestion: ' . $suggestion : ''),
            'INVALID_LIBRARY_ID_FORMAT, it\'s not url friendly',
            null,
            self::INVALID_LIBRARY_ID_FORMAT
        );
    }

    public static function createInvalidLengthError()
    {
        return self::createException(
            'Invalid library name length, should be between ' . LibraryElement::ID_MIN_LENGTH . ' and ' . LibraryElement::ID_MAX_LENGTH . ' characters length',
            'INVALID_LIBRARY_ID_LENGTH',
            null,
            self::INVALID_LIBRARY_ID_LENGTH
        );
    }

    public function getFieldName(): string
    {
        return 'name';
    }
}
