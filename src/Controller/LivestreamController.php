<?php
declare(strict_types=1);

namespace App\Controller;

use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Repository\CameraRepository;
use App\Service\StreamProcessing\StreamStateMachine;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LivestreamController extends Controller
{
    /** @var MessagingDispatcher */
    private $messagingDispatcher;

    /** @var CameraRepository */
    private $cameraRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /**
     * LivestreamController constructor.
     * @param MessagingDispatcher $messagingDispatcher
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param CameraRepository $cameraRepository
     * @param LoggerInterface $logger
     * @param FlashBagInterface $flashBag
     * @param StreamStateMachine $streamStateMachine
     */
    public function __construct(
        MessagingDispatcher $messagingDispatcher,
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        CameraRepository $cameraRepository,
        LoggerInterface $logger,
        FlashBagInterface $flashBag,
        StreamStateMachine $streamStateMachine
    ) {
        parent::__construct($twig, $tokenStorage);
        $this->messagingDispatcher = $messagingDispatcher;
        $this->cameraRepository = $cameraRepository;
        $this->logger = $logger;
        $this->flashBag = $flashBag;
        $this->streamStateMachine = $streamStateMachine;
    }

    /**
     * @return Response
     */
    public function statusStream()
    {
        $camera = $this->cameraRepository->getMainCamera();
        return $this->render('components/livestream.html.twig', ['camera' => $camera]);
    }
}
