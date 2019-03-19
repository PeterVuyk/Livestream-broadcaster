<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\Messaging\InvalidMessageTypeException;
use App\Messaging\Library\Command\StartLivestreamCommand;
use App\Messaging\Library\Command\StopLivestreamCommand;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Messaging\Library\MessageInterface;
use App\Repository\ChannelRepository;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StopLivestream;
use Psr\Log\LoggerInterface;

class ProcessMessageDelegator
{
    /** @var StartLivestream */
    private $startLivestream;

    /** @var StopLivestream */
    private $stopLivestream;

    /** @var LoggerInterface */
    private $logger;

    /** @var ChannelRepository */
    private $channelRepository;

    /**
     * processMessageDelegator constructor.
     * @param StartLivestream $startLivestream
     * @param StopLivestream $stopLivestream
     * @param LoggerInterface $logger
     * @param ChannelRepository $channelRepository
     */
    public function __construct(
        StartLivestream $startLivestream,
        StopLivestream $stopLivestream,
        LoggerInterface $logger,
        ChannelRepository $channelRepository
    ) {
        $this->startLivestream = $startLivestream;
        $this->stopLivestream = $stopLivestream;
        $this->logger = $logger;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @param MessageInterface $message
     * @throws InvalidMessageTypeException
     */
    public function process(MessageInterface $message): void
    {
        $channelName = $this->channelRepository->getChannel()->getName();
        if ($message->getChannel() !== $channelName) {
            return;
        }

        switch (true) {
            case $message instanceof StartLivestreamCommand:
                $processor = $this->startLivestream;
                break;
            case $message instanceof StopLivestreamCommand:
                $processor = $this->stopLivestream;
                break;
            case $message instanceof CameraStateChangedEvent:
                return;
            default:
                throw InvalidMessageTypeException::forMessage($message);
        }
        try {
            $processor->process($message);
        } catch (\Exception $exception) {
            $this->logger->error('Could not process message', ['exception' => $exception]);
        }
    }
}
