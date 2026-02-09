<?php

//Modificato da: Andrea Amodeo

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use Carbon\Carbon;
use App\Notifications\TaskDueSoon;

class CheckOverdueTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users about tasks that are due soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tasks = Task::where('due_date', '<=', Carbon::now()->addDays(2))
            ->where('status', '!=', 'completed')
            ->with('assignee')
            ->get();

        foreach ($tasks as $task) {
            if ($task->assignee) {
                $task->assignee->notify(new TaskDueSoon($task));
            }
        }

        $this->info('Task due notifications sent.');

        return Command::SUCCESS;
    }
}
