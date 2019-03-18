<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\LivestreamController;
use App\Entity\Camera;
use App\Messaging\Dispatcher\MessagingDispatcher;
use App\Repository\CameraRepository;
use App\Service\StreamProcessing\StreamStateMachine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @coversDefaultClass \App\Controller\LivestreamController
 * @covers ::<!public>
 * @covers ::__construct
 * @uses \App\Controller\Controller
 */
class LivestreamControllerTest extends TestCase
{
    /** @var MessagingDispatcher|MockObject */
    private $messagingDispatcher;

    /** @var CameraRepository|MockObject */
    private $cameraRepositoryMock;

    /** @var LoggerInterface|MockObject */
    private $loggerMock;

    /** @var FlashBagInterface|MockObject */
    private $flashBagMock;

    /** @var \Twig_Environment|MockObject */
    private $twigMock;

    /** @var TokenStorageInterface|MockObject */
    private $tokenStorageMock;

    /** @var StreamStateMachine|MockObject */
    private $streamStateMachineMock;

    /** @var LivestreamController */
    private $livestreamController;

    public function setUp()
    {
        $this->messagingDispatcher = $this->createMock(MessagingDispatcher::class);
        $this->cameraRepositoryMock = $this->createMock(CameraRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->flashBagMock = $this->createMock(FlashBagInterface::class);
        $this->twigMock = $this->createMock(\Twig_Environment::class);
        $this->streamStateMachineMock = $this->createMock(StreamStateMachine::class);
        $this->tokenStorageMock = $this->createMock(TokenStorageInterface::class);
        $this->livestreamController = new LivestreamController(
            $this->messagingDispatcher,
            $this->twigMock,
            $this->tokenStorageMock,
            $this->cameraRepositoryMock,
            $this->loggerMock,
            $this->flashBagMock,
            $this->streamStateMachineMock
        );
    }

    /**
     * @covers ::statusStream
     */
    public function testStatusStream()
    {
        $this->twigMock->expects($this->once())->method('render')->willReturn('<p>hi</p>');
        $this->cameraRepositoryMock->expects($this->once())->method('getMainCamera')->willReturn(new Camera());
        $response = $this->livestreamController->statusStream();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }
}
