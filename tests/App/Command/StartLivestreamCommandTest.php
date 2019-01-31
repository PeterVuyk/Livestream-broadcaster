<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StartLivestreamCommand;
use App\Exception\CouldNotStartLivestreamException;
use App\Service\StreamProcessing\StartStreamService;
use App\Service\StreamProcessing\StatusStreamService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\StartLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Service\StreamProcessing\StartStreamService
 * @uses \App\Service\StreamProcessing\StatusStreamService
 * @uses \App\Entity\StreamSchedule
 */
class StartLivestreamCommandTest extends TestCase
{
    /** @var StartStreamService|MockObject */
    private $startStreamServiceMock;

    /** @var LoggerInterface|MockObject */
    private $logger;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->startStreamServiceMock = $this->createMock(StartStreamService::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $startLivestreamCommand = new StartLivestreamCommand(
            $this->startStreamServiceMock,
            $this->logger
        );

        $application = new Application($kernelMock);
        $application->add($startLivestreamCommand);

        $startLivestreamCommandMock = $application->find(StartLivestreamCommand::COMMAND_START_STREAM);
        $this->commandTester = new CommandTester($startLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $this->startStreamServiceMock->expects($this->atLeastOnce())->method('process');
        $this->logger->expects($this->never())->method('error');

        $this->commandTester->execute([StartLivestreamCommand::COMMAND_START_STREAM]);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteStreamFailed()
    {
        $this->startStreamServiceMock->expects($this->atLeastOnce())
            ->method('process')
            ->willThrowException(CouldNotStartLivestreamException::hostNotAvailable());
        $this->logger->expects($this->atLeastOnce())->method('error');

        $this->commandTester->execute([StartLivestreamCommand::COMMAND_START_STREAM]);
    }
}
