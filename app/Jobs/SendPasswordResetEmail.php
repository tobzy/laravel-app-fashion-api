<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;

class SendPasswordResetEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user;
    /**
     * @var array
     */
    protected $content;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param array $content
     */
    public function __construct(User $user, array $content)
    {
        $this->user = $user;
        $this->content = $content;
    }

    /**
     * Execute the job.
     *
     * @param Mailer $mailer
     */
    public function handle(Mailer $mailer)
    {
        $mailer->send('emails.password_reset',$this->content, function (Message $message) {
            $message->to($this->user->email)->subject('Password Reset');
        });
    }
}
