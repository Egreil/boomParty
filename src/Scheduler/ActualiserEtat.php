<?php

namespace App\Scheduler;

use App\Scheduler\Handler\Historiser;
use App\Service\HistoriserHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
final class ActualiserEtat implements ScheduleProviderInterface
{
    public function __construct(private readonly EntityManagerInterface $em) {
    }

    public function getSchedule(): Schedule
    {


        //Option: ajouter une tache au scheduler Methode de base
        //RecurringMessage::cron('*/1 */1 * * *',new Historiser($this->em),new \DateTimeZone('Europe/Paris'));
        $schedule=new Schedule();
        $historiser=new Historiser($this->em);
        //$schedule->add( RecurringMessage::cron('*/1 */1 * * *',new Historiser($this->em),new \DateTimeZone('Europe/Paris')));



        //Option Run
        /*
        $schedule = new Schedule();
        //$schedule->
        $scheduler = new Scheduler(handlers: [
            Historiser::class => new HistoriserHandler(),
            // add more handlers if you have more message types
        ], schedules: [
            $schedule,
            // the scheduler can take as many schedules as you need
        ]);
        $scheduler->run();*/
        return $schedule;


//
//        );
        //$schedule->add(RecurringMessage::cron('0 0 * * *',new Historiser()));
        //$schedule->add(RecurringMessage::cron('*/1 */1 * * *',(new Historiser())(),new \DateTimeZone('Europe/Paris')));

    }
}



