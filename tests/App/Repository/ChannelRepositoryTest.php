<?php
declare(strict_types=1);

namespace App\Tests\App\Repository;

use App\Entity\Channel;
use App\Exception\Repository\CouldNotModifyChannelException;
use App\Repository\ChannelRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @coversDefaultClass \App\Repository\ChannelRepository
 * @covers ::<!public>
 * @covers ::__construct()
 * @uses \App\Entity\Channel
 */
class ChannelRepositoryTest extends TestCase
{
    /** @var ChannelRepository */
    private $channelRepository;

    /** @var MockObject|EntityManager */
    private $entityManager;

    public function setUp()
    {
        $classMetaData = $this->createMock(ClassMetadata::class);
        $this->entityManager = $this->createMock(EntityManager::class);
        $this->entityManager->expects($this->once())->method('getClassMetadata')->willReturn($classMetaData);
        $registryInterface = $this->createMock(RegistryInterface::class);
        $registryInterface->expects($this->once())->method('getManagerForClass')->willReturn($this->entityManager);
        $this->channelRepository = new ChannelRepository($registryInterface);
    }

    /**
     * @throws CouldNotModifyChannelException
     * @covers ::save
     */
    public function testSave()
    {
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');
        $this->channelRepository->save(new Channel());

        $this->addToAssertionCount(1);
    }

    /**
     * @throws CouldNotModifyChannelException
     * @covers ::save
     */
    public function testSaveFailed()
    {
        $this->expectException(CouldNotModifyChannelException::class);

        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush')->willThrowException(new ORMException());
        $this->channelRepository->save(new Channel());
    }

    /**
     * @covers ::getChannel
     */
    public function testGetChannel()
    {
        $this->loadInvoked([new Channel()]);
        $this->assertInstanceOf(Channel::class, $this->channelRepository->getChannel());
    }

    private function loadInvoked($returnedValue)
    {
        $method = (is_array($returnedValue)) ? 'loadAll': 'load';
        $entityPersisterMock = $this->createMock(EntityPersister::class);
        $entityPersisterMock->expects($this->once())->method($method)->willReturn($returnedValue);
        $unitOfWorkMock = $this->createMock(UnitOfWork::class);
        $unitOfWorkMock->expects($this->once())->method('getEntityPersister')->willReturn($entityPersisterMock);
        $this->entityManager->expects($this->once())->method('getUnitOfWork')->willReturn($unitOfWorkMock);
    }
}
