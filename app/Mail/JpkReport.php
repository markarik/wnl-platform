<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class JpkReport extends Mailable
{
    use Queueable, SerializesModels;

    private $filename;
    private $users;

    /**
     * Create a new message instance.
     *
     * @param $users
     * @param $filename
     */
    public function __construct($filename, $users)
    {
        $this->filename = $filename;
        $this->users = $users;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fileContents = \Storage::get('exports/' . $this->filename);
        $now = Carbon::now();

        $message = $this
            ->view('mail.jpk')
            ->subject("Bethink - JPK - {$now->format('m')}/{$now->format('Y')}")
            ->attachData($fileContents, $this->filename, [
                'mime' => 'text/xml',
            ]);

        foreach ($this->users as $user) {
            $message->cc($user->email);
        }

        return $message;
    }
}
