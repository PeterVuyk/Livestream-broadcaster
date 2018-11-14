<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ScheduleLog;
use App\Entity\StreamSchedule;
use PHPUnit\Framework\TestCase;

class StreamScheduleTest extends TestCase
{
    public function testId()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setId('id-4');
        $this->assertSame('id-4', $streamSchedule->getId());
    }

    public function testName()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setName('some-name');
        $this->assertSame('some-name', $streamSchedule->getName());
    }

    public function testCommand()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setCommand('command:name');
        $this->assertSame('command:name', $streamSchedule->getCommand());
    }

    /**
     * @throws \Exception
     */
    public function testLastExecution()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setLastExecution(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $streamSchedule->getLastExecution());
    }

    public function testPriority()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setPriority(50);
        $this->assertSame(50, $streamSchedule->getPriority());
    }

    public function testRunWithNextExecution()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setRunWithNextExecution(true);
        $this->assertSame(true, $streamSchedule->getRunWithNextExecution());
    }

    public function testDisabled()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setDisabled(false);
        $this->assertSame(false, $streamSchedule->getDisabled());
    }

    public function testWrecked()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setWrecked(false);
        $this->assertSame(false, $streamSchedule->isWrecked());
    }

    public function testExecutionTime()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $streamSchedule->getExecutionTime());
    }

    public function testExecutionDay()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionDay('monday');
        $this->assertSame('monday', $streamSchedule->getExecutionDay());
    }

    public function testGetNextExecutionTime()
    {
        $streamSchedule = new StreamSchedule();
        $streamSchedule->setExecutionTime(new \DateTime());
        $streamSchedule->setExecutionDay('monday');
        $this->assertInstanceOf(\DateTime::class, $streamSchedule->getNextExecutionTime());
    }

    /**
     * @throws \Exception
     */
    public function testScheduleLog()
    {
        $scheduleLog = new ScheduleLog(new StreamSchedule(), true, 'message');
        $streamSchedule = new StreamSchedule();
        $streamSchedule->addScheduleLog($scheduleLog);
        $this->assertInstanceOf(ScheduleLog::class, $streamSchedule->getScheduleLog()[0]);
    }

    /**
     * @dataProvider streamTobeExecutedProvider
     * @param StreamSchedule $streamSchedule
     * @param bool $result
     */
    public function testStreamTobeExecuted(StreamSchedule $streamSchedule, bool $result)
    {
        $this->assertSame($result, $streamSchedule->streamTobeExecuted());
    }

    public function streamTobeExecutedProvider()
    {
        $streamScheduleWrecked = new StreamSchedule();
        $streamScheduleWrecked->setWrecked(true);
        $streamScheduleRunWithNextExecution = new StreamSchedule();
        $streamScheduleRunWithNextExecution->setRunWithNextExecution(true);
        $streamScheduleNextExecution = new StreamSchedule();
        $streamScheduleNextExecution->setExecutionTime(new \DateTime('- 1 minute'));
        $streamScheduleNoExecution = new StreamSchedule();
        $streamScheduleNoExecution->setExecutionTime(new \DateTime('+ 1 minute'));
        $streamScheduleNoExecution->setExecutionDay(date('l'));
        $streamScheduleAlreadyExecuted = new StreamSchedule();
        $streamScheduleAlreadyExecuted->setLastExecution(new \DateTime());
        $streamScheduleAlreadyExecuted->setExecutionDay(date('l'));

        return [
            [
                'streamSchedule' => $streamScheduleWrecked,
                'result' => false,
            ], [
                'streamSchedule' => $streamScheduleRunWithNextExecution,
                'result' => true,
            ], [
                'streamSchedule' => $streamScheduleAlreadyExecuted,
                'result' => false,
            ], [
                'streamSchedule' => $streamScheduleNextExecution,
                'result' => true,
            ], [
                'streamSchedule' => $streamScheduleNoExecution,
                'result' => false,
            ]
        ];
    }
}
