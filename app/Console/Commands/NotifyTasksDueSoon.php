<?php

//Realizzato da: Andrea Amodeo

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Notifications\TaskDueSoon;
use Carbon\Carbon;

class NotifyTasksDueSoon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:notify-due-soon';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invia notifiche per task in scadenza nei prossimi 2 giorni';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(2);

        $tasks = Task::whereBetween('due_date', [$today, $limit])
            ->where('status', '!=', 'completed')
            ->with('assignee')
            ->get();

        foreach ($tasks as $task) {
            if ($task->assignee) {
                $task->assignee->notify(new TaskDueSoon($task));
            }
        }

        $this->info('Notifiche task in scadenza inviate.');
    }
}
