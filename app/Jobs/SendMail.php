<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\Restaurar;

class SendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contenido;

    public function __construct($contenido)
    {
        $this->contenido = $contenido;
    }

    public function handle()
    {
        //[0] Vista
        //[1] Asunto
        //[2] Destino
        //[...3] Datos
        Mail::to($this->contenido[2])->send(new Restaurar($this->contenido));
        //->cc($moreUsers)
        //->bcc($evenMoreUsers)
    }
}
