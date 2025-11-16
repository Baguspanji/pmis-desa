<?php

namespace App\Notifications;

use App\Models\TaskLogbook;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LogbookCreated extends Notification
{
    use Queueable;

    public function __construct(
        public TaskLogbook $logbook
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Logbook Baru: '.$this->logbook->title)
            ->line('Logbook baru telah ditambahkan untuk task: '.$this->logbook->task->task_name)
            ->action('Lihat Logbook', url('/projects/'.$this->logbook->task->program_id.'/tasks/'.$this->logbook->task_id.'/targets/'.$this->logbook->task_target_id))
            ->line('Silakan review dan verifikasi logbook tersebut.');
    }

    public function toArray($notifiable): array
    {
        return [
            'logbook_id' => $this->logbook->id,
            'task_id' => $this->logbook->task_id,
            'title' => $this->logbook->title,
            'type' => 'logbook_created',
        ];
    }
}
