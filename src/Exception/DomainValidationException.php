<?php declare(strict_types = 1);

namespace App\Exception;

class DomainValidationException extends ApplicationException
{
    /**
     * @var string $translationName
     */
    protected $translationName = '';

    public static function createException(string $translation, string $exceptionMessage = '', \Throwable $previous = null, int $code = 0)
    {
        $exception = new static($exceptionMessage, $code, $previous);
        $exception->translationName = $translation;

        return $exception;
    }

    /**
     * @return string
     */
    public function getTranslationName(): string
    {
        return $this->translationName;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return 400;
    }

    /**
     * @return string
     */
    public function getFieldName(): string
    {
        return '';
    }
}
