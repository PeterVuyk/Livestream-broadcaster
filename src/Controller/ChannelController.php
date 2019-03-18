<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\Repository\CouldNotModifyChannelException;
use App\Form\EditChannelType;
use App\Repository\ChannelRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChannelController extends Controller
{
    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var ChannelRepository */
    private $channelRepository;

    /**
     * UserManagementController constructor.
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     * @param ChannelRepository $channelRepository
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        ChannelRepository $channelRepository
    ) {
        parent::__construct($twig, $tokenStorage);
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->channelRepository = $channelRepository;
    }

    /**
     * @return Response
     */
    public function channelList()
    {
        return $this->render('channel/list.html.twig', ['channel' => $this->channelRepository->getChannel()]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editChannel(Request $request)
    {
        $form = $this->formFactory->create(EditChannelType::class, $this->channelRepository->getChannel());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->channelRepository->save($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'Channel name successfully updated');
            } catch (CouldNotModifyChannelException $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'Could not modify channel name');
            }
            return new RedirectResponse($this->router->generate('channel_list'));
        }
        return $this->render('channel/edit.html.twig', ['form' => $form->createView()]);
    }
}
