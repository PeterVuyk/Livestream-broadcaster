<?php
declare(strict_types=1);

namespace App\Exception\Repository;

class CouldNotFindMainCameraException extends \Exception
{
    public static function fromRepository()
    {
        return new self('Could not find main camera from database');
    }
}
