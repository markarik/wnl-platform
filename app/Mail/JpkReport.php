<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JpkReport extends Mailable
{
    use Queueable, SerializesModels;

    private $filename;
    private $users;

    /**
     * Create a new message instance.
     *
     * @param string $filename
     * @param User[] $users
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
        $fileContents = \Storage::drive()->get('exports/' . $this->filename);
        $lastMonth = Carbon::now()->subMonth();

        $message = $this
            ->view('mail.jpk')
            ->subject("Bethink - JPK - {$lastMonth->format('m')}/{$lastMonth->format('Y')}")
            ->attachData($fileContents, $this->filename, [
                'mime' => 'text/xml',
            ]);

        foreach ($this->users as $user) {
            $message->cc($user->email);
        }

        return $message;
    }
}
