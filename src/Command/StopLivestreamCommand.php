<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\Repository\CouldNotFindMainCameraException;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StopLivestream;
use App\Service\StreamProcessing\StreamStateMachine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopLivestreamCommand extends Command
{
    const COMMAND_STOP_LIVESTREAM = 'app:livestream-stop';

    /** @var LivestreamService */
    private $livestreamService;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /** @var StopLivestream */
    private $stopLivestream;

    /**
     * @param LivestreamService $livestreamService
     * @param StreamStateMachine $streamStateMachine
     * @param StopLivestream $stopLivestream
     */
    public function __construct(
        LivestreamService $livestreamService,
        StreamStateMachine $streamStateMachine,
        StopLivestream $stopLivestream
    ) {
        parent::__construct();
        $this->livestreamService = $livestreamService;
        $this->streamStateMachine = $streamStateMachine;
        $this->stopLivestream = $stopLivestream;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_STOP_LIVESTREAM)
            ->setDescription('Stop the livestream.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotFindMainCameraException
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('Stop livestream request started...');

        $camera = $this->livestreamService->getMainCameraStatus();
        $toStopping = $this->streamStateMachine->can($camera, 'to_stopping');
        if (!$toStopping) {
            $message = "tried to stop livestream while this is not possible, current state: {$camera->getState()}";
            $output->writeln("<error>{$message}</error>");
            return;
        }

        try {
            $this->stopLivestream->process();
        } catch (\Exception $exception) {
            $output->writeln("<error>Could not stop livestream, exception: {$exception->getMessage()}</error>");
        }
    }
}
