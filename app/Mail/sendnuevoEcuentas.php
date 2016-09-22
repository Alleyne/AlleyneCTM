<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class sendnuevoEcuentas extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public $datos;

    public function __construct($data, $imps, $recs)
    {
        $this->data = $data;
        $this->imps = $imps;
        $this->recs = $recs;          
   }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('gabarriosb@hotmail.com', 'ctmaster.net')
                    ->subject('Nuevo estado de cuentas')
                    ->view('contabilidad.ctdasms.emailEcuenta')
                    ->with([
                        'data' => $this->data,
                        'imps' => $this->imps,
                        'recs' => $this->recs
                    ]);
    }
}