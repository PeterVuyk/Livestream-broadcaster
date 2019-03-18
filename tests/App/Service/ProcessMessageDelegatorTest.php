<?php
declare(strict_types=1);

namespace App\Tests\MessageProcessor;

use App\Exception\Messaging\InvalidMessageTypeException;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\MessageInterface;
use App\Service\ProcessMessageDelegator;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StopLivestream;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \App\Service\ProcessMessageDelegator
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Messaging\Library\Command\StartLivestreamCommand
 */
class ProcessMessageDelegatorTest extends TestCase
{
    /** @var ProcessMessageDelegator */
    private $processMessageDelegator;

    /** @var StartLivestream|MockObject */
    private $startLivestream;

    /** @var StopLivestream|MockObject */
    private $stopLivestream;

    /** @var LoggerInterface|MockObject */
    private $logger;


    protected function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->startLivestream = $this->createMock(StartLivestream::class);
        $this->stopLivestream = $this->createMock(StopLivestream::class);
        $this->processMessageDelegator = new ProcessMessageDelegator(
            $this->startLivestream,
            $this->stopLivestream,
            $this->logger
        );
    }

    /**
     * @covers ::process
     * @throws InvalidMessageTypeException
     */
    public function testProcessSuccess()
    {
        $this->startLivestream->expects($this->once())->method('process');
        $this->processMessageDelegator->process(StartLivestreamCommand::create('some-channel'));
    }

    /**
     * @covers ::process
     * @throws InvalidMessageTypeException
     */
    public function testProcessFailed()
    {
        $this->expectException(InvalidMessageTypeException::class);
        $this->processMessageDelegator->process($this->createMock(MessageInterface::class));
    }
}
