<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Camera;
use App\Exception\Repository\CouldNotFindMainCameraException;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Repository\CameraRepository;

class LivestreamService
{
    /** @var StateMachineInterface */
    private $streamStateMachine;

    /** @var CameraRepository */
    private $cameraRepository;

    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /**
     * @param StateMachineInterface $streamStateMachine
     * @param CameraRepository $cameraRepository
     * @param MessagingDispatcher $messagingDispatcher
     */
    public function __construct(
        StateMachineInterface $streamStateMachine,
        CameraRepository $cameraRepository,
        MessagingDispatcher $messagingDispatcher
    ) {
        $this->streamStateMachine = $streamStateMachine;
        $this->cameraRepository = $cameraRepository;
        $this->messagingDispatcher = $messagingDispatcher;
    }

    /**
     * @return Camera
     * @throws CouldNotFindMainCameraException
     */
    public function getMainCameraStatus(): Camera
    {
        $camera = $this->cameraRepository->getMainCamera();
        if (!$camera instanceof Camera) {
            throw CouldNotFindMainCameraException::fromRepository();
        }
        return $camera;
    }
}
