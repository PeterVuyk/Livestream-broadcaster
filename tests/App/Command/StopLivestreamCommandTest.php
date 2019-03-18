<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\StopLivestreamCommand;
use App\Entity\Camera;
use App\Exception\Livestream\CouldNotStopLivestreamException;
use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \App\Command\StopLivestreamCommand
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Entity\Camera
 */
class StopLivestreamCommandTest extends TestCase
{
    /** @var LivestreamService|MockObject */
    private $livestreamService;

    /** @var StreamStateMachine|MockObject */
    private $streamStateMachine;

    /** @var StopLivestream|MockObject */
    private $stopLivestream;

    /** @var CommandTester */
    private $commandTester;

    public function setUp()
    {
        $this->livestreamService= $this->createMock(LivestreamService::class);
        $this->streamStateMachine = $this->createMock(StreamStateMachine::class);
        $this->stopLivestream = $this->createMock(StopLivestream::class);

        $containerMock = $this->createMock(ContainerInterface::class);
        $kernelMock = $this->createMock(KernelInterface::class);
        $kernelMock->expects($this->any())->method('getEnvironment')->willReturn('phpunit');
        $kernelMock->expects($this->any())->method('getBundles')->willReturn([]);
        $kernelMock->expects($this->any())->method('getContainer')->willReturn($containerMock);

        $stopLivestreamCommand = new StopLivestreamCommand(
            $this->livestreamService,
            $this->streamStateMachine,
            $this->stopLivestream
        );

        $application = new Application($kernelMock);
        $application->add($stopLivestreamCommand);

        $stopLivestreamCommandMock = $application->find(StopLivestreamCommand::COMMAND_STOP_LIVESTREAM);
        $this->commandTester = new CommandTester($stopLivestreamCommandMock);
    }

    /**
     * @covers ::execute
     */
    public function testExecuteSuccess()
    {
        $camera = new Camera();
        $camera->setState('running');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->stopLivestream->expects($this->once())->method('process');

        $this->commandTester->execute(
            ['command' => StopLivestreamCommand::COMMAND_STOP_LIVESTREAM]
        );
    }

    /**
     * @covers ::execute
     */
    public function testExecuteDispatchFailed()
    {
        $camera = new Camera();
        $camera->setState('running');
        $this->livestreamService->expects($this->once())->method('getMainCameraStatus')->willReturn($camera);
        $this->streamStateMachine->expects($this->once())->method('can')->willReturn(true);
        $this->stopLivestream->expects($this->once())
            ->method('process')
            ->willThrowException(CouldNotStopLivestreamException::runProcessFailed(''));

        $this->commandTester->execute(
            ['command' => StopLivestreamCommand::COMMAND_STOP_LIVESTREAM]
        );
    }
}
