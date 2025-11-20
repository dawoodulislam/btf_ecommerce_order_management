<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(public string $to, public string $view, public array $data = []) {}

    public function handle()
    {
        Mail::send($this->view, $this->data, function($m){
            $m->to($this->to)->subject($this->data['subject'] ?? 'Notification');
        });
    }
}
