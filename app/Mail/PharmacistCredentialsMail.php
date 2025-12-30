<?php

namespace App\Mail;

use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PharmacistCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public Pharmacy $pharmacy;
    public User $user;
    public string $plainPassword;
    public \Carbon\Carbon $expiresAt;

    public function __construct(Pharmacy $pharmacy, User $user, string $plainPassword, $expiresAt)
    {
        $this->pharmacy = $pharmacy;
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->expiresAt = \Carbon\Carbon::parse($expiresAt);
    }

    public function build()
    {
        return $this->subject('Accès Pharmacien - PharmaOnline')
            ->view('emails.pharmacist_credentials');
    }
}
