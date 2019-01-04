<?php
declare(strict_types=1);

namespace App\Exception;

use Doctrine\ORM\ORMException;

class CouldNotModifyStreamScheduleException extends \Exception
{
    public static function forError(ORMException $error)
    {
        return new self('Database by ORM not available', 0, $error);
    }
}
