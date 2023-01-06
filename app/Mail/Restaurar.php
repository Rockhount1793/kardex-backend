<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Restaurar extends Mailable
{
    use Queueable, SerializesModels;

    protected $contenido;

    public function __construct($contenido)
    {
        $this->contenido = $contenido;
    }

    public function build()
    {
        //[0] Vista
        //[1] Asunto
        //[2] Destino
        //[3..] Datos
 
        return $this->subject($this->contenido[1])
        ->with(['contenido'=>$this->contenido])
        ->view($this->contenido[0]);

    }
}
