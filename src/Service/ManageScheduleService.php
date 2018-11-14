<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\StreamSchedule;
use App\Repository\StreamScheduleRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class ManageScheduleService
{
    /** @var StreamScheduleRepository */
    private $streamScheduleRepository;

    /**
     * @param StreamScheduleRepository $streamScheduleRepository
     */
    public function __construct(StreamScheduleRepository $streamScheduleRepository)
    {
        $this->streamScheduleRepository = $streamScheduleRepository;
    }

    /**
     * @return StreamSchedule[]
     */
    public function getRecurringSchedules(): array
    {
        return $this->streamScheduleRepository->getRecurringScheduledItems();
    }

    /**
     * @return StreamSchedule[]
     */
    public function getOnetimeSchedules(): array
    {
        return $this->streamScheduleRepository->getOnetimeScheduledItems();
    }

    /**
     * @param string $id
     * @return StreamSchedule|null
     */
    public function getScheduleById(string $id): ?StreamSchedule
    {
        return $this->streamScheduleRepository->getScheduledItem($id);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function toggleDisablingSchedule(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setDisabled(!$streamSchedule->getDisabled());
        $this->saveSchedule($streamSchedule);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function executeScheduleWithNextExecution(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setRunWithNextExecution(true);
        $this->saveSchedule($streamSchedule);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function unwreckSchedule(StreamSchedule $streamSchedule): void
    {
        $streamSchedule->setWrecked(false);
        $this->saveSchedule($streamSchedule);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     */
    public function removeSchedule(StreamSchedule $streamSchedule): void
    {
        $this->streamScheduleRepository->remove($streamSchedule);
    }

    /**
     * @param StreamSchedule $streamSchedule
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveSchedule(StreamSchedule $streamSchedule): void
    {
        $this->streamScheduleRepository->save($streamSchedule);
    }
}
