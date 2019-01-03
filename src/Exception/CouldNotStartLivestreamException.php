<?php
declare(strict_types=1);

namespace App\Exception;

class CouldNotStartLivestreamException extends \Exception
{
    const HOST_NOT_AVAILABLE_MESSAGE = 'Unable to start stream because host is not available, aborted';
    const COULD_NOT_CREATE_DIRS_MESSAGE = 'Could not create the required directories for the camera, aborted';
    const COULD_NOT_CREATE_SYMLINK_MESSAGE = 'Could not create the required symlink for the camera, aborted';

    public function __construct(string $reason, \Throwable $previous = null)
    {
        parent::__construct($reason, 0, $previous);
    }

    public static function hostNotAvailable()
    {
        return new self(self::HOST_NOT_AVAILABLE_MESSAGE);
    }
}
