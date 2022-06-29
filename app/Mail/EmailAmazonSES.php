<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailAmazonSES extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build($view, $to, $nameTo, $subject, $body, $file = '')
    {
        Mail::send($view, ['body' => $body], function (Message $message) use ($body, $to, $nameTo, $subject, $file) {
            $message
                ->to($to, $nameTo)
                ->from('dhernanezm@agarcia.com.mx', 'Diego Hernandez')
                ->subject($subject);
        });
    }
}
