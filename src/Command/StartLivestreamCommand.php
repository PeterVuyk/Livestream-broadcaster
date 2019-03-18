<?php
declare(strict_types=1);

namespace App\Command;

use App\Exception\Repository\CouldNotFindMainCameraException;
use App\Service\LivestreamService;
use App\Service\StreamProcessing\StartLivestream;
use App\Service\StreamProcessing\StreamStateMachine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartLivestreamCommand extends Command
{
    const COMMAND_START_LIVESTREAM = 'app:livestream-start';

    /** @var LivestreamService */
    private $livestreamService;

    /** @var StreamStateMachine */
    private $streamStateMachine;

    /** @var StartLivestream */
    private $startLivestream;

    /**
     * @param LivestreamService $livestreamService
     * @param StreamStateMachine $streamStateMachine
     * @param StartLivestream $startLivestream
     */
    public function __construct(
        LivestreamService $livestreamService,
        StreamStateMachine $streamStateMachine,
        StartLivestream $startLivestream
    ) {
        parent::__construct();
        $this->livestreamService = $livestreamService;
        $this->streamStateMachine = $streamStateMachine;
        $this->startLivestream = $startLivestream;
    }

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_START_LIVESTREAM)
            ->setDescription('Start the livestream.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws CouldNotFindMainCameraException
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('start livestream...');

        $camera = $this->livestreamService->getMainCameraStatus();
        $toStarting = $this->streamStateMachine->can($camera, 'to_starting');

        if (!$toStarting) {
            $message = "tried to start livestream while this is not possible, current state: {$camera->getState()}";
            $output->writeln("<error>{$message}</error>");
            return;
        }

        try {
            $this->startLivestream->process();
        } catch (\Exception $exception) {
            $output->writeln("<error>Could not start livestream, Exception: {$exception->getMessage()}</error>");
        }
    }
}
