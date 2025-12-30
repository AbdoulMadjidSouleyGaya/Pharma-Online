<?php

namespace App\Mail;

use App\Models\SupplierMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierContact extends Mailable
{
    use Queueable, SerializesModels;

    public $msg;

    public function __construct(SupplierMessage $msg)
    {
        $this->msg = $msg;
    }

    public function build()
    {
        return $this->subject($this->msg->subject)
            ->view('emails.supplier_contact');
    }
}
