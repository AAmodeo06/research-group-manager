<?php

//Realizzato da: Andrea Amodeo

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDueSoon extends Notification
{
    use Queueable;

    protected Task $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
        $this->afterCommit();
    }
    
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task in scadenza')
            ->greeting('Ciao ' . $notifiable->name . ',')
            ->line('Il seguente task è in scadenza:')
            ->line('Progetto: ' . $this->task->project->title)
            ->line('Task: ' . $this->task->title)
            ->line('Scadenza: ' . $this->task->due_date)
            ->action('Visualizza Task', route('projects.tasks.show', [$this->task->project, $this->task]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_due_soon',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'project_id' => $this->task->project->id,
            'project_title' => $this->task->project->title,
            'due_date' => $this->task->due_date,
            'notified_at' => now(),
        ];
    }
}
