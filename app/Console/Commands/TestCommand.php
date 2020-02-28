<?php

namespace App\Console\Commands;

use App\Models\Events\Event;
use App\Models\Flight;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $events = Event::with('flight')
            ->join(
                'event_responsible_departments',
                'events.id',
                '=',
                'event_responsible_departments.event_id'
            )
            ->join(
                'flights',
                'events.flight_id',
                '=',
                'flights.id'
            )
            ->where('flights.captain', 'Иванов Иван Иванович')
            ->where('event_responsible_departments.department_id', 2)
            ->select('events.id')
            ->get();
        /** @var Event $event */
        foreach ($events as $event) {
            $this->info($event->id);
        }

//        $events = Event::with('responsible_departments')
//            ->join(
//                'event_responsible_departments',
//                'events.id',
//                '=',
//                'event_responsible_departments.event_id'
//            )->whereIn('event_responsible_departments.department_id', [1,2,3,4])->get();
//
//        /** @var Event $event */
//        foreach ($events as $event) {
//            $this->info($event->id);
//        }
    }
}
