<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StartLivestreamCommand;
use App\Entity\Camera;
use App\Exception\Livestream\CouldNotStartLivestreamException;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\StartLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\Camera
 */
class StartLivestreamCommandTest extends TestCase
{
    /** @var LivestreamService|MockObject */
    private $livestreamService;

    /** @var StreamStateMachine|MockObject */
    private $streamStateMachine;

    /** @var StartLivestream|MockObject */
    private $startLivestream;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->livestreamService = $this->createMock(LivestreamService::class);
        $this->streamStateMachine = $this->createMock(StreamStateMachine::class);
        $this->startLivestream = $this->createMock(StartLivestream::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $startLivestreamCommand = new StartLivestreamCommand(
            $this->livestreamService,
            $this->streamStateMachine,
            $this->startLivestream
        );

        $application = new Application($kernelMock);
        $application->add($startLivestreamCommand);

        $startLivestreamCommandMock = $application->find(StartLivestreamCommand::COMMAND_START_LIVESTREAM);
        $this->commandTester = new CommandTester($startLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $camera = new Camera();
        $camera->setState('inactive');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->startLivestream->expects($this->once())->method('process');

        $this->commandTester->execute(
            ['command' => StartLivestreamCommand::COMMAND_START_LIVESTREAM]
        );
    }

    /**
     * @covers ::execute
     */
    public function testExecuteDispatchFailed()
    {
        $camera = new Camera();
        $camera->setState('inactive');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->startLivestream->expects($this->atLeastOnce())
            ->method('process')
            ->willThrowException(CouldNotStartLivestreamException::hostNotAvailable());

        $this->commandTester->execute(
            ['command' => StartLivestreamCommand::COMMAND_START_LIVESTREAM]
        );
    }
}
