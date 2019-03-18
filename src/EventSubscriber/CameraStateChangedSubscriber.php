<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\Messaging\PublishMessageFailedException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Messaging\Library\Event\CameraStateChangedEvent;
use App\Repository\ChannelRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class CameraStateChangedSubscriber implements EventSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var ChannelRepository */
    private $channelRepository;

    /**
     * FailureStateSubscriber constructor.
     * @param LoggerInterface $logger
     * @param MessagingDispatcher $messagingDispatcher
     * @param ChannelRepository $channelRepository
     */
    public function __construct(
        LoggerInterface $logger,
        MessagingDispatcher $messagingDispatcher,
        ChannelRepository $channelRepository
    ) {
        $this->logger = $logger;
        $this->messagingDispatcher = $messagingDispatcher;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @param Event $event
     */
    public function sendCameraStateChangedEvent(Event $event): void
    {
        $previousState = $event->getTransition()->getFroms();
        $newState = $event->getTransition()->getTos();
        $channel = $this->channelRepository->getChannel();

        $cameraStateChangedEvent = CameraStateChangedEvent::create(
            current($previousState),
            current($newState),
            $channel->getName()
        );

        try {
            $this->messagingDispatcher->sendMessage($cameraStateChangedEvent);
        } catch (PublishMessageFailedException $exception) {
            $this->logger->error($exception->getMessage(), ['exception' => $exception]);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return ['workflow.camera_stream.entered' => 'sendCameraStateChangedEvent'];
    }
}
